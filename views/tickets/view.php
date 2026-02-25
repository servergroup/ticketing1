<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var app\models\Ticket $model */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Tickets', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="ticket-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= Html::a('Torna al ticket assegnati',['assegnazioni/index']) ?>
    <p>
        
        
    </p>

    <?php if(Yii::$app->user->identity->ruolo=='cliente'):?>
    <?= DetailView::widget([
        'model' => $model,
        
        'attributes' => [
            'id',
            'problema',
            'reparto',
            'codice_ticket',
            'stato',
            'data_invio',
            'priorita',
        ],
    ]) ?>

       <?php else:  ?>

        <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'problema',
            'reparto',
            'codice_ticket',
            'stato',
            'scadenza',
            'id_cliente',
            'data_invio',
            'priorita',
        ],
    ]) ?>
        <?php endif; ?>

</div>
