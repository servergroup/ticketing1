<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\TempiTable $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="tempi-ticket-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'id_ticket') ?>

    <?= $form->field($model, 'id_operatore') ?>

    <?= $form->field($model, 'ora_inizio') ?>

    <?= $form->field($model, 'ora_fine') ?>

    <?php // echo $form->field($model, 'tempo_lavorazione') ?>

    <?php // echo $form->field($model, 'pause_effettuate') ?>

    <?php // echo $form->field($model, 'tempi_pause') ?>

    <?php // echo $form->field($model, 'ora_pause') ?>

    <?php // echo $form->field($model, 'chiuso_il') ?>

    <?php // echo $form->field($model, 'stato') ?>

    <?php // echo $form->field($model, 'tempo_sospensione') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
