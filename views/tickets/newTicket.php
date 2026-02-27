<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\ticketfunction $ticket */

$this->title = 'Nuovo ticket';
$this->params['breadcrumbs'][] = ['label' => 'Ticket', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="page-shell page-shell--narrow ticket-form-page">
    <div class="page-head">
        <div>
            <h1 class="page-title"><?= Html::encode($this->title) ?></h1>
            <p class="page-subtitle">Apri una richiesta di assistenza e indirizzala al reparto corretto.</p>
        </div>
        <div class="page-actions">
            <?= Html::a('I miei ticket', ['tickets/my-ticket'], ['class' => 'btn btn-sm btn-outline-secondary']) ?>
        </div>
    </div>

    <div class="form-card">
        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($ticket, 'problema')->textarea([
            'rows' => 6,
            'placeholder' => 'Descrivi in dettaglio il problema riscontrato',
        ]) ?>

        <div class="row">
            <div class="col-md-4">
                <?= $form->field($ticket, 'reparto')->dropDownList([
                    'ict' => 'Sistemistica (ICT)',
                    'sviluppo' => 'Sviluppo',
                ], ['prompt' => 'Seleziona reparto']) ?>
            </div>
            <div class="col-md-4">
                <?= $form->field($ticket, 'priorita')->dropDownList([
                    'bassa' => 'Bassa',
                    'media' => 'Media',
                    'alta' => 'Alta',
                ], ['prompt' => 'Seleziona priorita']) ?>
            </div>
            <div class="col-md-4">
                <?= $form->field($ticket, 'scadenza')->input('date') ?>
            </div>
        </div>

        <?= $form->field($ticket, 'id_cliente')->hiddenInput()->label(false) ?>

        <div class="d-flex gap-2">
            <?= Html::submitButton('Invia ticket', ['class' => 'btn btn-primary']) ?>
            <?= Html::a('Annulla', ['tickets/index'], ['class' => 'btn btn-outline-secondary']) ?>
        </div>

        <?php ActiveForm::end(); ?>
    </div>
</div>

