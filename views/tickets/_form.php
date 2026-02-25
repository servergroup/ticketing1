<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\Ticket $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="ticket-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'problema')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'ambito')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'codice_ticket')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'stato')->textInput(['maxlength' => true]) ?>

    <?php if(Yii::$app->user->identity->ruolo=='amministratore'):?>
         <?= $form->field($model, 'scadenza')->textInput() ?>
    
        <?php endif;?>
    
       



    <?= $form->field($model, 'priorita')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
