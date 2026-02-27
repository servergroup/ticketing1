<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\TicketMessage $model */
/** @var array $recipientOptions */
/** @var array $ticketOptions */
/** @var string $recipientHint */

$this->title = 'Nuovo messaggio';
$this->params['breadcrumbs'][] = 'Messaggi';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="page-shell message-compose">
    <div class="page-head">
        <div>
            <h1 class="page-title"><?= Html::encode($this->title) ?></h1>
            <p class="page-subtitle">Messaggistica interna tra clienti, operatori e amministratori.</p>
        </div>
        <div class="page-actions">
            <?= Html::a('Inbox', ['index'], ['class' => 'btn btn-sm btn-outline-primary']) ?>
            <?= Html::a('Messaggi inviati', ['index', 'box' => 'sent'], ['class' => 'btn btn-sm btn-outline-secondary']) ?>
        </div>
    </div>

    <div class="form-card">
        <?php $form = ActiveForm::begin(); ?>

        <div class="row">
            <div class="col-lg-6">
                <?= $form->field($model, 'recipient_id')->dropDownList($recipientOptions, [
                    'prompt' => 'Seleziona destinatario',
                    'class' => 'form-control form-select',
                ]) ?>
                <p class="form-text text-muted mb-3"><?= Html::encode($recipientHint) ?></p>
            </div>
            <div class="col-lg-6">
                <?= $form->field($model, 'ticket_id')->dropDownList($ticketOptions, [
                    'prompt' => 'Nessun ticket specifico',
                    'class' => 'form-control form-select',
                ]) ?>
            </div>
        </div>

        <?= $form->field($model, 'subject')->textInput([
            'maxlength' => true,
            'placeholder' => 'Oggetto del messaggio',
        ]) ?>

        <?= $form->field($model, 'body')->textarea([
            'rows' => 8,
            'placeholder' => 'Scrivi il messaggio da inviare',
        ]) ?>

        <div class="d-flex gap-2">
            <?= Html::submitButton('Invia messaggio', ['class' => 'btn btn-primary']) ?>
            <?= Html::a('Annulla', ['index'], ['class' => 'btn btn-outline-secondary']) ?>
        </div>

        <?php ActiveForm::end(); ?>
    </div>
</div>
