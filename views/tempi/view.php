<?php

use yii\helpers\Html;
use yii\helpers\Json;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var app\models\TempiTicket $model */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Tempi Tickets', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="tempi-ticket-view">

    <h1><?= Html::encode($this->title) ?></h1>

     
    <?= DetailView::widget([ 
        'model' => $model, 
        'attributes' => [ 
            'id', 'id_ticket', 'id_operatore', 'ora_inizio',
            // ... 
            [
                 'attribute' => 'tempi_pause', 
                 'label' => 'Tempi Pause (JSON)', 
                 'value' => function($model) { 
                    $val = $model->tempi_pause; 
                    if (is_array($val)) { 
                        return Json::encode($val); 
                        } 
                        return (string)$val; },
                         'format' => 'ntext', 
                         // o 'raw' se già codificato/escaped 
                         ], 
                         ], 
                         ]) ?> 

</div>
