<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;
use app\models\userService;
use app\models\Assegnazioni;
use app\eccezioni\existUserException;
use RobThree\Auth\TwoFactorAuth;
use RobThree\Auth\Providers\Qr\BaconQrCodeProvider;
use RobThree\Auth\Algorithm;


use app\eccezioni\tentativiSuperati;
use app\models\User;
use app\models\Ticket;
use app\models\Turni;
use app\models\Mail;
use app\models\TicketMessage;
use app\models\ticketFunctions;
use app\models\ticketfunction;
use Exception;
use yii\db\Expression;

class SiteController extends Controller
{
    public function behaviors()
    {
        return [
            'access' =>
            [
                'class' => AccessControl::class,
                'only' => ['index', 'contact', 'attesa', 'account', 'modify-username', 'recovery-mail', 'reset', 'modify-iva', 'modify-image', 'modify-email', 'salta-pausa'],
                'rules' => [
                    [
                        'actions' => ['index', 'contact', 'logout', 'mail', 'recupero-password', 'attesa', 'account', 'modify-username', 'recovery-mail', 'reset', 'modify-iva', 'modify-image', 'modify-email'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [],
            ],
        ];
    }
    public function actions()
    {
        return [
            'error' => ['class' => 'yii\web\ErrorAction'],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }
    public function actionIndex()
    {
        $user = User::findOne(['id' => Yii::$app->user->id]);
        if ($user === null) {
            return $this->redirect(['login']);
        }

        $function = new ticketFunctions();
        $function->ticketScaduto();

        if (!$user->approvazione) {
            Yii::$app->session->setFlash('info', 'Attendere l\'approvazione da parte di un amministratore');
            return $this->redirect(['attesa']);
        }

        $ticketQuery = Ticket::find()->where(['id_cliente' => $user->id]);
        $ticket = $ticketQuery->all();
        $countTicket = (int)(clone $ticketQuery)->count();
        $ultimoTicket = (clone $ticketQuery)->orderBy(['data_invio' => SORT_DESC])->one();

        $assegnazioni = Assegnazioni::find()->where(['id_operatore' => $user->id])->all();

        $isOperator = !in_array($user->ruolo, ['cliente', 'amministratore'], true);
        $customerRecentTickets = [];
        $inlineTicketModel = null;
        $operatorAssignedTicketCodes = [];
        $operatorAssignedTickets = [];
        $operatorDepartmentTickets = [];
        $operatorRecentMessages = [];
        $operatorDepartment = null;

        if ($user->ruolo === 'cliente') {
            $customerRecentTickets = Ticket::find()
                ->where(['id_cliente' => $user->id])
                ->orderBy(['data_invio' => SORT_DESC, 'id' => SORT_DESC])
                ->limit(10)
                ->all();

            $inlineTicketModel = new ticketfunction();
            $inlineTicketModel->id_cliente = $user->id;
        }

        if ($isOperator) {
            $operatorAssignedTicketCodes = Assegnazioni::find()
                ->select('codice_ticket')
                ->where(['id_operatore' => $user->id])
                ->andWhere(['not', ['codice_ticket' => null]])
                ->column();

            if (!empty($operatorAssignedTicketCodes)) {
                $operatorAssignedTickets = Ticket::find()
                    ->where(['codice_ticket' => $operatorAssignedTicketCodes])
                    ->orderBy(['data_invio' => SORT_DESC, 'id' => SORT_DESC])
                    ->limit(10)
                    ->all();
            }

            $operatorDepartment = ticketFunctions::departmentFromRole($user->ruolo);
            if ($operatorDepartment !== null) {
                $departmentAliases = ticketFunctions::departmentAliases($operatorDepartment);
                $operatorDepartmentTickets = Ticket::find()
                    ->where(['in', new Expression('LOWER(reparto)'), $departmentAliases])
                    ->orderBy(['data_invio' => SORT_DESC, 'id' => SORT_DESC])
                    ->limit(10)
                    ->all();
            }
        }

        $unreadMessages = 0;
        try {
            if (Yii::$app->db->schema->getTableSchema(TicketMessage::tableName(), true) !== null) {
                $unreadMessages = (int)TicketMessage::find()
                    ->where(['recipient_id' => $user->id, 'is_read' => 0])
                    ->count();

                if ($isOperator) {
                    $operatorRecentMessages = TicketMessage::find()
                        ->with(['sender', 'ticket'])
                        ->where(['recipient_id' => $user->id, 'is_read' => 0])
                        ->orderBy(['created_at' => SORT_DESC, 'id' => SORT_DESC])
                        ->limit(10)
                        ->all();
                }
            }
        } catch (\Throwable $e) {
            $unreadMessages = 0;
            $operatorRecentMessages = [];
        }

        $dashboardStats = [
            'total' => 0,
            'open' => 0,
            'in_progress' => 0,
            'closed' => 0,
            'expired' => 0,
        ];

        if ($user->ruolo === 'amministratore') {
            $allTickets = Ticket::find();
            $dashboardStats['total'] = (int)(clone $allTickets)->count();
            $dashboardStats['open'] = (int)(clone $allTickets)->where(['stato' => 'aperto'])->count();
            $dashboardStats['in_progress'] = (int)(clone $allTickets)->where(['stato' => 'in lavorazione'])->count();
            $dashboardStats['closed'] = (int)(clone $allTickets)->where(['stato' => 'chiuso'])->count();
            $dashboardStats['expired'] = (int)(clone $allTickets)->where(['stato' => 'scaduto'])->count();
        } elseif ($user->ruolo === 'cliente') {
            $myTickets = Ticket::find()->where(['id_cliente' => $user->id]);
            $dashboardStats['total'] = (int)(clone $myTickets)->count();
            $dashboardStats['open'] = (int)(clone $myTickets)->andWhere(['stato' => 'aperto'])->count();
            $dashboardStats['in_progress'] = (int)(clone $myTickets)->andWhere(['stato' => 'in lavorazione'])->count();
            $dashboardStats['closed'] = (int)(clone $myTickets)->andWhere(['stato' => 'chiuso'])->count();
            $dashboardStats['expired'] = (int)(clone $myTickets)->andWhere(['stato' => 'scaduto'])->count();
        } else {
            if (!empty($operatorAssignedTicketCodes)) {
                $assignedTicketQuery = Ticket::find()->where(['codice_ticket' => $operatorAssignedTicketCodes]);
                $dashboardStats['total'] = (int)(clone $assignedTicketQuery)->count();
                $dashboardStats['open'] = (int)(clone $assignedTicketQuery)->andWhere(['stato' => 'aperto'])->count();
                $dashboardStats['in_progress'] = (int)(clone $assignedTicketQuery)->andWhere(['stato' => 'in lavorazione'])->count();
                $dashboardStats['closed'] = (int)(clone $assignedTicketQuery)->andWhere(['stato' => 'chiuso'])->count();
                $dashboardStats['expired'] = (int)(clone $assignedTicketQuery)->andWhere(['stato' => 'scaduto'])->count();
            }
        }

        return $this->render('index', [
            'user' => $user,
            'ticket' => $ticket,
            'countTicket' => $countTicket,
            'ultimoTicket' => $ultimoTicket,
            'assegnazioni' => $assegnazioni,
            'dashboardStats' => $dashboardStats,
            'unreadMessages' => $unreadMessages,
            'operatorAssignedTickets' => $operatorAssignedTickets,
            'operatorDepartmentTickets' => $operatorDepartmentTickets,
            'operatorRecentMessages' => $operatorRecentMessages,
            'operatorDepartment' => $operatorDepartment,
            'customerRecentTickets' => $customerRecentTickets,
            'inlineTicketModel' => $inlineTicketModel,
        ]);
    }
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }
        $model = new LoginForm();
        $function = new userService();
        $register = new User();
        if ($model->load(Yii::$app->request->post())) {
            try {
                if ($model->verifyBlocco($model->username)) {
                    throw new tentativiSuperati('Troppi tentativi: attivato blocco anti-accesso');
                }
                if ($model->login()) {
                    $user = Yii::$app->user->identity;
                    // 🔐 SE L’UTENTE HA LA 2FA ATTIVA → BLOCCO LOGIN E PASSO ALLA VERIFICA 
                    if ($user->is_totp_enabled) {
                        Yii::$app->user->logout(false);
                        Yii::$app->session->set('pending_user_id', $user->id);
                        return $this->redirect(['verify-2fa']);
                    } // 🔓 LOGIN NORMALE 
                    if (in_array($user->ruolo, ['amministratore', 'developer', 'itc', 'ict', 'sistemista'], true)) {
                        return $this->redirect(['attesa']);
                    }
                    if ($user->ruolo == 'cliente') {
                        return $this->redirect(['index']);
                    }
                } else {
                    Yii::$app->session->setFlash('error', 'Credenziali errate');
                }
                $model->password = '';
                return $this->render('login', ['model' => $model]);
            } catch (tentativiSuperati $e) {
                Yii::$app->session->setFlash('error', 'Troppi tentativi: blocco attivato');
            }
        }
        return $this->render('login', ['model' => $model, 'register' => $register,]);
    }
    public function actionVerify2fa()
    {
        $userId = Yii::$app->user->identity->id;
        if (!$userId) {
            return $this->redirect(['login']);
        }
        $user = User::findOne($userId);
        $model = new \yii\base\DynamicModel(['code']);
        $model->addRule('code', 'required');
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $provider = new BaconQrCodeProvider();
           $tfa=new TwoFactorAuth($provider, 'Dataseed', 6, 30, Algorithm::Sha1);

          if ($tfa->verifyCode($user->totp_secret, $model->code)) {

          if(!$user->is_totp_enabled)
            {
    // Attiva la 2FA definitivamente
    $user->is_totp_enabled = 1;
    $user->save();

    // Rimuove la sessione temporanea
    Yii::$app->session->remove('pending_user_id');

    // Login dell’utente
    Yii::$app->user->login($user);
    
    return $this->goHome();
    }else{
         $user->is_totp_enabled = 0;

    $user->save();

    // Rimuove la sessione temporanea
    Yii::$app->session->remove('pending_user_id');

    
    return $this->redirect(['logout']);
    }

            $model->addError('code', 'Codice non valido');
        }
        }
        return $this->render('verify-2fa', ['model' => $model]);
    
    }
    public function actionLogout()
    {
        $function = new userService();
        $function->fuoriServizio();
        Yii::$app->user->logout();
        return $this->goHome();
    }

    /*
    public function actionDisable2fa()
    {
        $function=new userService();

        if($function->disattiva2fa()){
            
            Yii::$app->session->setFlash('success','Autenticazione a due fattori disattivata correttamente');

            return $this->redirect(['index']);
        }else{
             Yii::$app->session->setFlash('error','Autenticazione a due fattori non disattivata correttamente');
            return $this->redirect(['index']);
        }
    }
*/

   public function actionEnable2fa()
{
    if (Yii::$app->user->isGuest) {
        Yii::$app->session->setFlash('error', 'Devi effettuare il login per attivare la 2FA.');
        return $this->redirect(['login']);
    }

    $user = User::findOne(Yii::$app->user->id);

    if (!$user) {
        Yii::$app->session->setFlash('error', 'Utente non trovato.');
        return $this->redirect(['login']);
    }

    // Provider QR
    $provider = new BaconQrCodeProvider();

    // TwoFactorAuth con costruttore corretto
    $tfa = new TwoFactorAuth(
        $provider,
        'NomeApp',
        6,
        30,
        Algorithm::Sha1
    );

    // Genera segreto se non esiste
    if (!$user->totp_secret) {
        $user->totp_secret = $tfa->createSecret();
      
        if($user->totp_secret)
            $user->is_totp_enabled=1;
        $user->save();
    }

    // Genera QR code
    $qrCode = $tfa->getQRCodeImageAsDataUri($user->email, $user->totp_secret);

    return $this->render('enable-2fa', [
        'user' => $user,
        'qrCode' => $qrCode,
    ]);
}




    /**
     * Displays contact page.
     *
     * @return Response|string
     */
    public function actionContact()
    {

        $model = new ContactForm();


        if ($model->load(Yii::$app->request->post()) && $model->contact()) {
            Yii::$app->session->setFlash('contactFormSubmitted');

            return $this->refresh();
        }
        return $this->render('contact', [
            'model' => $model,

        ]);
    }




    /**
     * Displays about page.
     *
     * @return string
     */


    public function actionMail()
    {
        $user = new User();
        $function = new userService();
        if ($user->load(Yii::$app->request->post())) {

            $userFind = User::findOne(['email' => $user->email]);

            if (!$userFind) {
                Yii::$app->session->setFlash('error', 'la mail inserita non coincide con nessun utente registrato');
                return $this->redirect('mail');
            }
            if ($user && $function->emailRequest($user->email)) {
                Yii::$app->session->setFlash('success', 'Controlla la tua email, ti abbiamo inviato il link di recupero');
                
                return $this->redirect('mail');
            } else {
                Yii::$app->session->setFlash('error', 'Qualcosa è andato storto nel tentativo di recupero dei dati');
                return $this->redirect('mail');
            }
        }
        return $this->render('recuperoMail', ['user' => $user]);
    }

    public function actionRecuperoPassword($token)
    {
        $user = User::findOne(['token' => $token]);
        $function = new userService();
        if (!$user) {
            Yii::$app->session->setFlash('error', 'Token non valido o scaduto.');
            return $this->redirect(['site/login']);
        }

        if (Yii::$app->request->isPost) {
            $password = Yii::$app->request->post('User')['password'];

            if ($function->modifyPassword($password, $token)) {
                Yii::$app->session->setFlash('success', 'Password modificata con successo.');
                return $this->redirect(['site/login']);
            } else {
                Yii::$app->session->setFlash('error', 'Errore durante la modifica della password.');
            }
        }

        return $this->render('modifyPassword', [
            'tokenUser' => $user
        ]);



        return $this->render('modifyPassword', ['user' => $user]);
    }

    public function actionAttesa()
    {

        $user = User::findOne(['username' => Yii::$app->user->identity->username]);
        // Ticket dell’utente
        $ticket = Ticket::find()->where(['id_cliente' => Yii::$app->user->identity->id])->all();

        // Conteggio corretto dei ticket dell’utente
        $countTicket = Ticket::find()->where(['id_cliente' => Yii::$app->user->identity->id])->count();

        //ulitmo ticket rilevato

        $ultimoTicket = Ticket::find()
            ->where(['id_cliente' => Yii::$app->user->identity->id])
            ->orderBy(['data_invio' => SORT_DESC])
            ->one();


        if (!$user->approvazione) {
            Yii::$app->session->setFlash('info', 'Si prega di attendere l\'approvazione da parte di uno degli amministratori,grazie per l\'attesa');
        } else {
            return $this->redirect(['index']);
        }
        return $this->render('approved', [
            'user' => $user,
            'ticket' => $ticket,
            'countTicket' => $countTicket,
            'ultimoTicket' => $ultimoTicket
        ]);
    }
    public function actionRegister()
    {
        $user = new User();
        $function = new userService();
        $turni = new Turni();

        if ($user->load(Yii::$app->request->post())) {
            try {
                if ($function->verifyUser($user->username, $user->email)) {
                    Yii::$app->session->setFlash('error', 'Utente già registrato');
                } else if ($function->registerAdmin(
                    $user->nome,
                    $user->cognome,
                    $user->password,
                    $user->email,
                    $user->ruolo,
                    $user->partita_iva,
                    $user->azienda,
                    $user->nazione,
                    $user->recapito_telefonico,
                    $user->telegram_username,
                    $user->telegram_chat_id
                )) {

                    Yii::$app->session->setFlash('success', 'Registrazione avvenuta correttamente');
                   
                  return  $this->redirect(['login']);
                    
                } else {

                    Yii::$app->session->setFlash('error', 'Registrazione fallita, riprovare');
                }

                return $this->refresh();
            } catch (existUserException $e) {
                Yii::$app->session->setFlash('error', $e->getMessage());
                return $this->refresh();
            }
        }


        return $this->render('register', ['user' => $user, 'turni' => $turni]);
    }

    public function actionAccount()
    {
        $account = User::findOne(['username' => Yii::$app->user->identity->username]);

        $count = User::find()->where(['username' => Yii::$app->user->identity->username])->count();
        if ($count == 0) {
            Yii::$app->session->setFlash('error', 'Non hai ancora creato nessun ticket');
            return $this->redirect(['tickets/new-ticket']);
        }
        return $this->render('myAccount', ['account' => $account]);
    }

    public function actionModifyUsername()
    {
        $user = new User();
        $function = new userService();

        if ($user->load(Yii::$app->request->post())) {
            /*
                if($function->verifyCookie()){
                      Yii::$app->session->setFlash('error','Email inesistente');
                      return $this->redirect(['index']);
                }
                */
            if ($function->recoveryEmail($user->email)) {
                Yii::$app->session->setFlash('success', 'Recupero email effettuata con successo');
                return $this->redirect(['site/index']);
            } else {
                Yii::$app->session->setFlash('error', 'Recupero email non  effettuata con successo');

                return $this->redirect(['site/index']);
            }
        }
        return $this->render('modifyEmail', ['user' => $user]);
    }

    public function actionReset($username)
    {
        $user = User::findOne(['blocco' => true]);
        $function = new userService();

        if ($function->resetLogin($username)) {
            Yii::$app->session->setFlash('success', 'Reset effettuato correttamente');
            return $this->redirect(['index']);
        } else {
            Yii::$app->session->setFlash('error', 'Reset  non effettuato correttamente');
            return $this->redirect(['index']);
        }
    }


    public function actionModifyIva()
    {
        $user = new User();
        $function = new userService();

        if ($user->load(Yii::$app->request->post())) {
            if ($function->ModifyPartitaIva($user->partita_iva)) {
                Yii::$app->session->setflash('success', 'Modifica della partita iva effettuata correttamente');
                return $this->redirect(['index']);
            } else {
                Yii::$app->session->setflash('success', 'Modifica della partita iva non effettuata correttamente');
                return $this->redirect(['index']);
            }
        }

        return $this->render('modifyIva', ['user' => $user]);
    }


    public function actionModifyImage()
    {
        $account = User::findOne(Yii::$app->user->id);
        $service = new userService();

        if ($account === null) {
            Yii::$app->session->setFlash('error', 'Profilo utente non trovato.');
            return $this->redirect(['account']);
        }

        if (Yii::$app->request->isPost) {
            $file = \yii\web\UploadedFile::getInstance($account, 'immagine');
            if ($file === null) {
                $file = \yii\web\UploadedFile::getInstanceByName('User[immagine]');
            }

            if ($file === null) {
                Yii::$app->session->setFlash('error', 'Nessun file caricato.');
                return $this->redirect(['account']);
            }

            $allowedExt = ['jpg', 'jpeg', 'png', 'webp'];
            $ext = strtolower($file->extension ?: pathinfo($file->name, PATHINFO_EXTENSION));
            if (!in_array($ext, $allowedExt, true)) {
                Yii::$app->session->setFlash('error', 'Formato non valido. Usa JPG, PNG o WEBP.');
                return $this->redirect(['account']);
            }

            if ((int)$file->size > (5 * 1024 * 1024)) {
                Yii::$app->session->setFlash('error', 'File troppo grande. Dimensione massima 5 MB.');
                return $this->redirect(['account']);
            }

            if ($service->modifyImmagine($account, $file)) {
                Yii::$app->session->setFlash('success', 'Immagine modificata con successo.');
            } else {
                Yii::$app->session->setFlash('error', 'Errore nella modifica dell\'immagine.');
            }

            return $this->redirect(['account']);
        }

        return $this->render('myAccount', [
            'account' => $account
        ]);
    }


    public function actionModifyEmail()
    {
        $user = User::findOne(['username' => Yii::$app->user->identity->username]);
        $function = new userService();

        if ($user->load(Yii::$app->request->post())) {
            if ($function->recoveryEmail($user->email)) {
                Yii::$app->session->setFlash('success', 'Email recuperatacon successo');
                return $this->redirect(['logout']);
            } else {
                Yii::$app->session->setFlash('success', 'Email non recuperatacon successo');
            }
        }
        return $this->render('modifyEmail', [
            'user' => $user
        ]);
    }

    public function actionSaltaPausa($id)
    {
        $function = new userService();
        $turni = Turni::findOne(['id_operatore' => $id]);

        if ($function->saltaPausa($id)) {
            Yii::$app->session->setFlash('success', 'Pausa saltata correttamente');
            return $this->refresh();
        } else {
            Yii::$app->session->setFlash('error', 'Pausa  non saltata correttamente');
            return $this->refresh();
        }
    }

    public function actionMyReclamo()
    {
        $reclamo = Mail::find()->where(['azienda' => Yii::$app->user->identity->azienda])->all();

        return $this->render('MyReclami', ['reclamo' => $reclamo]);
    }



    public function actionVisualizzato($codice_ticket)
    {
        $function = new userService();

        return $function->visualizzato($codice_ticket);

        //return $this->redirect(['all-reclami']);
    }

    public function actionAvanzaRiapertura($codice_ticket, $id_operatore)
    {
        $function = new userService();
        $cookie = Yii::$app->request->cookies;
        if ($function->avanzaRiapertura($codice_ticket, $id_operatore)) {
            if ($cookie->has('richiesta')) {
                Yii::$app->session->setFlash('error', 'La richiesta è sta gia\' inviata');
                return $this->redirect(['operatore/view-ticket']);
            }
            Yii::$app->session->setFlash('success', 'Riapertura avanzata correttamente');
            return $this->redirect(['operatore/view-ticket']);
        } else {
            Yii::$app->session->setFlash('error', 'Riapertura non  avanzata correttamente');
            return $this->redirect(['operatore/view-ticket']);
        }
    }

    public function actionMessagio($codice_ticket)
    {
        $function=new userService();
        $user=User::findOne(Yii::$app->user->identity->id);
        $model=new Mail();

        if($model->load(Yii::$app->request->post())){
        if($function->resolveMessage($codice_ticket,Yii::$app->user->identity->email,$model->messagio))
            {
                  Yii::$app->session->setFlash('success', 'Risposta inviata correttamente');
                  // Compone ed invia email
        $logoUrl = Yii::$app->request->hostInfo . Yii::getAlias('@web/img/taglio_dataseed.png');
        Yii::$app->mailer->compose()
            ->setTo(Yii::$app->params['senderEmail'])
            ->setFrom(Yii::$app->user->identity->email)
            ->setReplyTo([Yii::$app->user->identity->email => Yii::$app->user->identity->email])
            ->setSubject('Messagio relativo al ticket con codice:' . $codice_ticket)
            ->setHtmlBody('<html>'.'
            <body>
            <p>'.$model->messagio.'</p>
            <img src="'.$logoUrl.'" alt="Dataseed" style="max-width:180px;height:auto;">
            </body>
            '.'</html>')
            ->send();
                return $this->redirect(['index']);
            }else{
                 Yii::$app->session->setFlash('error', 'Risposta non inviata correttamente');
                return $this->redirect(['index']);
            }
        }

            return $this->render('Message',['model'=>$model]);
    }
}
