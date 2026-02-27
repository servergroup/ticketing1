<?php

use app\models\ticketFunctions;
use app\models\User;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\Assegnazioni $model */
/** @var yii\widgets\ActiveForm $form */

$departmentAttribute = null;
if ($model->hasAttribute('reparto')) {
    $departmentAttribute = 'reparto';
} elseif ($model->hasAttribute('ambito')) {
    $departmentAttribute = 'ambito';
}

$selectedDepartment = null;
if ($departmentAttribute !== null) {
    $selectedDepartment = ticketFunctions::normalizeDepartment((string)$model->{$departmentAttribute});
}

$allowedRoles = ticketFunctions::rolesForDepartment($selectedDepartment);
$operatori = User::find()
    ->where(['ruolo' => $allowedRoles, 'approvazione' => 1])
    ->orderBy(['ruolo' => SORT_ASC, 'nome' => SORT_ASC, 'cognome' => SORT_ASC])
    ->all();

$operatorOptions = [];
foreach ($operatori as $operatore) {
    $nomeCompleto = trim($operatore->nome . ' ' . $operatore->cognome);
    $operatorOptions[(int)$operatore->id] = $nomeCompleto . ' - ' . $operatore->ruolo;
}
?>

<div class="form-card">
    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'codice_ticket')->textInput([
        'maxlength' => true,
        'readonly' => !$model->isNewRecord,
    ]) ?>

    <?php if ($departmentAttribute !== null): ?>
        <?= $form->field($model, $departmentAttribute)->dropDownList([
            'ict' => 'Sistemistica (ICT)',
            'sviluppo' => 'Sviluppo',
        ], ['prompt' => 'Seleziona reparto']) ?>
    <?php endif; ?>

    <?= $form->field($model, 'id_operatore')->dropDownList($operatorOptions, [
        'prompt' => 'Seleziona operatore',
        'class' => 'form-control form-select',
    ]) ?>

    <div class="d-flex gap-2">
        <?= Html::submitButton(
            $model->isNewRecord ? 'Salva assegnazione' : 'Aggiorna assegnazione',
            ['class' => 'btn btn-primary']
        ) ?>
        <?= Html::a('Annulla', ['assegnazioni/index'], ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>

