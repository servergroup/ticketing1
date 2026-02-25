<?php

namespace app\controllers;
use Yii;
use app\models\Assegnazioni;
use app\models\assegnazioniTable;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * AeegnazioniController implements the CRUD actions for Assegnazioni model.
 */
class AssegnazioniController extends Controller
{
    /**
     * @inheritDoc
     */
    public function behaviors()
    {
        return array_merge(
            parent::behaviors(),
            [
                'verbs' => [
                    'class' => VerbFilter::className(),
                    'actions' => [
                        'delete' => ['POST'],
                    ],
                ],
            ]
        );
    }

    /**
     * Lists all Assegnazioni models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new assegnazioniTable();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Assegnazioni model.
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
     * Creates a new Assegnazioni model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
 

    /**
     * Updates an existing Assegnazioni model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Assegnazioni model.
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
     * Finds the Assegnazioni model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return Assegnazioni the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Assegnazioni::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('Questa pagina non esiste.');
    }

    public function actionMyTicket()
{
       $searchModel = new assegnazioniTable();

    // Passiamo i queryParams come sempre
    $params = Yii::$app->request->queryParams;

    // Aggiungiamo il filtro per l'utente loggato
    $params['assegnazioniTable']['id_operatore'] =Yii::$app->user->identity->id;

    $dataProvider = $searchModel->search($params);

    return $this->render('index', [
        'searchModel' => $searchModel,
        'dataProvider' => $dataProvider,
    ]);
}

    public function actionMyReparto()
{
       $searchModel = new assegnazioniTable();

    // Passiamo i queryParams come sempre
    $params = Yii::$app->request->queryParams;

    // Aggiungiamo il filtro per l'utente loggato
    if(Yii::$app->user->identity->ruolo=='developer'){
    $params['assegnazioniTable']['ambito']='sviluppo';
    }else if(Yii::$app->user->identity->ruolo=='ict'){
        $params['assegnazioniTable']['ambito']='ict';
    }
    $dataProvider = $searchModel->search($params);

    return $this->render('index', [
        'searchModel' => $searchModel,
        'dataProvider' => $dataProvider,
    ]);
}
}
