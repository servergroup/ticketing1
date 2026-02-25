<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
?>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<?php
if (Yii::$app->session->hasFlash('success')) {
    $msg = Yii::$app->session->getFlash('success');
    $this->registerJs("
        Swal.fire({
            title: '$msg',
            icon: 'success',
            confirmButtonText: 'OK'
        });
    ");
}

if (Yii::$app->session->hasFlash('error')) {
    $msg = Yii::$app->session->getFlash('error');
    $this->registerJs("
        Swal.fire({
            title: '$msg',
            icon: 'error',
            confirmButtonText: 'OK'
        });
    ");
}
?>

<div class="ds-container">
    <div class="ds-card">
        <h1 class="ds-title">Modifica Password</h1>
        <p class="ds-subtitle">Aggiorna la password del tuo account in modo sicuro</p>

        <?php $form = ActiveForm::begin(); ?>

            <?= $form->field($tokenUser, 'email')->hiddenInput([
                'value' => $tokenUser->email
            ])->label(false) ?>

            <?= $form->field($tokenUser, 'password')->passwordInput([
                
                'class' => 'ds-input'
            ])->label('Nuova password') ?>

            <div class="form-group">
                <?= Html::submitButton('Modifica la password', [
                    'class' => 'ds-btn'
                ]) ?>
            </div>

        <?php ActiveForm::end(); ?>
    </div>
</div>
