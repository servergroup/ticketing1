<?php
use yii\helpers\Html;
use app\assets\LogAsset;
use yii\web\View;

$this->title = '';
LogAsset::register($this);
?>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


<div class="login-box">
    <div class="card">
        <div class="login-card-body">

            <p class="login-box-msg">
               <img src="<?= Yii::getAlias('@web/img/taglio_dataseed.png') ?>">
            </p>

            <h1 class="text-center mb-4">Accedi</h1>

            <?php $form = \yii\bootstrap4\ActiveForm::begin(['id' => 'login-form']) ?>

            <!-- USERNAME -->
            <?= $form->field($model, 'username', [
                'template' => '{beginWrapper}{input}{icon}{error}{endWrapper}',
                'wrapperOptions' => ['class' => 'input-group mb-3'],
                'parts' => [
                    '{icon}' => '
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-user"></span>
                            </div>
                        </div>'
                ]
            ])->label(false)->textInput(['placeholder' => 'Username']) ?>

            <!-- PASSWORD -->
            <?= $form->field($model, 'password', [
                'template' => '{beginWrapper}{input}{icon}{error}{endWrapper}',
                'wrapperOptions' => ['class' => 'input-group mb-3'],
                'parts' => [
                    '{icon}' => '
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-lock"></span>
                            </div>
                        </div>'
                ]
            ])->label(false)->passwordInput(['placeholder' => 'Password']) ?>

            <div class="text-center mt-3">
                <?= Html::submitButton('Accedi', [
                    'class' => 'btn btn-primary btn-block'
                ]) ?>
            </div>

            <div class="text-center mt-3">
                <?= Html::a('Hai dimenticato la password?', ['site/mail']) ?>
            </div>

            <div class="text-center mt-2">
                <?= Html::a('Non sei registrato?', ['site/register']) ?>
            </div>

            <?php \yii\bootstrap4\ActiveForm::end(); ?>

        </div>
    </div>
</div>

<style>

/* ===== Layout generale ===== */
body.login-page {
    background-color: #f4f6f9;
    font-family: "Segoe UI", Roboto, Arial, sans-serif;
    margin: 0;
    min-height: 100vh;

    /* Flex per centro verticale + orizzontale */
    display: flex;
    justify-content: center;
    align-items: center;
}

/* ===== Box login ===== */
.login-box {
    width: 420px;
    max-width: 90%;
    margin: 0 auto;
    margin-top:150px;
}

/* ===== Card ===== */
.card {
    background-color: #ffffff;
    border-radius: 12px;
    border: 1px solid #e1e1e1;
    box-shadow: 0 4px 18px rgba(0, 0, 0, 0.08);
}

/* ===== Contenuto card ===== */
.login-card-body {
    padding: 30px;
}

/* ===== Logo ===== */
.login-box-msg {
    text-align: center;
    margin-bottom: 20px;
}

.login-box-msg img {
    width: 110px;
    height: auto;
}

/* ===== Input ===== */
.input-group .form-control {
    border-radius: 6px 0 0 6px;
    border: 1px solid #cfcfcf;
    padding: 10px;
    font-size: 15px;
}

.input-group-text {
    background-color: #f0f0f0;
    border: 1px solid #cfcfcf;
    border-left: none;
    border-radius: 0 6px 6px 0;
}

/* Rimuove icone di validazione Bootstrap */
.form-control.is-valid,
.form-control.is-invalid {
    background-image: none !important;
}

/* ===== Pulsante ===== */
.btn-primary {
    background-color: #0066cc;
    border-color: #005bb5;
    padding: 10px 18px;
    font-size: 15px;
    border-radius: 6px;
    transition: all 0.25s ease;
}

.btn-primary:hover {
    background-color: #005bb5;
    border-color: #004f9e;
    transform: translateY(-2px);
    box-shadow: 0 6px 14px rgba(0, 102, 204, 0.25);
}

/* ===== Link ===== */
.login-card-body a {
    color: #0066cc;
    font-weight: 500;
    text-decoration: none;
}

.login-card-body a:hover {
    text-decoration: underline;
}

/* ===== Responsive ===== */
@media (max-width: 480px) {
    .login-card-body {
        padding: 20px;
    }
}


</style>
