<?php

use app\models\Assegnazioni;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var app\models\assegnazioniTable $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Assegnazioni';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="assegnazioni-index">

    <h1><?= Html::encode($this->title) ?></h1>



    <?php // echo $this->render('_search', ['model' => $searchModel]); 
    ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
           
            'id',
            'codice_ticket',
            'id_operatore',
            'ambito',
            [
                'class' => ActionColumn::className(),
                'urlCreator' => function ($action, Assegnazioni $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'id' => $model->id]);
                }
            ],
        ],
    ]) ?>



</div>