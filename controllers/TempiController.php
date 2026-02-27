<?php

namespace app\controllers;

use Yii;
use app\models\TempiTicket;
use app\models\TempiTable;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;

/**
 * TempiController implements the CRUD actions for TempiTicket model.
 */
class TempiController extends Controller
{
    /**
     * @inheritDoc
     */
    public function behaviors()
    {
        return [
            // ACCESS CONTROL - Solo operatori (developer, ict)
            'access' => [
                'class' => AccessControl::class,
                'only' => ['index', 'view', 'delete', 'findModel'],
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['index', 'view', 'delete', 'findModel'],
                        'roles' => ['@'],
                        'matchCallback' => function () {
                            $ruolo = Yii::$app->user->identity->ruolo;
                            $approvazione = Yii::$app->user->identity->approvazione;
                            return in_array($ruolo, ['developer', 'ict']) && $approvazione == 1;
                        },
                        'denyCallback' => function () {
                            $approvazione = Yii::$app->user->identity->approvazione;
                            if ($approvazione != 1) {
                                Yii::$app->session->setFlash('error', 'Attendere l\'approvazione da parte di un amministratore.');
                            } else {
                                Yii::$app->session->setFlash('error', 'Non hai i permessi per accedere a questa sezione.');
                            }
                            return $this->goHome();
                        }
                    ],
                ],
            ],
            // VERB FILTER
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all TempiTicket models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new TempiTable();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single TempiTicket model.
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
     * Deletes an existing TempiTicket model.
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
     * Finds the TempiTicket model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return TempiTicket the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = TempiTicket::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}