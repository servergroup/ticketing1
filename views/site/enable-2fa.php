<?php
use yii\helpers\Html;

$this->title = 'Attiva Autenticazione a Due Fattori';
?>

<div class="container mt-5" style="max-width: 480px;">
    <div class="card shadow-sm">
        <div class="card-body text-center">

            <h3 class="mb-3">Attiva la 2FA</h3>

            <p class="text-muted">
                Scansiona questo QR code con Google Authenticator o un'altra app compatibile.
            </p>

            <div class="mb-3">
                <img src="<?= $qrCode ?>" alt="QR Code 2FA" class="img-fluid">
            </div>

            <p class="text-muted">
                Se non puoi scansionare il QR, inserisci manualmente questo codice:
            </p>

            <code style="font-size: 1.2em;"><?= $user->totp_secret ?></code>

            <div class="mt-4">
                <?= Html::a('Ho scansionato il QR, procedi', ['verify-2fa'], ['class' => 'btn btn-primary']) ?>
            </div>

        </div>
    </div>
</div>
