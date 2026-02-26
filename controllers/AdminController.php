<?php

namespace app\controllers;

use app\models\ticketFunction;
use app\models\User;
use app\models\userService;
use app\models\Ticket;

use app\models\Turni;
use app\models\UserTable;
use Yii;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
/**
 * AdminController implements the CRUD actions for User model.
 */
class AdminController extends Controller
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
                'index','update','view','findModel',
                'delegate','reset','attese','block',
                'approve','ritira','resetLogin','view-operatori',
                'verifyRuolo'
            ],
            'rules' => [

                
                [
                    'allow' => true,
                    'actions' => [ 'index','update','view','findModel',
                'delegate','reset','attese','block',
                'approve','ritira','resetLogin','view-operatori',
                'verifyRuolo'],
                    'roles' => ['@'],
                    'matchCallback' => function () {
                        $ruolo = Yii::$app->user->identity->ruolo;
                        $approvazione=Yii::$app->user->identity->approvazione;
                        return $ruolo== 'amministratore' && $approvazione;
                    },
                    'denyCallback'=>function(){
                        return $this->redirect(['attese']);
                    }
                ],
            ],
        ],
    ];
}


    /**
     * Lists all User models.
     *
     * @return string
     */
  
  public function actionIndex($mode = 'all')
{
    $searchModel = new \app\models\UserTable();

    // Prendiamo i parametri GET (paginazione / filtri)
    $params = Yii::$app->request->queryParams;

    // Se vuoi forzare un filtro sui ruoli quando chiami una specifica action,
    // impostalo qui prima di chiamare search():
    // $params['UserTable']['ruolo'] = ['developer','ict','amministratore'];

    // Otteniamo il dataProvider dal search model (search() applica filtri, ordinamenti, paginazione)
    $dataProvider = $searchModel->search($params);

    // Recuperiamo la query per applicare filtri aggiuntivi (se necessari)
    $query = $dataProvider->query;

    // In base alla modalità applichiamo filtri diversi
    switch ($mode) {
        case 'pending':
            // utenti in attesa: approvazione = 0
            $query->andWhere(['approvazione' => 0]);
            $title = 'Utenti in attesa';
            break;

        case 'blocked':
            // utenti bloccati: blocco != 0
            $query->andWhere(['<>', 'blocco', 0]);
            $title = 'Utenti bloccati';
            break;

        case 'operators':
            // esempio: solo ruoli operatori
            $roles = ['developer', 'ict', 'amministratore'];
            $query->andWhere(['ruolo' => $roles]);
            $title = 'Operatori';
            break;

        default:
            $title = 'Users';
            break;
    }

    // Non esiste setQuery(); abbiamo già modificato $dataProvider->query direttamente.
    return $this->render('index', [
        'searchModel'  => $searchModel,
        'dataProvider' => $dataProvider,
        'mode'         => $mode,
        'title'        => $title,
    ]);
}


    /**
     * Displays a single User model.
     * @param int $id
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
 

    /**
     * Creates a new User model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */


    /**
     * Updates an existing User model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = User::findOne($id);
        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success','Turno dell \' operatore aggiornato correttamente');
            return $this->redirect(['index']);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

       public function actionView($id)
    {
        $model=User::findOne([$id]);
        return $this->render('view', [
            'model' => $model,
        ]);
    }
    /**
     * Deletes an existing User model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
 

    /**
     * Finds the User model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id
     * @return User the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = User::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    public function actionDelegate($id)
    {
        $ticket=Ticket::findOne($id);
        if ($ticket === null) {
            Yii::$app->session->setFlash('error', 'Ticket non trovato');
            return $this->redirect(['tickets/index']);
        }
        
        $function=new ticketFunction();

        if($function->assegnaTicket($ticket->codice_ticket,$ticket->reparto))
            {
                Yii::$app->session->setFlash('success','Ticket assegnato correttamente');
                return $this->redirect(['tickets/index']);
                }else{
                Yii::$app->session->setFlash('error','Ticket non assegnato correttamente');
                return $this->redirect(['tickets/index']);
            }

    }

    public function actionReset($id)
    {
        $function=new userService();
        $model=new UserTable();
        if($function->resetLogin($id))
            {
                Yii::$app->session->setFlash('success','Reset effettuato correttamente');
                return $this->redirect(['update','id'=>$id]);
            }else{
                Yii::$app->session->setFlash('error','Reset non effettuato correttamente');
                return $this->redirect(['update','id'=>$id]);
            }
    }

     public function actionAttese()
{
    $searchModel = new UserTable();

    // Passiamo i queryParams come sempre
    $params = Yii::$app->request->queryParams;

    // Aggiungiamo il filtro per l'utente loggato
    $params['UserTable']['approvazione'] =0;

    $dataProvider = $searchModel->search($params);

    return $this->render('index', [
        'searchModel' => $searchModel,
        'dataProvider' => $dataProvider,
    ]);
}

 public function actionBlock()
{
    $searchModel = new UserTable();

    // Passiamo i queryParams come sempre
    $params = Yii::$app->request->queryParams;

    // Aggiungiamo il filtro per l'utente loggato
    $params['UserTable']['bocco'] =0;

    $dataProvider = $searchModel->search($params);

    return $this->render('index', [
        'searchModel' => $searchModel,
        'dataProvider' => $dataProvider,
    ]);
}


public function actionApprove($id)
{
    $user=UserTable::findOne($id);
     

      
    $function=new userService();

    if($function->approva($user->username))
        {
            Yii::$app->session->setFlash('success','Operatore approvato correttamente');
            return $this->redirect(['attese']);
        }else{
                        Yii::$app->session->setFlash('success','Operatore approvato correttamente');
                        return $this->redirect(['attese']);
        }

          return $this->redirect(['attese']);
    }

    public function actionRitira($id)
    {
         $user=UserTable::findOne($id);
     

      
    $function=new userService();

    if($function->ritira($user->username))
        {
            Yii::$app->session->setFlash('success','Operatore approvato correttamente');
            return $this->redirect(['attese']);
        }else{
                        Yii::$app->session->setFlash('success','Operatore approvato correttamente');
                        return $this->redirect(['attese']);
        }

          return $this->redirect(['attese']);
    }

    public function actionResetLogin($id)
    {
        $user=User::findOne($id);
        $function=new userService();

        if($user && $function->resetLogin($user->username)){
            Yii::$app->session->setFlash('success','Reset del login  effettuato correttamente');
        }else{
             Yii::$app->session->setFlash('error','Reset del login non effettuato correttamente');
        }

        return $this->redirect(Yii::$app->request->referrer ?: ['index']);
    }

public function actionViewOperatori()
{
    $searchModel = new \app\models\UserTable();

    // Prendiamo i query params (per paginazione / filtri)
    $params = Yii::$app->request->queryParams;

    // Costruiamo il dataProvider tramite il search model
    $dataProvider = $searchModel->search($params);

    // Applichiamo il filtro sui ruoli direttamente alla query del dataProvider
    // così non dipendiamo da come UserTable::search gestisce il valore di 'ruolo'
    $roles = ['developer', 'ict', 'amministratore'];
    $query = $dataProvider->query;
    $query->andWhere(['ruolo' => $roles]);

    // Se vuoi, puoi anche impostare un titolo o una modalità per la view
    $title = 'Operatori (developer, ict, amministratore)';

    return $this->render('index', [
        'searchModel'  => $searchModel,
        'dataProvider' => $dataProvider,
        'mode'         => 'operators',
        'title'        => $title,
    ]);
}

public function actionVerifyRuolo()
{
    $searchModel = new \app\models\UserTable();

    // Prendiamo i query params (per paginazione / filtri)
    $params = Yii::$app->request->queryParams;

    // Costruiamo il dataProvider tramite il search model
    $dataProvider = $searchModel->search($params);

    // Applichiamo il filtro sui ruoli direttamente alla query del dataProvider
    // così non dipendiamo da come UserTable::search gestisce il valore di 'ruolo'
    $roles = ['developer', 'ict', 'amministratore'];
    $query = $dataProvider->query;
    $query->andWhere(['ruolo' => $roles]);

    // Se vuoi, puoi anche impostare un titolo o una modalità per la view
    $title = 'Operatori (developer, ict, amministratore)';

    return $this->render('index', [
        'searchModel'  => $searchModel,
        'dataProvider' => $dataProvider,
        'mode'         => 'operators',
        'title'        => $title,
    ]);

}

}
