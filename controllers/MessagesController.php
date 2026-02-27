<?php

namespace app\controllers;

use app\models\Assegnazioni;
use app\models\Ticket;
use app\models\TicketMessage;
use app\models\ticketFunctions;
use app\models\User;
use Yii;
use yii\data\ActiveDataProvider;
use yii\db\Expression;
use yii\filters\AccessControl;
use yii\helpers\Html;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;

class MessagesController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['index', 'view', 'compose'],
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['index', 'view', 'compose'],
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    public function actionIndex($box = 'inbox')
    {
        if (!$this->isMessageTableAvailable()) {
            Yii::$app->session->setFlash('error', 'Modulo messaggi non installato. Esegui la migrazione database.');
            return $this->redirect(['site/index']);
        }

        $userId = Yii::$app->user->id;
        $box = ($box === 'sent') ? 'sent' : 'inbox';

        $query = TicketMessage::find()
            ->with(['sender', 'recipient', 'ticket'])
            ->orderBy(['created_at' => SORT_DESC]);

        if ($box === 'sent') {
            $query->andWhere(['sender_id' => $userId]);
            $title = 'Messaggi inviati';
        } else {
            $query->andWhere(['recipient_id' => $userId]);
            $title = 'Messaggi ricevuti';
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 15,
            ],
        ]);

        $unreadCount = TicketMessage::find()
            ->where(['recipient_id' => $userId, 'is_read' => 0])
            ->count();

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'box' => $box,
            'title' => $title,
            'unreadCount' => (int)$unreadCount,
        ]);
    }

    public function actionView($id)
    {
        if (!$this->isMessageTableAvailable()) {
            Yii::$app->session->setFlash('error', 'Modulo messaggi non installato. Esegui la migrazione database.');
            return $this->redirect(['site/index']);
        }

        $model = $this->findAccessibleMessage((int)$id);

        if ((int)$model->recipient_id === (int)Yii::$app->user->id && (int)$model->is_read === 0) {
            $model->is_read = 1;
            $model->read_at = date('Y-m-d H:i:s');
            $model->save(false, ['is_read', 'read_at']);
        }

        return $this->render('view', [
            'model' => $model,
        ]);
    }

    public function actionCompose($ticketId = null, $recipientId = null)
    {
        if (!$this->isMessageTableAvailable()) {
            Yii::$app->session->setFlash('error', 'Modulo messaggi non installato. Esegui la migrazione database.');
            return $this->redirect(['site/index']);
        }

        $model = new TicketMessage();
        $userId = (int)Yii::$app->user->id;

        $ticketOptions = $this->getTicketOptions();

        if ($ticketId !== null) {
            $ticketId = (int)$ticketId;
            if (array_key_exists($ticketId, $ticketOptions)) {
                $model->ticket_id = $ticketId;
            }
        }

        $selectedTicketId = !empty($model->ticket_id) ? (int)$model->ticket_id : null;
        $recipientOptions = $this->getRecipientOptions($selectedTicketId);
        $recipientHint = $selectedTicketId !== null
            ? 'Destinatari filtrati automaticamente in base al reparto del ticket.'
            : 'Seleziona un ticket per limitare i destinatari al reparto corretto.';

        if ($recipientId !== null) {
            $recipientId = (int)$recipientId;
            if (array_key_exists($recipientId, $recipientOptions)) {
                $model->recipient_id = $recipientId;
            }
        }

        if ($model->load(Yii::$app->request->post())) {
            $model->sender_id = $userId;
            $model->is_read = 0;

            $selectedTicketId = !empty($model->ticket_id) ? (int)$model->ticket_id : null;
            $recipientOptions = $this->getRecipientOptions($selectedTicketId);
            $recipientHint = $selectedTicketId !== null
                ? 'Destinatari filtrati automaticamente in base al reparto del ticket.'
                : 'Seleziona un ticket per limitare i destinatari al reparto corretto.';

            if (!array_key_exists((int)$model->recipient_id, $recipientOptions)) {
                $model->addError('recipient_id', 'Destinatario non valido per il reparto del ticket selezionato.');
            }

            if ($selectedTicketId !== null && !array_key_exists($selectedTicketId, $ticketOptions)) {
                $model->addError('ticket_id', 'Ticket non disponibile per il tuo profilo.');
            }

            if ($model->hasErrors()) {
                return $this->render('compose', [
                    'model' => $model,
                    'recipientOptions' => $recipientOptions,
                    'ticketOptions' => $ticketOptions,
                    'recipientHint' => $recipientHint,
                ]);
            }

            if ($model->save()) {
                $emailSent = $this->sendNotificationEmail($model);
                if ($emailSent) {
                    Yii::$app->session->setFlash('success', 'Messaggio inviato e notifica email recapitata.');
                } else {
                    Yii::$app->session->setFlash('info', 'Messaggio interno inviato. Email non recapitata.');
                }

                return $this->redirect(['view', 'id' => $model->id]);
            }
        }

        return $this->render('compose', [
            'model' => $model,
            'recipientOptions' => $recipientOptions,
            'ticketOptions' => $ticketOptions,
            'recipientHint' => $recipientHint,
        ]);
    }

    protected function findAccessibleMessage($id)
    {
        $model = TicketMessage::find()
            ->with(['sender', 'recipient', 'ticket'])
            ->where(['id' => $id])
            ->one();

        if ($model === null) {
            throw new NotFoundHttpException('Messaggio non trovato.');
        }

        $user = Yii::$app->user->identity;
        $isOwner = ((int)$model->sender_id === (int)$user->id) || ((int)$model->recipient_id === (int)$user->id);
        $isAdmin = $user->ruolo === 'amministratore';

        if (!$isOwner && !$isAdmin) {
            throw new ForbiddenHttpException('Non puoi accedere a questo messaggio.');
        }

        return $model;
    }

    protected function getRecipientOptions(?int $ticketId = null)
    {
        $identity = Yii::$app->user->identity;

        $query = User::find()
            ->where(['<>', 'id', $identity->id])
            ->andWhere(['approvazione' => 1])
            ->orderBy(['ruolo' => SORT_ASC, 'nome' => SORT_ASC, 'cognome' => SORT_ASC]);

        if ($ticketId !== null) {
            $ticket = Ticket::findOne((int)$ticketId);
            if ($ticket === null) {
                return [];
            }

            $allowedRoles = ticketFunctions::rolesForDepartment((string)$ticket->reparto);
            if ($identity->ruolo === 'cliente') {
                $query->andWhere(['ruolo' => $allowedRoles]);
            } else {
                $conditions = ['ruolo' => $allowedRoles];
                if (!empty($ticket->id_cliente)) {
                    $conditions = ['or', ['ruolo' => $allowedRoles], ['id' => (int)$ticket->id_cliente]];
                }
                $query->andWhere($conditions);
            }
        } elseif ($identity->ruolo === 'cliente') {
            $query->andWhere(['ruolo' => ['amministratore', 'developer', 'ict', 'itc', 'sistemista']]);
        }

        $users = $query->all();
        $options = [];

        foreach ($users as $user) {
            $nomeCompleto = trim($user->nome . ' ' . $user->cognome);
            $options[(int)$user->id] = $nomeCompleto . ' - ' . $user->ruolo;
        }

        return $options;
    }

    protected function getTicketOptions()
    {
        $identity = Yii::$app->user->identity;
        $query = Ticket::find()->orderBy(['id' => SORT_DESC]);

        if ($identity->ruolo === 'cliente') {
            $query->andWhere(['id_cliente' => $identity->id]);
        } elseif ($identity->ruolo !== 'amministratore') {
            $department = ticketFunctions::departmentFromRole($identity->ruolo);
            $aliases = ticketFunctions::departmentAliases($department);
            if (!empty($aliases)) {
                $query->andWhere(['in', new Expression('LOWER(reparto)'), $aliases]);
            } else {
                $codiciTicket = Assegnazioni::find()
                    ->select('codice_ticket')
                    ->where(['id_operatore' => $identity->id])
                    ->column();
                if (empty($codiciTicket)) {
                    return [];
                }
                $query->andWhere(['codice_ticket' => $codiciTicket]);
            }
        }

        $tickets = $query->all();
        $options = [];

        foreach ($tickets as $ticket) {
            $options[(int)$ticket->id] = $ticket->codice_ticket . ' - ' . $ticket->stato . ' - ' . $ticket->reparto;
        }

        return $options;
    }

    protected function sendNotificationEmail(TicketMessage $messageModel)
    {
        $sender = User::findOne($messageModel->sender_id);
        $recipient = User::findOne($messageModel->recipient_id);

        if ($sender === null || $recipient === null || empty($recipient->email)) {
            return false;
        }

        $ticketCode = $messageModel->ticket ? $messageModel->ticket->codice_ticket : 'N/D';
        $messageUrl = Yii::$app->urlManager->createAbsoluteUrl(['messages/view', 'id' => $messageModel->id]);
        $subject = '[Ticketing] ' . $messageModel->subject;

        $bodyHtml = '<p>Hai ricevuto un nuovo messaggio nel portale ticketing.</p>'
            . '<p><strong>Ticket:</strong> ' . Html::encode($ticketCode) . '</p>'
            . '<p><strong>Da:</strong> ' . Html::encode(trim($sender->nome . ' ' . $sender->cognome)) . '</p>'
            . '<p><strong>Oggetto:</strong> ' . Html::encode($messageModel->subject) . '</p>'
            . '<p><strong>Messaggio:</strong><br>' . nl2br(Html::encode($messageModel->body)) . '</p>'
            . '<p><a href="' . Html::encode($messageUrl) . '">Apri il messaggio nel sistema</a></p>';

        try {
            return (bool)Yii::$app->mailer->compose()
                ->setTo($recipient->email)
                ->setFrom([Yii::$app->params['senderEmail'] => Yii::$app->params['senderName']])
                ->setReplyTo([$sender->email => trim($sender->nome . ' ' . $sender->cognome)])
                ->setSubject($subject)
                ->setHtmlBody($bodyHtml)
                ->send();
        } catch (\Throwable $e) {
            Yii::warning('Invio email messaggio fallito: ' . $e->getMessage(), __METHOD__);
            return false;
        }
    }

    private function isMessageTableAvailable()
    {
        try {
            return Yii::$app->db->schema->getTableSchema(TicketMessage::tableName(), true) !== null;
        } catch (\Throwable $e) {
            return false;
        }
    }
}
