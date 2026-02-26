<?php

namespace app\controllers;

use app\eccezioni\dataException;
use app\models\Ticket;
use app\models\ticketfunction;
use app\models\ticketFunctions;
use app\models\User;
use app\models\userService;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class TicketsController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => [
                    'index', 'view', 'update', 'delete',
                    'open', 'close', 'lavorazione', 'scadence',
                    'new-ticket', 'my-ticket',
                    'modify-ticket', 'delete-ticket',
                    'resolve', 'ritiro', 'reintegra',
                    'my-reparto', 'my-reparto-open',
                ],
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                        'actions' => ['new-ticket', 'my-ticket', 'view', 'update', 'delete', 'open', 'scadence', 'close', 'lavorazione'],
                        'matchCallback' => function () {
                            return Yii::$app->user->identity->ruolo === 'cliente'
                                && (bool)Yii::$app->user->identity->approvazione;
                        },
                    ],
                    [
                        'allow' => true,
                        'roles' => ['@'],
                        'actions' => [
                            'my-ticket', 'view', 'update', 'delete', 'open',
                            'scadence', 'close', 'lavorazione', 'resolve',
                            'my-reparto', 'my-reparto-open', 'index',
                        ],
                        'matchCallback' => function () {
                            $ruolo = Yii::$app->user->identity->ruolo;
                            return in_array($ruolo, ['developer', 'ict', 'itc'], true)
                                && (bool)Yii::$app->user->identity->approvazione;
                        },
                    ],
                    [
                        'allow' => true,
                        'roles' => ['@'],
                        'actions' => [
                            'index', 'view', 'update', 'delete',
                            'open', 'close', 'lavorazione', 'scadence',
                            'new-ticket', 'my-ticket', 'modify-ticket',
                            'delete-ticket', 'resolve', 'ritiro',
                            'reintegra', 'my-reparto', 'my-reparto-open',
                        ],
                        'matchCallback' => function () {
                            return Yii::$app->user->identity->ruolo === 'amministratore'
                                && (bool)Yii::$app->user->identity->approvazione;
                        },
                    ],
                ],
                'denyCallback' => function () {
                    Yii::$app->session->setFlash('error', 'Non hai i permessi per questa operazione.');
                    return $this->redirect(['site/index']);
                },
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                    'delete-ticket' => ['POST'],
                    'resolve' => ['GET', 'POST'],
                ],
            ],
        ];
    }

    public function actionIndex()
    {
        $searchModel = new ticketfunction();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    public function actionUpdate($id)
    {
        $ticket = $this->findModel($id);
        $service = new ticketFunctions();

        if ($this->request->isPost && $ticket->load($this->request->post())) {
            if ($service->modificaTicket(
                $ticket->codice_ticket,
                $ticket->problema,
                $ticket->priorita,
                $ticket->reparto,
                $ticket->scadenza
            )) {
                Yii::$app->session->setFlash('success', 'Modifica effettuata correttamente');
            } else {
                Yii::$app->session->setFlash('error', 'Modifica non completata');
            }
            return $this->redirect(['view', 'id' => $ticket->id]);
        }

        return $this->render('modifyTicket', [
            'ticket' => $ticket,
        ]);
    }

    public function actionDelete($id)
    {
        $this->findModel($id)->delete();
        Yii::$app->session->setFlash('success', 'Ticket eliminato.');
        return $this->redirect(['index']);
    }

    public function actionOpen()
    {
        return $this->renderStatusFiltered('aperto');
    }

    public function actionClose()
    {
        return $this->renderStatusFiltered('chiuso');
    }

    public function actionLavorazione()
    {
        return $this->renderStatusFiltered('in lavorazione');
    }

    public function actionScadence()
    {
        return $this->renderStatusFiltered('scaduto');
    }

    public function actionNewTicket()
    {
        $ticket = new ticketfunction();
        $service = new ticketFunctions();
        $recipients = User::find()->where(['ruolo' => ['amministratore', 'ict', 'itc', 'developer']])->all();

        if ($ticket->load(Yii::$app->request->post())) {
            try {
                if ($service->newTicket(
                    $ticket->problema,
                    $ticket->reparto,
                    $ticket->scadenza,
                    $ticket->priorita
                )) {
                    $lastTicket = Ticket::find()
                        ->where(['id_cliente' => Yii::$app->user->id])
                        ->orderBy(['id' => SORT_DESC])
                        ->one();

                    foreach ($recipients as $recipient) {
                        $service->contact(
                            $recipient->email,
                            '<p>E\' stato creato un nuovo ticket con codice '
                                . ($lastTicket ? $lastTicket->codice_ticket : 'N/D')
                                . '.</p>',
                            'Nuovo ticket creato'
                        );
                    }

                    $service->ticketScaduto();
                    Yii::$app->session->setFlash('success', 'Richiesta inviata correttamente');
                    return $this->redirect(['my-ticket']);
                }

                Yii::$app->session->setFlash('error', 'Invio ticket non riuscito, riprovare.');
            } catch (dataException $e) {
                Yii::$app->session->setFlash('error', 'La data inserita risulta nel passato.');
                return $this->refresh();
            }
        }

        return $this->render('newTicket', [
            'ticket' => $ticket,
        ]);
    }

    public function actionMyTicket()
    {
        $searchModel = new ticketfunction();
        $params = Yii::$app->request->queryParams;
        $params['ticketfunction']['id_cliente'] = Yii::$app->user->id;
        $dataProvider = $searchModel->search($params);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionDeleteTicket($id)
    {
        $service = new ticketFunctions();
        $userService = new userService();
        $personale = User::find()->where(['ruolo' => ['amministratore', 'cliente', 'ict', 'itc', 'developer']])->all();
        $cliente = User::findOne(Yii::$app->user->id);

        if ($service->deleteTicket((int)$id)) {
            foreach ($personale as $p) {
                $userService->contact(
                    $p->email,
                    '<p>Ticket eliminato in data ' . date('Y-m-d')
                        . ' alle ore ' . date('H:i:s')
                        . ' da ' . ($cliente ? ($cliente->nome . ' ' . $cliente->cognome) : 'utente sconosciuto')
                        . '.</p>',
                    'Eliminazione ticket'
                );
            }

            Yii::$app->session->setFlash('success', 'Eliminazione effettuata con successo');
        } else {
            Yii::$app->session->setFlash('error', 'Eliminazione fallita');
        }

        return $this->redirect(['site/index']);
    }

    public function actionRitiro($codice_ticket)
    {
        $service = new ticketFunctions();

        if ($service->ritiraAssegnazione((string)$codice_ticket)) {
            Yii::$app->session->setFlash('success', 'Ritiro effettuato correttamente');
        } else {
            Yii::$app->session->setFlash('error', 'Ritiro non effettuato correttamente');
        }

        return $this->redirect(['site/index']);
    }

    public function actionModifyTicket($codiceTicket)
    {
        $ticket = Ticket::findOne(['codice_ticket' => $codiceTicket]);
        if ($ticket === null) {
            throw new NotFoundHttpException('Ticket non trovato.');
        }

        $service = new ticketFunctions();

        if ($ticket->load(Yii::$app->request->post())) {
            if ($service->modificaTicket(
                $ticket->codice_ticket,
                $ticket->problema,
                $ticket->priorita,
                $ticket->reparto,
                $ticket->scadenza
            )) {
                Yii::$app->session->setFlash('success', 'Modifica del ticket completata');
                return $this->redirect(['view', 'id' => $ticket->id]);
            }

            Yii::$app->session->setFlash('error', 'Modifica del ticket non completata');
            return $this->refresh();
        }

        return $this->render('modifyTicket', [
            'ticket' => $ticket,
        ]);
    }

    public function actionReintegra($codice_ticket)
    {
        $ticket = Ticket::findOne(['codice_ticket' => $codice_ticket]);
        if ($ticket === null) {
            throw new NotFoundHttpException('Ticket non trovato.');
        }

        $service = new ticketFunctions();
        if ($service->prolungate($ticket->codice_ticket)) {
            Yii::$app->session->setFlash('success', 'Ticket nuovamente gestibile');
        } else {
            Yii::$app->session->setFlash('error', 'Reintegrazione del ticket fallita');
        }

        return $this->redirect(['tickets/index']);
    }

    public function actionResolve($id)
    {
        $ticket = Ticket::findOne((int)$id);
        if ($ticket === null) {
            throw new NotFoundHttpException('Ticket non trovato.');
        }

        $service = new ticketFunctions();
        $userService = new userService();
        $personale = User::find()->where(['ruolo' => ['amministratore', 'cliente', 'ict', 'itc', 'developer']])->all();

        if (!$service->verifyAssegnazione($ticket->codice_ticket)) {
            Yii::$app->session->setFlash('error', 'Ticket non ancora assegnato');
            return $this->redirect(['assegnazioni/index']);
        }

        if ($service->chiudiTicket((int)$ticket->id)) {
            foreach ($personale as $p) {
                $userService->contact(
                    $p->email,
                    '<p>Ticket risolto il ' . date('Y-m-d') . ' alle ore ' . date('H:i:s')
                        . '.<br>Codice: ' . $ticket->codice_ticket . '</p>',
                    'Ticket risolto'
                );
            }
            Yii::$app->session->setFlash('success', 'Ticket risolto correttamente');
            return $this->redirect(['assegnazioni/my-ticket']);
        }

        Yii::$app->session->setFlash('error', 'Ticket non risolto correttamente');
        return $this->redirect(['assegnazioni/index']);
    }

    public function actionMyReparto()
    {
        $reparto = in_array(Yii::$app->user->identity->ruolo, ['ict', 'itc'], true) ? 'ict' : 'sviluppo';
        $ticket = Ticket::find()->where(['reparto' => $reparto])->all();

        return $this->render('myDepartment', ['ticket' => $ticket]);
    }

    public function actionMyRepartoOpen()
    {
        $reparto = in_array(Yii::$app->user->identity->ruolo, ['ict', 'itc'], true) ? 'ict' : 'sviluppo';
        $ticket = Ticket::find()->where(['reparto' => $reparto, 'stato' => 'aperto'])->all();

        return $this->render('myDepartment', ['ticket' => $ticket]);
    }

    protected function findModel($id)
    {
        if (($model = Ticket::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    private function renderStatusFiltered($status)
    {
        $searchModel = new ticketfunction();
        $params = Yii::$app->request->queryParams;
        $params['ticketfunction']['stato'] = $status;
        $dataProvider = $searchModel->search($params);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
}
