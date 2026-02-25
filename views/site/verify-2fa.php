<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = 'Verifica codice 2FA';
?>

<div class="container mt-5" style="max-width: 420px;">
    <div class="card shadow-sm">
        <div class="card-body">

            <h3 class="text-center mb-3">Autenticazione a due fattori</h3>
            <p class="text-muted text-center">
                Inserisci il codice generato dalla tua app Google Authenticator.
            </p>

            <?php $form = ActiveForm::begin(); ?>

                <?= $form->field($model, 'code')->textInput([
                    'maxlength' => true,
                    'class' => 'form-control text-center',
                    'placeholder' => 'Codice a 6 cifre',
                    'autocomplete' => 'one-time-code'
                ]) ?>

                <div class="d-grid gap-2 mt-3">
                    <?= Html::submitButton('Verifica', ['class' => 'btn btn-primary btn-block']) ?>
                </div>

            <?php ActiveForm::end(); ?>

            <div class="text-center mt-3">
                <?= Html::a('Torna al login', ['site/login'], ['class' => 'text-decoration-none']) ?>
            </div>

        </div>
    </div>
</div>
