<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\Ticket $ticket */

$this->title = 'Modifica ticket ' . $ticket->codice_ticket;
$this->params['breadcrumbs'][] = ['label' => 'Ticket', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="page-shell page-shell--narrow ticket-form-page">
    <div class="page-head">
        <div>
            <h1 class="page-title"><?= Html::encode($this->title) ?></h1>
            <p class="page-subtitle">Aggiorna i dati del ticket mantenendo la tracciabilita operativa.</p>
        </div>
    </div>

    <div class="form-card">
        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($ticket, 'problema')->textarea(['rows' => 5]) ?>

        <div class="row">
            <div class="col-md-4">
                <?= $form->field($ticket, 'reparto')->dropDownList([
                    'ict' => 'Sistemistica (ICT)',
                    'sviluppo' => 'Sviluppo',
                ], ['prompt' => 'Seleziona reparto']) ?>
            </div>
            <div class="col-md-4">
                <?= $form->field($ticket, 'priorita')->dropDownList([
                    'alta' => 'Alta',
                    'media' => 'Media',
                    'bassa' => 'Bassa',
                ], ['prompt' => 'Seleziona priorita']) ?>
            </div>
            <?php if (Yii::$app->user->identity->ruolo === 'amministratore'): ?>
                <div class="col-md-4">
                    <?= $form->field($ticket, 'scadenza')->input('date') ?>
                </div>
            <?php endif; ?>
        </div>

        <div class="d-flex gap-2">
            <?= Html::submitButton('Salva modifiche', ['class' => 'btn btn-primary']) ?>
            <?= Html::a('Annulla', ['tickets/view', 'id' => $ticket->id], ['class' => 'btn btn-outline-secondary']) ?>
        </div>

        <?php ActiveForm::end(); ?>
    </div>
</div>

