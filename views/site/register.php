<?php

use yii\helpers\Html;
use yii\helpers\Json;
use yii\widgets\ActiveForm;
use yii\web\View;
use app\assets\RegisterAsset;

/** @var app\models\User $user */
/** @var app\models\Turni $turni */

$this->title = 'Registrazione Aziendale';
RegisterAsset::register($this);
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
$this->registerJs(
    'window.ticketingCountryPrefixMap = ' . Json::htmlEncode($countryPrefixMap) . ';',
    View::POS_HEAD
);
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
