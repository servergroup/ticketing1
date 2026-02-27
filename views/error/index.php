<?php

use app\models\ErrorLog;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Error Logs';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="error-log-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Error Log', ['create'], ['class' => 'btn btn-success']) ?>
    </p>


    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'type',
            'message:ntext',
            'code',
            'file',
            //'line',
            //'trace:ntext',
            //'url:url',
            //'user_id',
            //'user_ip',
            //'request_method',
            //'status_code',
            //'is_handled',
            //'created_at',
            [
                'class' => ActionColumn::className(),
                'urlCreator' => function ($action, ErrorLog $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'id' => $model->id]);
                 }
            ],
        ],
    ]); ?>


</div>
