<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var app\models\User $user */
/** @var app\models\Turni $turni */

$this->title = 'Registrazione Aziendale';
$logoSvgPath = Yii::getAlias('@webroot/img/taglio_dataseed.svg');
$logoSrc = file_exists($logoSvgPath)
    ? Yii::getAlias('@web/img/taglio_dataseed.svg')
    : Yii::getAlias('@web/img/taglio_dataseed.png');

$countryDialCodes = [
    'IT' => ['label' => 'Italia', 'prefix' => '+39'],
    'US' => ['label' => 'Stati Uniti', 'prefix' => '+1'],
    'GB' => ['label' => 'Regno Unito', 'prefix' => '+44'],
    'FR' => ['label' => 'Francia', 'prefix' => '+33'],
    'DE' => ['label' => 'Germania', 'prefix' => '+49'],
    'ES' => ['label' => 'Spagna', 'prefix' => '+34'],
    'PT' => ['label' => 'Portogallo', 'prefix' => '+351'],
    'CH' => ['label' => 'Svizzera', 'prefix' => '+41'],
    'NL' => ['label' => 'Paesi Bassi', 'prefix' => '+31'],
    'BE' => ['label' => 'Belgio', 'prefix' => '+32'],
];

$countryOptions = [];
$countryPrefixMap = [];
foreach ($countryDialCodes as $countryCode => $countryMeta) {
    $countryOptions[$countryCode] = $countryMeta['label'] . ' (' . $countryMeta['prefix'] . ')';
    $countryPrefixMap[$countryCode] = $countryMeta['prefix'];
}
?>

<div class="admin-registerAdmin">
    <div class="logo-container">
        <img src="<?= $logoSrc ?>" alt="Logo Aziendale" class="company-logo">
    </div>

    <h1>Registrazione Aziendale</h1>

    <?php $form = ActiveForm::begin([
        'options' => ['enctype' => 'multipart/form-data'],
    ]); ?>

    <div class="image-upload-container">
        <label for="upload-img" class="upload-circle">
            <span class="plus-icon">+</span>
            <img id="preview-img" src="" alt="preview">
        </label>

        <?= $form->field($user, 'immagine')->fileInput([
            'id' => 'upload-img',
            'style' => 'display:none',
        ])->label(false) ?>
    </div>

    <?= $form->field($user, 'nome')->textInput(['maxlength' => true]) ?>
    <?= $form->field($user, 'cognome')->textInput(['maxlength' => true]) ?>
    <?= $form->field($user, 'password')->passwordInput(['maxlength' => true]) ?>
    <?= $form->field($user, 'email')->input('email') ?>
    <?= $form->field($user, 'nazione')->dropDownList(
        $countryOptions,
        ['prompt' => 'Seleziona nazione', 'id' => 'nazione-select']
    ) ?>

    <div class="row">
        <div class="col-sm-4">
            <div class="form-group phone-prefix-group">
                <label class="control-label" for="phone-prefix">Prefisso</label>
                <input type="text" id="phone-prefix" class="form-control" readonly value="">
            </div>
        </div>
        <div class="col-sm-8">
            <?= $form->field($user, 'recapito_telefonico')->textInput([
                'id' => 'phone-number',
                'placeholder' => '3331234567',
            ]) ?>
        </div>
    </div>

    <?= $form->field($user, 'telegram_username')->textInput(['maxlength' => true, 'placeholder' => '@utente']) ?>
    <?= $form->field($user, 'telegram_chat_id')->textInput(['maxlength' => true, 'placeholder' => 'es. 123456789']) ?>

    <?= $form->field($user, 'ruolo')->dropDownList([
        'cliente' => 'Cliente',
        'personale' => 'personale',
    ], ['prompt' => 'Seleziona un ruolo', 'id' => 'ruolo-select']) ?>

    <div id="piva-container" style="display:none;">
        <?= $form->field($user, 'partita_iva')->textInput(['maxlength' => true]) ?>
    </div>

    <div id="azienda-container" style="display:none;">
        <?= $form->field($user, 'azienda')->textInput(['maxlength' => true]) ?>
    </div>

    <div class="form-group">
        <?php if (Yii::$app->user->isGuest): ?>
            <?= Html::submitButton('Registrati', ['class' => 'btn btn-primary']) ?>
        <?php elseif (Yii::$app->user->identity->ruolo === 'amministratore'): ?>
            <?= Html::submitButton('Crea', ['class' => 'btn btn-primary']) ?>
        <?php endif; ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>

<style>
.admin-registerAdmin {
    max-width: 550px;
    margin: 0 auto;
    padding: 30px 25px;
    background: #ffffff;
    border-radius: 12px;
    box-shadow: 0 4px 18px rgba(0, 0, 0, 0.08);
    font-family: "Inter", Arial, sans-serif;
}

.logo-container {
    display: flex;
    justify-content: center;
    margin-bottom: 15px;
}

.company-logo {
    width: 140px;
    height: auto;
    object-fit: contain;
}

.admin-registerAdmin h1 {
    text-align: center;
    font-size: 26px;
    font-weight: 600;
    margin-bottom: 25px;
    color: #2c3e50;
}

.image-upload-container {
    display: flex;
    justify-content: center;
    margin-bottom: 25px;
}

.upload-circle {
    width: 150px;
    height: 150px;
    border: 2px dashed #b5b5b5;
    border-radius: 50%;
    cursor: pointer;
    position: relative;
    overflow: hidden;
    background: #f1f1f1;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: border-color 0.3s, background 0.3s;
}

.upload-circle:hover {
    border-color: #0056b3;
    background: #f8faff;
}

.plus-icon {
    font-size: 48px;
    color: #7a7a7a;
    font-weight: 300;
    position: absolute;
    transition: opacity 0.3s;
}

#preview-img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: none;
}

.form-group label {
    font-weight: 500;
    color: #34495e;
}

.form-control {
    border-radius: 8px !important;
    padding: 10px 12px !important;
    border: 1px solid #d0d0d0 !important;
    transition: border-color 0.2s, box-shadow 0.2s;
}

.form-control:focus {
    border-color: #0056b3 !important;
    box-shadow: 0 0 0 3px rgba(0, 86, 179, 0.15) !important;
}

.phone-prefix-group {
    margin-bottom: 1rem;
}

.btn-primary {
    width: 100%;
    padding: 12px;
    font-size: 16px;
    border-radius: 8px;
    background: #0056b3;
    border: none;
    transition: background 0.2s;
}

#turni {
    display: none;
}

.btn-primary:hover {
    background: #003f82;
}

@media (max-width: 768px) {
    .admin-registerAdmin {
        max-width: 90%;
        padding: 20px;
    }

    .upload-circle {
        width: 130px;
        height: 130px;
    }

    .company-logo {
        width: 120px;
    }

    .admin-registerAdmin h1 {
        font-size: 22px;
    }
}

@media (max-width: 480px) {
    .admin-registerAdmin {
        max-width: 95%;
        padding: 18px;
        box-shadow: none;
    }

    .upload-circle {
        width: 110px;
        height: 110px;
    }

    .plus-icon {
        font-size: 38px;
    }

    .company-logo {
        width: 100px;
    }

    .admin-registerAdmin h1 {
        font-size: 20px;
        margin-bottom: 18px;
    }

    .form-control {
        padding: 9px 10px !important;
        font-size: 14px;
    }

    .btn-primary {
        padding: 10px;
        font-size: 15px;
    }
}

@media (max-width: 360px) {
    .upload-circle {
        width: 95px;
        height: 95px;
    }

    .plus-icon {
        font-size: 32px;
    }

    .company-logo {
        width: 85px;
    }

    .admin-registerAdmin h1 {
        font-size: 18px;
    }
}
</style>

<script>
(function () {
    const uploadInput = document.getElementById('upload-img');
    if (uploadInput) {
        uploadInput.addEventListener('change', function (event) {
            const img = document.getElementById('preview-img');
            const plus = document.querySelector('.plus-icon');
            const selectedFile = event.target.files && event.target.files[0];
            if (!img || !plus || !selectedFile) {
                return;
            }

            img.src = URL.createObjectURL(selectedFile);
            img.style.display = 'block';
            plus.style.opacity = '0';
        });
    }

    const roleSelect = document.getElementById('ruolo-select');
    if (roleSelect) {
        const updateRoleFields = function () {
            const piva = document.getElementById('piva-container');
            const azienda = document.getElementById('azienda-container');
            if (!piva || !azienda) {
                return;
            }

            if (roleSelect.value === 'cliente') {
                piva.style.display = 'block';
                azienda.style.display = 'block';
            } else {
                piva.style.display = 'none';
                azienda.style.display = 'none';
            }
        };

        roleSelect.addEventListener('change', updateRoleFields);
        updateRoleFields();
    }

    const countryPrefixes = <?= json_encode($countryPrefixMap, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;
    const countrySelect = document.getElementById('nazione-select');
    const phonePrefixInput = document.getElementById('phone-prefix');
    const phoneInput = document.getElementById('phone-number');

    if (!countrySelect || !phonePrefixInput || !phoneInput) {
        return;
    }

    const onlyDigits = function (value) {
        return (value || '').replace(/\D+/g, '');
    };

    const stripKnownPrefix = function (digits, prefix) {
        const prefixDigits = onlyDigits(prefix);
        if (prefixDigits !== '' && digits.startsWith(prefixDigits)) {
            return digits.slice(prefixDigits.length);
        }
        return digits;
    };

    const applySelectedPrefix = function () {
        const selectedPrefix = countryPrefixes[countrySelect.value] || '';
        const previousPrefix = phonePrefixInput.dataset.currentPrefix || '';
        phonePrefixInput.value = selectedPrefix;
        phonePrefixInput.dataset.currentPrefix = selectedPrefix;
        phoneInput.placeholder = selectedPrefix !== '' ? selectedPrefix + ' 3331234567' : '3331234567';

        if (selectedPrefix === '') {
            return;
        }

        const rawValue = phoneInput.value.trim();
        if (rawValue === '') {
            return;
        }

        let digits = onlyDigits(rawValue);
        digits = stripKnownPrefix(digits, previousPrefix);
        digits = stripKnownPrefix(digits, selectedPrefix);

        if (digits === '') {
            phoneInput.value = '';
            return;
        }

        phoneInput.value = selectedPrefix + digits;
    };

    countrySelect.addEventListener('change', applySelectedPrefix);
    phoneInput.addEventListener('blur', applySelectedPrefix);

    const registerForm = countrySelect.closest('form');
    if (registerForm) {
        registerForm.addEventListener('submit', applySelectedPrefix);
    }

    applySelectedPrefix();
})();
</script>
