<?php

namespace app\controllers;
use Yii;

use app\models\ticketfunction;
use app\models\ticketFunctions;
use app\models\User;
use app\models\Ticket;
use app\models\userService;
use app\eccezioni\dataException;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * TableTicketController implements the CRUD actions for Ticket model.
 */
class TicketsController extends Controller
{
    /**
     * @inheritDoc
     */
   public function behaviors()
{
    return [
        // ACCESS CONTROL
        'access' => [
            'class' => \yii\filters\AccessControl::class,
            'only' => [
                'ritiro',
                'modify-ticket','reintegra','my-ticket','new-ticket','resolve','my-reparto',
                'my-reparto-open','resolve','my-reparto-open','index'
            ],
            'rules' => [

                // 1️⃣ CLIENTE
                [
                    'allow' => true,
                    'actions' => ['new-ticket','my-ticket','delete-ticket','modify-ticket','view','update','delete','findModel','open','my-ticket','scadence','close','lavorazione','my-account'],
                    'roles' => ['@'],
                    'matchCallback' => function () {
                        $ruolo = Yii::$app->user->identity->ruolo;
                        $approvazione=Yii::$app->user->identity->approvazione;
                         return $ruolo== 'cliente' && $approvazione;
                    },
                     'denyCallback'=>function(){
                        if(Yii::$app->user->identity->approvazione)
                            {
                                Yii::$app->sessionFlash('error','Attendere l\'approvazione da parte di un utente');
                            }
                    }
                ],

                // 2️⃣ OPERATORE (developer + ict)
                [
                    'allow' => true,
                    'actions' => ['new-ticket','my-ticket','delete-ticket','modify-ticket',
                    'view','update','delete','findModel','open','scadence','resolve',
                    'close','lavorazione','my-account','my-reparto-open'],
                    'roles' => ['@'],
                    'matchCallback' => function () {
                        $ruolo = Yii::$app->user->identity->ruolo;
                        $approvazione=Yii::$app->user->identity->approvazione;
                        return in_array($ruolo, ['developer','ict'], $approvazione) ;
                    },

                    'denyCallback'=>function(){
                        if(Yii::$app->user->identity->approvazione)
                            {
                                Yii::$app->sessionFlash('error','Attendere l\'approvazione da parte di un utente');
                            }
                    }
                ],

                // 3️⃣ AMMINISTRATORE
                [
                    'allow' => true,
                    'actions' => ['index','reintegra','ritiro','new-ticket','my-ticket','delete-ticket','modify-ticket','index','view','update','delete','findModel','open','scadence','close','lavorazione','my-account'],
                    'roles' => ['@'],
                    'matchCallback' => function () {
                        $approvazione=Yii::$app->user->identity->approvazione;
                        return Yii::$app->user->identity->ruolo === 'amministratore' && $approvazione;
                    }
                ],
            ],
        ],

        // VERB FILTER
        'verbs' => [
            'class' => \yii\filters\VerbFilter::class,
            'actions' => [
                'delete-ticket' => ['POST'],
               
                'resolve' => ['GET'],
            ],
        ],
    ];
}


    /**
     * Lists all Ticket models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new ticketfunction();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Ticket model.
     * @param int $id ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Ticket model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */


    /**
     * Updates an existing Ticket model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $ticket = $this->findModel($id);
        $function=new ticketfunction();
        if ($this->request->isPost && $ticket->load($this->request->post()))
             {
                if($function->modificaTicket($ticket->codice_ticket,$ticket->problema,$ticket->priorita,$ticket->ambito,$ticket->scadenza)){
                    Yii::$app->session->setFlash('success','Modifica effettuata correttamente');
                }
            return $this->redirect(['modifyTicket', 'ticket' => $ticket]);
        }

        return $this->render('modifyTicket', [
            'ticket' => $ticket,
        ]);
    }

    /**
     * Deletes an existing Ticket model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Ticket model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return Ticket the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Ticket::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }



    public function actionOpen()
{
    $searchModel = new ticketfunction();

    // Passiamo i queryParams come sempre
    $params = Yii::$app->request->queryParams;

    // Aggiungiamo il filtro per l'utente loggato
    $params['ticketfunction']['stato'] ='aperto';

    $dataProvider = $searchModel->search($params);

    return $this->render('index', [
        'searchModel' => $searchModel,
        'dataProvider' => $dataProvider,
    ]);
}

    public function actionClose()
{
    $searchModel = new ticketfunction();

    // Passiamo i queryParams come sempre
    $params = Yii::$app->request->queryParams;

    // Aggiungiamo il filtro per l'utente loggato
    $params['ticketfunction']['stato'] ='chiuso';

    $dataProvider = $searchModel->search($params);

    return $this->render('index', [
        'searchModel' => $searchModel,
        'dataProvider' => $dataProvider,
    ]);
}

    public function actionLavorazione()
{
    $searchModel = new ticketfunction();

    // Passiamo i queryParams come sempre
    $params = Yii::$app->request->queryParams;

    // Aggiungiamo il filtro per l'utente loggato
    $params['ticketfunction']['stato'] ='in lavorazione';

    $dataProvider = $searchModel->search($params);

    return $this->render('index', [
        'searchModel' => $searchModel,
        'dataProvider' => $dataProvider,
    ]);
}

    public function actionScadence()
{
    $searchModel = new ticketfunction();

    // Passiamo i queryParams come sempre
    $params = Yii::$app->request->queryParams;

    // Aggiungiamo il filtro per l'utente loggato
    $params['ticketfunction']['stato'] ='scaduto';

    $dataProvider = $searchModel->search($params);

    return $this->render('index', [
        'searchModel' => $searchModel,
        'dataProvider' => $dataProvider,
    ]);
}


    public function actionNewTicket()
    {
        $ticket = new ticketfunction();
        $functions = new ticketFunctions();
        $admins = User::find()->where(['ruolo' => ['amministratore', 'itc', 'developer']])->all();

        if ($ticket->load(Yii::$app->request->post())) {
            try{
        /*if($function->verifyData($ticket->scadenza))
            {
             
            }
            // Se esiste già un ticket simile
            if ($function->verifyTicket($ticket->problema)) {
                Yii::$app->session->setFlash(
                    'success',
                    "È stato inviato un sollecito ai nostri esperti che, al più presto, si occuperanno del suo ticket. Ci scusiamo per il disagio."
                );
            }
*/



            // Creazione nuovo ticket
            if ($functions->newTicket(
                $ticket->problema,
                $ticket->reparto,
                $ticket->scadenza,
                $ticket->priorita,

            )) {
                  // Notifica preventiva (opzionale)
        foreach ($admins as $admin) {
            $functions->contact(
                $admin->email,
                '<p>E\' stata avanzato un nuovo ticket con codice' . $ticket->codice_ticket . '.</p>',
                'Nuovo ticket in arrivo'
            );
        }
                Yii::$app->session->setFlash('success', 'Richiesta di ticketing inviata correttamente');
                $functions->ticketScaduto();
                return $this->redirect(['site/index']);
            } else {
                Yii::$app->session->setFlash(
                    'error',
                    "Problema durante la richiesta di invio ticketing. Al momento abbiamo delle difficoltà riguardanti il sistema. 
                    Contattare l'azienda e riferire il problema. Ci scusiamo per il disagio."
                );

               // return $this->redirect(['new-ticket']);
            }

           
        }catch(dataException $e){
               Yii::$app->session->setFlash('success','La data inserita risulta essere nel passato');
                return $this->refresh();
        }

        }
        return $this->render('newTicket', [
            'ticket' => $ticket
        ]);
    }

public function actionMyTicket()
{
       $searchModel = new ticketfunction();

    // Passiamo i queryParams come sempre
    $params = Yii::$app->request->queryParams;

    // Aggiungiamo il filtro per l'utente loggato
    $params['ticketfunction']['id_cliente'] =Yii::$app->user->identity->id;

    $dataProvider = $searchModel->search($params);

    return $this->render('index', [
        'searchModel' => $searchModel,
        'dataProvider' => $dataProvider,
    ]);
}


    public function actionDeleteTicket($id)
    {
        $function = new ticketFunction();
        $user=new userService();
         $personale = User::find()->where(['ruolo' => ['amministratore', 'cliente', 'itc', 'developer']])->all();
          $cliente = User::findOne(['username' => Yii::$app->user->identity->username]);

        if ($function->deleteTicket($id)) {


        foreach ($personale as $p) {
            $user->contact(
                $p->email,
                '<p>Si comunica che in data ' . date('Y-m-d') . ' alle ore ' . date('H:i:s') . ' è stato cancellato un ticket da ' . ($cliente ? ($cliente->nome . ' ' . $cliente->cognome) : 'utente sconosciuto') . '.</p>',
                'Eliminazione ticket'
            );
        }
            Yii::$app->session->setFlash('success', 'Eliminazione effettuata con successo');
            return $this->redirect(['site/index']);
        } else {
            Yii::$app->session->setFlash('error', 'eliminazione fallita a causa di un problema');
            return $this->redirect(['site/index']);
        }
    }

    public function actionRitiro($codice_ticket)
    {
        $function = new ticketFunction();

        if ($function->ritiraAssegnazione($codice_ticket)) {
            Yii::$app->session->setflash('success', 'Ritiro effettuato correttamente');
            return $this->redirect(['site/index']);
        } else {
            Yii::$app->session->setflash('else', 'Ritiro non effettuato correttamente');
            return $this->redirect(['site/index']);
        }
    }

    public function actionModifyTicket($codiceTicket)
    {
        $ticket = Ticket::findOne(['codice_ticket' => $codiceTicket]);
        
        $function = new ticketFunction();

        if($ticket->load(Yii::$app->request->post()))
            {
        if ($function->modificaTicket($ticket->codice_ticket, $ticket->problema, $ticket->priorita,$ticket->ambito,$ticket->scadenza)) {
            Yii::$app->session->setFlash('success', 'modifica del ticket avvenuta con successo');
            return $this->redirect(['site/index']);
        } else {
            Yii::$app->session->setFlash('error', 'Modifica del ticket non effettuata con successo');
            return $this->redirect(['site/index']);
        }

            }
        return $this->render('modifyTicket', [
            'ticket' => $ticket,
           

        ]);
    }

    public function actionReintegra($codice_ticket)
    {
        $ticket=Ticket::findOne(['codice_ticket'=>$codice_ticket]);
        $function=new ticketFunction();
        if($function->prolungate($ticket->codice_ticket))
            {
                Yii::$app->session->setFlash('success','Ticket nuovamente gestibile e lavorabile');
               
                }else{
                Yii::$app->session->setFlash('error','Reintegrazione del ticket fallita');
               
               
            }

            return $this->redirect(['admin/ticketing']);
    }

    public function actionResolve($id)
    {
        $ticket=Ticket::findOne($id);
        $user=new userService();
        $function=new ticketFunctions();
          $personale = User::find()->where(['ruolo' => ['amministratore', 'cliente', 'itc', 'developer']])->all();
        
          if(!$function->verifyAssegnazione($ticket->codice_ticket))
            {
                 Yii::$app->session->setFlash('error','Ticket non ancora in assegnazione');
                 return $this->redirect(['assegnazioni/index']);
                 }
            if($function->chiudiTicket($ticket->id))
                {
                     foreach ($personale as $p) {
                    $user->contact(
                        $p->email,
                        '<p>Si comunica che in data ' . date('Y-m-d') . ' alle ore ' . date('H:i:s') . ' è scaduto un ticket.<br>Codice ticket: ' . $ticket->codice_ticket . '<br>Scaduto il: ' . $ticket->scadenza . '</p>',
                        'Ticket scaduto'
                    );
                }
                    Yii::$app->session->setFlash('success','Ticket risolto correttamente');
                    return $this->redirect(['my-ticket']);
                }else{
                    Yii::$app->session->setFlash('error','Ticket purtroppo non risolto correttamente');
                    return $this->redirect(['operatore/view-ticket']);
                }
        

        return $this->redirect(['operatore/view-ticket']);
    }

   

public function actionMyReparto()
{

    $ticket=Ticket::find()->where(['ambito'=>'sviluppo'])->all();

    return $this->render('myDepartment',['ticket'=>$ticket]);
}

public function actionMyRepartoOpen()
{
    $ticket=Ticket::find()->where(['ambito'=>'sviluppo','stato'=>'aperto'])->all();

    return $this->render('myDepartment',['ticket'=>$ticket]);
}

public function actionMessageTicket()
{
    
}
}
