<?php

namespace app\models;
use yii\helpers\Html;
use Yii;
use yii\web\UploadedFile;
use app\models\User;
use yii\base\Model;
use yii\web\Cookie;

class userService extends Model
{

    // =========================
    // GESTIONE TURNI
    // =========================
    public function defineTurni($id_operatore,$entrata,$uscita,$pausa)
    {
        // Crea un nuovo record Turni
        $turni=new Turni();

        // Assegna i valori
        $turni->id_operatore=$id_operatore;
        $turni->entrata=$entrata;
        $turni->uscita=$uscita;
        $turni->pausa=$pausa;

        // Salva e ritorna risultato
        if(!$turni->save()){
            // Se fallisce non fai nulla (qui potresti loggare errore)
        }
        return $turni->save(); // ⚠ doppio save inutile
    }

    // =========================
    // INVIO EMAIL GENERICA
    // =========================
    public function contact($email, $messagio, $oggetto)
    {
        $emailSent = Yii::$app->mailer->compose()
            ->setTo($email)
            ->setFrom(Yii::$app->params['senderEmail'])
            ->setReplyTo([$email => $email])
            ->setSubject($oggetto)
            ->setHtmlBody($messagio) 
            ->send();

        $telegramSent = false;
        $user = User::findOne(['email' => $email]);
        if ($user && !empty($user->telegram_chat_id)) {
            $plainMessage = trim(strip_tags($messagio));
            $telegramSent = $this->sendTelegramMessage(
                $user->telegram_chat_id,
                $oggetto . "\n\n" . $plainMessage
            );
        }

        return $emailSent || $telegramSent;
    }

    // =========================
    // VERIFICA SE USERNAME O EMAIL ESISTONO
    // =========================
    public function verifyUser($username,$email)
    {
        // Controllo username
        if(User::findOne(['username' => $username]) ){
            return true; 
        }

        // Controllo email
        if(User::findOne(['email' => $email]))
        {
            return true;
        }

        return false;
    }

  public function emailRequest($email)
{
    $user = User::findOne(['email' => $email]);

    if (!$user) {
        return false;
    }

    // Genera token
    $user->generatePasswordResetToken();

    if (!$user->save(false)) {
        return false;
    }

    // Crea link con token
    $link = Yii::$app->urlManager->createAbsoluteUrl([
        'site/recupero-password',
        'token' => $user->token
    ]);

    // Invia email
    Yii::$app->mailer->compose()
        ->setTo($user->email)
        ->setFrom(Yii::$app->params['senderEmail'])
        ->setSubject('Recupero password')
        ->setHtmlBody("
            <p>È stata richiesta la reimpostazione della password.</p>
            <p>Clicca qui per procedere:</p>
            <p><a href='$link'>$link</a></p>
        ")
        ->send();

    return true;
}


    // =========================
    // REGISTRAZIONE UTENTE / ADMIN
    // =========================
    public function registerAdmin(
        $nome,
        $cognome,
        $password,
        $email,
        $ruolo,
        $partita_iva,
        $azienda,
        $nazione,
        $recapito_telefonico,
        $telegram_username = null,
        $telegram_chat_id = null
    )
    {
        $user = new User();

        // Gestione upload immagine
        $file=UploadedFile::getInstance($user,'immagine');

        if ($file) {
            $ext = strtolower($file->extension ?: pathinfo($file->name, PATHINFO_EXTENSION));
            if ($ext === '') {
                $ext = 'png';
            }
            $fileName = Yii::$app->security->generateRandomString(16) . '.' . $ext;
            $uploadDir = Yii::getAlias('@webroot/img/upload');
            if (!is_dir($uploadDir)) {
                @mkdir($uploadDir, 0755, true);
            }
            $file->saveAs($uploadDir . DIRECTORY_SEPARATOR . $fileName);
            $user->immagine = $fileName;
        }

        // Assegna campi
        $user->nome = $nome;
        $user->cognome = $cognome;
        $user->username =$nome[0].'.'.$cognome;
        $user->password = Yii::$app->security->generatePasswordHash($password);
        $user->email = $email;
        $user->auth_key = Yii::$app->security->generateRandomString();
        $user->access_token = Yii::$app->security->generateRandomString();
        $user->ruolo = $ruolo;
        $user->azienda=$azienda;
        $user->nazione=$nazione;
        $user->recapito_telefonico=$recapito_telefonico;
        $user->telegram_username = $telegram_username;
        $user->telegram_chat_id = $telegram_chat_id;
        $user->tentativi=10;
        $user->blocco=false;
       
        $user->partita_iva=$partita_iva;

        // Logica approvazione cliente
        if($user->partita_iva!=null && $user->ruolo=='cliente'){
            $user->approvazione=true;
        }else{
            $user->approvazione=false;
        }

        // Default azienda
        if($user->azienda ==null || $user->azienda == ''){
            $user->azienda='Dataseed';
        }

        if ($user->save()) {

        

        return true;
    }else{
        return false;
    }

            // Se non cliente crea turno base
            if($user->ruolo!='cliente'){
                $this->defineTurni($user->id,null,null,null);   
            }

            // Se non approvato crea cookie temporaneo
            if(!$user->approvazione){
                $cookie=new Cookie([
                    'name'=>'utente',
                    'value'=>$email,
                    'expire'=>time() + 600,
                ]);

                Yii::$app->response->cookies->add($cookie);
            }

            
        }
    

public function modifyPassword($password, $token)
{
    $user = User::findOne(['token' => $token]);

    if (!$user) {
        return false;
    }

    $user->password = Yii::$app->security->generatePasswordHash($password);

    // invalida il token
    $user->token = null;

    return $user->save();
}



    // =========================
    // INVIO MAIL RECUPERO
    // =========================
   /*
    public function invioMail($email)
{
    

        Yii::$app->mailer->compose()
            ->setTo($email)
            ->setFrom([Yii::$app->params['senderEmail'] => Yii::$app->params['senderName']])
            ->setReplyTo([$email => $email])
            ->setSubject('RICHIESTA DI RECUPERO')
            ->setHtmlBody('
                <p>Salve sig. ' . Html::encode($email) . ',</p>
                <p>Abbiamo ricevuto la richiesta di modifica della password.
                Pertanto le inviamo il link per il recupero:</p>
                <p><a href="http://localhost:8000/site/recupero-password">
                Clicca qui per recuperare la password</a></p>
            ')
            ->send();

        return true;
  */

   
//}


    // =========================
    // MODIFICA EMAIL UTENTE LOGGATO
    // =========================
    public function recoveryEmail($nuovaEmail)
    {
        $user = User::findOne(['username' => Yii::$app->user->identity->username]);

        if (!$user) {
            return false;
        }

        // Aggiorna username e email
        $user->username = $nuovaEmail;
        $user->email = $nuovaEmail;

        if ($user->save()) {

            // Notifica modifica via email
            Yii::$app->mailer->compose()
                ->setTo([Yii::$app->params['senderEmail'] => Yii::$app->params['senderName']])
                ->setFrom($user->email)
                ->setReplyTo([$user->email => $user->email])
                ->setSubject('RICHIESTA DI RECUPERO')
                ->setTextBody('Salve ' . $user->email .
                    ', la informiamo che in data ' . date('Y-m-d') .
                    ' alle ore ' . date('H:i:s') .
                    ' è stata effettuata la modifica della sua email.')
                ->send();

            return true;
        }else{
            return false;
        }
    }

    // =========================
    // RESET LOGIN
    // =========================
    public function resetLogin($username){
        $user=User::findOne(['username'=>$username]);

        $user->tentativi=10; // reset tentativi
        $user->blocco=false; // sblocca utente

        return $user->save() ? true : false;
    }

    // =========================
    // APPROVAZIONE UTENTE
    // =========================
    public function verifyApprovazione($username){
        $user=User::findOne(['username'=>$username]);
        return $user->approvazione;
    }

    public function approva($username)
    {
        $user=User::findOne(['username'=>$username]);
        $user->approvazione=true;

    
        return $user->save();
    }

    // =========================
    // MODIFICA PARTITA IVA
    // =========================
    public function ModifyPartitaIva($partitaIva)
    {
        $user=User::findOne(['username'=>Yii::$app->user->identity->username]);
        $user->partita_iva=$partitaIva;

        if($user->save()){
            return true;
        }else{
            return false;
        }
    }

    // =========================
    // MODIFICA IMMAGINE PROFILO
    // =========================
    /**
     * @param \app\models\User $user
     * @param UploadedFile $file
     * @return bool
     */
    public function modifyImmagine($user, UploadedFile $file)
    {
        if (!$user || !$file) {
            return false;
        }

        $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp'];
        $ext = strtolower($file->extension ?: pathinfo($file->name, PATHINFO_EXTENSION));
        if (!in_array($ext, $allowedExtensions, true)) {
            Yii::warning("Estensione non consentita: {$ext}", __METHOD__);
            return false;
        }

        $maxSize = 5 * 1024 * 1024;
        if ((int)$file->size > $maxSize) {
            Yii::warning("File troppo grande: {$file->size} bytes", __METHOD__);
            return false;
        }

        $uploadDir = Yii::getAlias('@webroot/img/upload');
        if (!is_dir($uploadDir) && !mkdir($uploadDir, 0755, true) && !is_dir($uploadDir)) {
            Yii::error("Impossibile creare la cartella upload: {$uploadDir}", __METHOD__);
            return false;
        }

        if (!is_writable($uploadDir)) {
            @chmod($uploadDir, 0755);
            if (!is_writable($uploadDir)) {
                Yii::error("Cartella upload non scrivibile: {$uploadDir}", __METHOD__);
                return false;
            }
        }

        $fileName = Yii::$app->security->generateRandomString(20) . '.' . $ext;
        $fullPath = $uploadDir . DIRECTORY_SEPARATOR . $fileName;
        $oldImage = $user->immagine;

        try {
            if (!$file->saveAs($fullPath, false)) {
                Yii::error("Salvataggio file fallito: {$fullPath}", __METHOD__);
                return false;
            }
        } catch (\Throwable $e) {
            Yii::error("Eccezione durante upload immagine: " . $e->getMessage(), __METHOD__);
            return false;
        }

        $user->immagine = $fileName;
        if ($user->save(false, ['immagine'])) {
            if (!empty($oldImage) && $oldImage !== $fileName) {
                $oldPath = $uploadDir . DIRECTORY_SEPARATOR . $oldImage;
                if (is_file($oldPath) && is_writable($oldPath)) {
                    @unlink($oldPath);
                }
            }
            return true;
        }

        if (is_file($fullPath)) {
            @unlink($fullPath);
        }
        Yii::error('Impossibile salvare il modello User con la nuova immagine.', __METHOD__);
        return false;
    }

    // =========================
    // MODIFICA TURNI
    // =========================
    public function modifyTurni($id_operatore,$entrata,$uscita,$pausa)
    {
        $turni=Turni::findOne(['id_operatore'=>$id_operatore]);

        $turni->entrata=$entrata;
        $turni->uscita=$uscita;
        $turni->pausa=$pausa;

        return $turni->save();
    }

    // =========================
    // INSERIMENTO / CALCOLO STATO PAUSA
    // =========================
    public function insertPausa($id_operatore)
    {
        $turni = Turni::findOne(['id_operatore' => $id_operatore]);
        if ($turni === null) {
            return false;
        }

        $oraAttuale = time();

        $inizioPausa = null;
        if (!empty($turni->pausa)) {
            $tsPausa = strtotime($turni->pausa);
            if ($tsPausa !== false) {
                $inizioPausa = $tsPausa;
            }
        }

        $finePausa = ($inizioPausa !== null) ? $inizioPausa + 3600 : null;

        $timestampUscita = null;
        if (!empty($turni->uscita)) {
            $tsUscita = strtotime($turni->uscita);
            if ($tsUscita !== false) {
                $timestampUscita = $tsUscita;
            }
        }

        $inizioServizio = strtotime('today 09:00');
        $fineServizio = strtotime('today 18:00');

        if ($timestampUscita !== null && $oraAttuale >= $timestampUscita) {
            $turni->stato = 'Fuori servizio';
        } elseif ($inizioPausa !== null && $oraAttuale >= $inizioPausa && $oraAttuale <= $finePausa) {
            $turni->stato = 'In pausa';
        } elseif ($oraAttuale >= $inizioServizio && $oraAttuale <= $fineServizio) {
            $turni->stato = 'In servizio';
        } else {
            $turni->stato = 'Non in servizio';
        }

        return $turni->save();
    }

    // =========================
    // SALTA PAUSA
    // =========================
    public function saltaPausa($id)
    {
        $turni = Turni::findOne(['id_operatore' => $id]);

        // Posticipa pausa di 1 ora
        $turni->pausa = date('Y-m-d H:i:s', strtotime($turni->pausa . ' +1 hour'));

        $turni->stato = 'In servizio';

        return $turni->save();
    }

    // =========================
    // METTI FUORI SERVIZIO
    // =========================
    public function fuoriServizio(){
        $turni=Turni::findOne(['id_operatore'=>Yii::$app->user->identity->id]);

        if(!$turni) return;

        $turni->stato='Fuori Servizio';
        return $turni->save();
    }

    // =========================
    // GESTIONE RUOLI
    // =========================
    public function assegnaRuolo($id,$ruolo)
    {
        $user=User::findOne($id);
        $user->ruolo=$ruolo;
        return $user->save();
    }

    public function resetRuolo($id)
    {
        $user=User::findOne($id);
        if($user->approvazione){
            $user->approvazione=false;
        }
        $user->ruolo='personale';
        return $user->save();
    }

    public function modifyRuolo($id,$ruolo)
    {
        $user=User::findOne($id);
        $user->ruolo=$ruolo;
        if($user->approvazione){
            $user->approvazione=false;
        }
        return $user->save();
    }


     // =========================
    // AVANZA UNA RIAPERTURA
    // =========================
public function avanzaRiapertura($codice_ticket, $id_operatore)
{
    // Recupera tutti gli admin
    $user = User::find()->where(['ruolo' => 'amministratore'])->all();
    $operatore = User::findOne($id_operatore);
    $ticket=Ticket::findOne(['codice_ticket'=>$codice_ticket]);
    $tutteInviate = true; // assumiamo successo

    foreach ($user as $user_item) {

        $success = $this->contact(
            $user_item->email,
            '
            <html>
            <body>
            <p>E\' stata avanzata una richiesta di ticket risolto ma da riaprire<br>
            Codice ticket: ' . $codice_ticket . '
            <br> da: ' . $operatore->nome . ' ' . $operatore->cognome . '
            </p>
            <a href='.'http://localhost:8000/site/login'.'></a>
            </body>
            </html>
            ',
            'richiesta di avanzo del ticket'
        );

 

    
        if (!$success) {
            $tutteInviate = false;
            
        }
    }

    return $tutteInviate;
}

public function disattiva2fa()
{
    $user=User::findOne(Yii::$app->user->identity->id);

    if($user->is_totp_enabled)
        {
            $user->totp_secret=null;
            $user->is_totp_enabled=0;

            $user->save();
        }
}

public function resolveMessage($codice_ticket,$messagio)
{
    $mail=new Mail();

    $mail->mittente=Yii::$app->user->identity->email;
    $mail->destinatario=Yii::$app->params['senderEmail'];
    $mail->messagio=$messagio;
    $mail->oggetto='Messagio da parte di '. Yii::$app->user->identity->nome.' '. Yii::$app->user->identity->cognome;
    $mail->codice_ticket=$codice_ticket;

 
    return $mail->save();

    
}

public function sendTelegramMessage($chatId, $message)
{
    $botToken = Yii::$app->params['telegramBotToken'] ?? null;
    if (empty($botToken) || empty($chatId) || empty($message)) {
        return false;
    }

    $url = 'https://api.telegram.org/bot' . $botToken . '/sendMessage';
    $payload = http_build_query([
        'chat_id' => $chatId,
        'text' => $message,
        'disable_web_page_preview' => true,
    ]);

    $context = stream_context_create([
        'http' => [
            'method' => 'POST',
            'header' => "Content-Type: application/x-www-form-urlencoded\r\n",
            'content' => $payload,
            'timeout' => 8,
        ],
    ]);

    $response = @file_get_contents($url, false, $context);
    return $response !== false;
}
}
