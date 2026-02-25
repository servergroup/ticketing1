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

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Sei sicuro di voler eliminare dodesto ticket?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?php if(Yii::$app->user->identity->ruolo=='cliente'):?>
    <?= DetailView::widget([
        'model' => $model,
        
        'attributes' => [
            'id',
            'problema',
            'ambito',
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
            'ambito',
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
