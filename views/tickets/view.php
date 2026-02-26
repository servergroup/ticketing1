<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var app\models\Ticket $model */

$this->title = 'Ticket ' . $model->codice_ticket;
$this->params['breadcrumbs'][] = ['label' => 'Ticket', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="page-shell">
    <div class="page-head">
        <div>
            <h1 class="page-title"><?= Html::encode($this->title) ?></h1>
            <p class="page-subtitle">Dettaglio completo e comunicazione rapida collegata al ticket.</p>
        </div>
        <div class="page-actions">
            <?= Html::a('Torna ai ticket', ['tickets/index'], ['class' => 'btn btn-sm btn-outline-secondary']) ?>
            <?= Html::a('Invia messaggio', ['messages/compose', 'ticketId' => $model->id], ['class' => 'btn btn-sm btn-success']) ?>
        </div>
    </div>

    <div class="detail-card">
        <?= DetailView::widget([
            'model' => $model,
            'attributes' => [
                'id',
                'codice_ticket',
                'problema:ntext',
                'reparto',
                'priorita',
                'stato',
                'scadenza',
                'id_cliente',
                'data_invio',
            ],
        ]) ?>
    </div>
</div>

