<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $account app\models\User */

$imageUrl = $account->immagine
    ? Yii::getAlias('@web/img/upload/' . $account->immagine)
    : Yii::getAlias('@web/img/profile.png');
?>

<div class="profile-shell">

    <!-- PROFILE HEADER -->
    <div class="profile-head">
        <div class="profile-avatar-wrap">
            <img src="<?= Html::encode($imageUrl) ?>" class="profile-avatar" alt="<?= Html::encode($account->nome . ' ' . $account->cognome) ?>">
        </div>
        <div class="profile-head-text">
            <h1><?= Html::encode($account->nome . ' ' . $account->cognome) ?></h1>
            <p>@<?= Html::encode($account->username) ?></p>
            <span class="profile-role"><?= Html::encode($account->ruolo) ?></span>
        </div>
    </div>

    <!-- GRID -->
    <div class="profile-grid">

        <!-- DATI ACCOUNT -->
        <section class="profile-card">
            <h2>Dati account</h2>

            <div class="info-row">
                <span>Email</span>
                <strong><?= Html::encode($account->email ?: '-') ?></strong>
            </div>

            <div class="info-row">
                <span>Nome</span>
                <strong><?= Html::encode($account->nome ?: '-') ?></strong>
            </div>

            <div class="info-row">
                <span>Cognome</span>
                <strong><?= Html::encode($account->cognome ?: '-') ?></strong>
            </div>

            <div class="info-row">
                <span>Autorizzato</span>
                <?php if ($account->approvazione): ?>
                    <strong class="status-badge status-ok">Sì</strong>
                <?php else: ?>
                    <strong class="status-badge status-no">No</strong>
                <?php endif; ?>
            </div>

            <?php if (Yii::$app->user->identity->ruolo === 'cliente'): ?>
                <div class="info-row">
                    <span>Partita IVA</span>
                    <strong><?= Html::encode($account->partita_iva ?: '-') ?></strong>
                </div>
            <?php endif; ?>
        </section>

        <!-- AZIONI RAPIDE -->
        <section class="profile-card actions-card">
            <h2>Azioni rapide</h2>

            <?php $form = ActiveForm::begin([
                'action' => ['site/modify-image'],
                'options' => ['enctype' => 'multipart/form-data', 'id' => 'form-image'],
            ]); ?>
            <br>
            <?= $form->field($account, 'immagine')->fileInput([
                'accept' => '.jpg,.jpeg,.png,.webp',
                'id' => 'upload-img',
                'style' => 'display:none',
            ])->label(false) ?>
  <br>
            <?= Html::button('Modifica immagine', [
                'class' => 'profile-btn profile-btn-primary',
                'id' => 'btn-change-image',
                'type' => 'button',
            ]) ?>
              <br>
            <small class="text-muted d-block mb-2">Formati supportati: JPG, PNG, WEBP. Max 5 MB.</small>

            <?= Html::a('Modifica email', ['site/modify-email'], ['class' => 'profile-btn profile-btn-ghost']) ?>
             <br>
            <?= Html::a('Modifica password', ['site/mail'], ['class' => 'profile-btn profile-btn-ghost']) ?>

            <?php if (!$account->is_totp_enabled): ?>
               <br>
                <?= Html::a('Attiva 2FA', ['site/enable-2fa'], ['class' => 'profile-btn profile-btn-warning']) ?>
              <br>
                <?php else: ?>
                <?= Html::a('Disattiva 2FA', ['site/verify-2fa'], ['class' => 'profile-btn profile-btn-warning']) ?>
            <br>
                <?php endif; ?>

            <?php if (Yii::$app->user->identity->ruolo === 'cliente'): ?>
              
                <?= Html::a('Modifica Partita IVA', ['site/modify-iva'], ['class' => 'profile-btn profile-btn-ghost']) ?>
            <?php endif; ?>

            <?php ActiveForm::end(); ?>
              <br>
        </section>

    </div>
</div>

<!-- SCRIPT UPLOAD IMMAGINE -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    const btn = document.getElementById('btn-change-image');
    const fileInput = document.getElementById('upload-img');
    const form = document.getElementById('form-image');

    if (!btn || !fileInput || !form) return;

    btn.addEventListener('click', function (e) {
        e.preventDefault();
        fileInput.click();
    });

    fileInput.addEventListener('change', function () {
        if (!fileInput.files || fileInput.files.length === 0) return;

        const file = fileInput.files[0];
        const allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];
        const maxSize = 5 * 1024 * 1024;

        if (!allowedTypes.includes(file.type)) {
            alert('Formato non supportato. Usa JPG, PNG o WEBP.');
            fileInput.value = '';
            return;
        }

        if (file.size > maxSize) {
            alert('File troppo grande. Max 5 MB.');
            fileInput.value = '';
            return;
        }

        form.submit();
    });
});
</script>

<!-- STILE -->
<style>
.profile-shell {
    --bg-a: #edf6ff;
    --bg-b: #dceeff;
    --card: #ffffff;
    --line: #d8e6f3;
    --ink: #16324a;
    --muted: #65839b;
    --accent: #1d8ddb;
    --accent-soft: #e5f3ff;
    --warn: #ffb020;
    max-width: 1020px;
    margin: 18px auto;
    padding: 24px;
    border-radius: 22px;
    background:
        radial-gradient(circle at 8% 14%, #ffffffc9 0, #ffffff00 35%),
        linear-gradient(140deg, var(--bg-a), var(--bg-b));
    border: 1px solid #c9deef;
    font-family: "Segoe UI", "Helvetica Neue", sans-serif;
}

.profile-head {
    display: flex;
    gap: 18px;
    align-items: center;
    padding: 18px;
    border-radius: 16px;
    background: var(--card);
    border: 1px solid var(--line);
}

.profile-avatar-wrap {
    width: 90px;
    height: 90px;
    border-radius: 50%;
    padding: 3px;
    background: linear-gradient(135deg, #1d8ddb, #45c2ff);
}

.profile-avatar {
    width: 100%;
    height: 100%;
    object-fit: cover;
    border-radius: 50%;
}

.profile-head-text h1 {
    margin: 0;
    color: var(--ink);
    font-size: 28px;
}

.profile-head-text p {
    margin: 6px 0 10px;
    color: var(--muted);
    font-size: 15px;
}

.profile-role {
    display: inline-block;
    padding: 6px 12px;
    border-radius: 999px;
    background: var(--accent-soft);
    color: #0f6fac;
    font-weight: 600;
    text-transform: capitalize;
}

.profile-grid {
    margin-top: 16px;
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 14px;
}

.profile-card {
    background: var(--card);
    border: 1px solid var(--line);
    border-radius: 16px;
    padding: 18px;
}

.profile-card h2 {
    margin: 0 0 14px;
    color: var(--ink);
    font-size: 20px;
}

.info-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 10px 0;
    border-top: 1px dashed #d7e7f7;
}

.info-row:first-of-type {
    border-top: 0;
    padding-top: 0;
}

.info-row span {
    color: var(--muted);
    font-size: 14px;
}

.info-row strong {
    color: var(--ink);
    font-size: 15px;
    font-weight: 600;
}

.actions-card {
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.profile-btn {
    display: block;
    width: 100%;
    text-align: center;
    padding: 10px 14px;
    border-radius: 10px;
    font-weight: 600;
    border: 1px solid transparent;
    transition: transform .12s ease, box-shadow .12s ease, filter .12s ease;
}

.profile-btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 8px 20px #1d8ddb1f;
}

.profile-btn-primary {
    background: var(--accent);
    color: #fff;
}

.profile-btn-ghost {
    border-color: #c7ddef;
    background: #f7fbff;
    color: #1d4f73;
}

.profile-btn-warning {
    background: #fff8e8;
    border-color: #ffd68a;
    color: #8d5e00;
}

/* BADGE STATO */
.status-badge {
    padding: 5px 12px;
    border-radius: 999px;
    font-size: 13px;
    font-weight: 600;
}

.status-ok {
    background: #e6f7ee;
    color: #1f7a4d;
}

.status-no {
    background: #fdeaea;
    color: #b42318;
}

@media (max-width: 820px) {
    .profile-grid {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 640px) {
    .profile-shell {
        border-radius: 0;
        margin: 0;
        padding: 14px;
    }

    .profile-head {
        flex-direction: column;
        align-items: flex-start;
    }

    .profile-head-text h1 {
        font-size: 24px;
    }

    .info-row {
        flex-direction: column;
        align-items: flex-start;
    }

    .info-row strong {
        text-align: left;
    }
}
</style>