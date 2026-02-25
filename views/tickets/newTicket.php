<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\assets\LogAsset;

/** @var yii\web\View $this */
/** @var app\models\Ticket $ticket*/

$this->title = '';
LogAsset::register($this);


?>


<?php


?>
<div class="ticket-newTicket">

    <h1>Nuovo Ticket</h1>

    <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($ticket, 'problema')->textarea(['placeholder' => 'Descrivi il problema']) ?>

        <?= $form->field($ticket, 'ambito')->dropDownList([
            'ict' => 'Sistemistica',
            'sviluppo' => 'Sviluppo',
            
        ], ['prompt' => 'Seleziona ambito']) ?>

               <?= $form->field($ticket, 'priorita')->dropDownList([
            'bassa' => 'Bassa',
            'media' => 'Media',
            'alta'=>'Alta'
        ], ['prompt' => 'Seleziona una priortià']) ?>


       

        <?= $form->field($ticket, 'id_cliente')->hiddenInput()->label(false) ?>

        <div class="form-group">
            <?= Html::submitButton('Invia Ticket', ['class' => 'btn btn-primary']) ?>
        </div>

    <?php ActiveForm::end(); ?>

</div><!-- ticket-newTicket -->

<style>
body {
    background: #f4f6f9;
    font-family: "Segoe UI", Roboto, Arial, sans-serif;
}

/* ====== Contenitore principale ====== */
.ticket-newTicket {
    width: 480px;
    margin: 60px auto;
    background: #ffffff;
    border: 1px solid #e1e1e1;
    border-radius: 12px;
    box-shadow: 0 4px 18px rgba(0,0,0,0.08);
    padding: 30px;
}

/* ====== Titolo ====== */
.ticket-newTicket h1 {
    font-size: 20px;
    font-weight: 600;
    color: #2c3e50;
    text-align: center;
    margin-bottom: 25px;
}

/* ====== Input, select e textarea ====== */
.ticket-newTicket .form-control {
    border-radius: 6px;
    border: 1px solid #cfcfcf;
    padding: 10px;
    font-size: 15px;
    transition: border-color 0.2s ease, box-shadow 0.2s ease;
}

.ticket-newTicket .form-control:focus {
    border-color: #0066cc;
    box-shadow: 0 0 0 3px rgba(0,102,204,0.15);
}

/* ====== Label ====== */
.ticket-newTicket .control-label {
    font-weight: 500;
    color: #2c3e50;
}

/* ====== Pulsante ====== */
.ticket-newTicket .btn-primary {
    background-color: #0066cc;
    border-color: #005bb5;
    padding: 10px 18px;
    font-size: 15px;
    border-radius: 6px;
    width: 100%;
    transition: all 0.25s ease-in-out;
}

.ticket-newTicket .btn-primary:hover {
    background-color: #005bb5;
    border-color: #004f9e;
    transform: translateY(-2px);
    box-shadow: 0 6px 14px rgba(0, 102, 204, 0.25);
}

/* ====== Spaziatura campi ====== */
.ticket-newTicket .form-group {
    margin-bottom: 18px;
}
</style