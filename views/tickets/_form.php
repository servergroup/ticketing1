<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\Ticket $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="form-card">
    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'problema')->textarea([
        'rows' => 6,
        'placeholder' => 'Descrivi il problema in modo chiaro e completo',
    ]) ?>

    <div class="row">
        <div class="col-md-4">
            <?= $form->field($model, 'reparto')->dropDownList([
                'ict' => 'Sistemistica (ICT)',
                'sviluppo' => 'Sviluppo',
            ], ['prompt' => 'Seleziona reparto']) ?>
        </div>
        <div class="col-md-4">
            <?= $form->field($model, 'priorita')->dropDownList([
                'alta' => 'Alta',
                'media' => 'Media',
                'bassa' => 'Bassa',
            ], ['prompt' => 'Seleziona priorita']) ?>
        </div>
        <div class="col-md-4">
            <?= $form->field($model, 'stato')->dropDownList([
                'aperto' => 'Aperto',
                'in lavorazione' => 'In lavorazione',
                'chiuso' => 'Chiuso',
                'scaduto' => 'Scaduto',
            ], ['prompt' => 'Seleziona stato']) ?>
        </div>
    </div>

    <?php if (Yii::$app->user->identity->ruolo === 'amministratore'): ?>
        <div class="row">
            <div class="col-md-4">
                <?= $form->field($model, 'scadenza')->input('date') ?>
            </div>
        </div>
    <?php endif; ?>

    <div class="d-flex gap-2">
        <?= Html::submitButton('Salva ticket', ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Annulla', ['tickets/index'], ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>

