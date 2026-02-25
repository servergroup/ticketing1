<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\Assegnazioni $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="assegnazioni-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'codice_ticket')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'id_operatore')->textInput() ?>

    <?= $form->field($model, 'ambito')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
