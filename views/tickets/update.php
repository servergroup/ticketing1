<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\Ticket $model */

$this->title = 'Aggiorna ticket ' . $model->codice_ticket;
$this->params['breadcrumbs'][] = ['label' => 'Ticket', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => 'Dettaglio ' . $model->codice_ticket, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="page-shell page-shell--narrow ticket-update">
    <div class="page-head">
        <div>
            <h1 class="page-title"><?= Html::encode($this->title) ?></h1>
            <p class="page-subtitle">Aggiorna dati ticket mantenendo lo standard operativo aziendale.</p>
        </div>
    </div>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>

