<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $user app\models\User */
/* @var $form yii\widgets\ActiveForm */
?>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<?php
// SweetAlert SUCCESS
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

// SweetAlert ERROR
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


    <div class="register-card shadow-lg">
        <h2 class="register-title">Recupero della password</h2>
        <p class="register-subtitle">Inserisci la tua mail per ottenere il link relativo alla modifica della password</p>

        <?php $form = ActiveForm::begin(); ?>

            <?= $form->field($user,'email')->input('email') ?>

            <div class="form-group mt-4">
                <?= Html::submitButton('Invia', ['class' => 'btn btn-dataseed']) ?>
            </div>

        <?php ActiveForm::end(); ?>
    </div>


<style>

    label{
        text-align: center;
    }
/* Container centrale */
.register-admin-container {
    display: flex;
    justify-content: center;
    align-items: center;
    padding-top: 40px;
    padding-bottom: 40px;
   
}

/* Card */
.register-card {
    background: white;
    border-radius: 12px;
    padding: 35px 40px;
    width: 100%;
    max-width: 480px;
    border: 0.5px solid #e5e5e5;
    
    /* Centratura */
    margin: 0 auto;
}

/* Il container padre deve avere: */
body {
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 100vh;
}

/* Titolo */
.register-title {
    font-size: 26px;
    font-weight: 700;
    color: #1a2a3a;
    margin-bottom: 10px;
}

/* Sottotitolo */
.register-subtitle {
    font-size: 15px;
    color: #6c7a89;
    margin-bottom: 25px;
}

/* Pulsante aziendale */
.btn-dataseed {
    background: linear-gradient(135deg, #0066cc, #0099ff);
    border: none;
    color: #fff;
    font-weight: 600;
    padding: 10px 22px;
    border-radius: 8px;
    transition: 0.2s ease;
    width: 100%;
}

.btn-dataseed:hover {
    background: linear-gradient(135deg, #005bb5, #0088e6);
    color: #fff;
}

/* Input */
.form-control {
    border-radius: 8px !important;
    border: 1px solid #cfd6dd;
}

.form-control:focus {
    border-color: #0099ff;
    box-shadow: 0 0 0 0.2rem rgba(0,153,255,0.25);
}
</style>