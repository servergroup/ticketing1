<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
?>

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<?php
if (Yii::$app->session->hasFlash('success')) {
    $msg = Yii::$app->session->getFlash('success');
    $this->registerJs("
        Swal.fire({
            title: 'Completato!',
            text: '$msg',
            icon: 'success',
            confirmButtonText: 'OK',
            confirmButtonColor: '#198754',
            timer: 5000,
            timerProgressBar: true
        });
    ");
}

if (Yii::$app->session->hasFlash('error')) {
    $msg = Yii::$app->session->getFlash('error');
    $this->registerJs("
        Swal.fire({
            title: 'Attenzione!',
            text: '$msg',
            icon: 'error',
            confirmButtonText: 'OK',
            confirmButtonColor: '#dc3545',
            timer: 5000,
            timerProgressBar: true
        });
    ");
}
?>

<!-- Breadcrumb -->
<div class="breadcrumb-container">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= Url::to(['/site/index']) ?>"><i class="bi bi-house-door"></i> Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">Modifica Password</li>
        </ol>
    </nav>
</div>

<!-- Main Content -->
<div class="auth-container">
    <div class="auth-card">
        <!-- Card Header -->
        <div class="auth-header">
            <div class="auth-icon">
                <i class="bi bi-shield-lock"></i>
            </div>
            <h1 class="auth-title">Modifica Password</h1>
            <p class="auth-subtitle">Inserisci la nuova password per il tuo account</p>
        </div>

        <!-- Card Body -->
        <div class="auth-body">
            <?php $form = ActiveForm::begin([
                'id' => 'password-form',
                'options' => ['class' => 'auth-form'],
                'fieldConfig' => [
                    'template' => "{label}\n{input}\n{error}",
                    'labelOptions' => ['class' => 'form-label'],
                    'errorOptions' => ['class' => 'invalid-feedback d-block'],
                ],
            ]); ?>

                <?= $form->field($tokenUser, 'email')->hiddenInput()->label(false) ?>

                <div class="mb-4">
                    <?= $form->field($tokenUser, 'password')->passwordInput([
                        'class' => 'form-control form-control-lg',
                        'id' => 'password-input',
                        'value' => '', // <-- CAMPO VUOTO
                        'autocomplete' => 'new-password',
                        'placeholder' => 'Inserisci nuova password'
                    ])->label('Nuova Password', ['class' => 'form-label fw-medium']) ?>
                </div>

                <div class="d-grid gap-2">
                    <?= Html::submitButton('<i class="bi bi-check-lg me-2"></i>Conferma Nuova Password', [
                        'class' => 'btn btn-primary btn-lg fw-medium'
                    ]) ?>
                </div>

            <?php ActiveForm::end(); ?>
        </div>

        <!-- Card Footer -->
        <div class="auth-footer">
            <p>Hai cambiato idea? <a href="<?= Url::to(['/site/index']) ?>">Torna alla home</a></p>
        </div>
    </div>
</div>

<style>
    /* Reset e variabili */
    :root {
        --primary-color: #0d6efd;
        --primary-dark: #0b5ed7;
        --secondary-color: #6c757d;
        --success-color: #198754;
        --danger-color: #dc3545;
        --warning-color: #ffc107;
        --light-bg: #f8f9fa;
        --white: #ffffff;
        --border-color: #dee2e6;
        --text-primary: #212529;
        --text-secondary: #6c757d;
        --shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        --shadow-sm: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    }

    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background: linear-gradient(135deg, #f5f7fa 0%, #e4e8ec 100%);
        min-height: 100vh;
    }

    /* Breadcrumb */
    .breadcrumb-container {
        background: var(--white);
        padding: 0.75rem 2rem;
        border-bottom: 1px solid var(--border-color);
    }

    .breadcrumb {
        margin-bottom: 0;
        font-size: 0.875rem;
    }

    .breadcrumb a {
        color: var(--primary-color);
        text-decoration: none;
    }

    .breadcrumb a:hover {
        text-decoration: underline;
    }

    /* Container principale */
    .auth-container {
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: calc(100vh - 200px);
        padding: 2rem;
    }

    /* Card di autenticazione */
    .auth-card {
        background: var(--white);
        border-radius: 1rem;
        box-shadow: var(--shadow);
        width: 100%;
        max-width: 480px;
        overflow: hidden;
    }

    .auth-header {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        padding: 2rem;
        text-align: center;
        border-bottom: 1px solid var(--border-color);
    }

    .auth-icon {
        width: 70px;
        height: 70px;
        background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 1rem;
    }

    .auth-icon i {
        font-size: 2rem;
        color: var(--white);
    }

    .auth-title {
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--text-primary);
        margin-bottom: 0.5rem;
    }

    .auth-subtitle {
        color: var(--text-secondary);
        font-size: 0.9375rem;
        margin: 0;
    }

    /* Body della card */
    .auth-body {
        padding: 2rem;
    }

    .auth-form .form-control {
        border: 2px solid var(--border-color);
        border-radius: 0.5rem;
        padding: 0.75rem 1rem;
        font-size: 1rem;
        transition: all 0.3s ease;
    }

    .auth-form .form-control:focus {
        border-color: var(--primary-color);
        box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.15);
    }

    .auth-form .form-label {
        color: var(--text-primary);
        font-weight: 500;
        margin-bottom: 0.5rem;
    }

    .auth-form .invalid-feedback {
        font-size: 0.8125rem;
        margin-top: 0.25rem;
    }

    /* Button */
    .btn-primary {
        background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
        border: none;
        border-radius: 0.5rem;
        padding: 0.875rem 1.5rem;
        font-weight: 600;
        letter-spacing: 0.25px;
        transition: all 0.3s ease;
    }

    .btn-primary:hover {
        background: linear-gradient(135deg, var(--primary-dark) 0%, #0a58ca 100%);
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(13, 110, 253, 0.3);
    }

    /* Footer card */
    .auth-footer {
        background: #f8f9fa;
        padding: 1rem 2rem;
        text-align: center;
        border-top: 1px solid var(--border-color);
    }

    .auth-footer p {
        margin: 0;
        font-size: 0.875rem;
        color: var(--text-secondary);
    }

    .auth-footer a {
        color: var(--primary-color);
        text-decoration: none;
        font-weight: 500;
    }

    .auth-footer a:hover {
        text-decoration: underline;
    }

    /* Footer aziendale */
    .company-footer {
        background: #1e3a5f;
        color: var(--white);
        padding: 1.5rem 2rem;
        text-align: center;
    }

    .footer-content p {
        margin: 0;
        font-size: 0.875rem;
        opacity: 0.9;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .auth-container {
            padding: 1rem;
        }

        .auth-card {
            max-width: 100%;
        }

        .auth-header,
        .auth-body,
        .auth-footer {
            padding: 1.5rem;
        }
    }
</style>