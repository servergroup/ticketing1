<?php

use app\models\ticketFunctions;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var app\models\Ticket $model */
/** @var app\models\Assegnazioni|null $assegnazione */
/** @var array $operatorOptions */

$this->title = 'Ticket ' . $model->codice_ticket;
$this->params['breadcrumbs'][] = ['label' => 'Ticket', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$ruolo = Yii::$app->user->identity->ruolo;
$isAdmin = $ruolo === 'amministratore';

$clienteLabel = 'N/D';
if ($model->cliente !== null) {
    $nomeCompleto = trim($model->cliente->nome . ' ' . $model->cliente->cognome);
    $clienteLabel = $nomeCompleto !== '' ? $nomeCompleto : ('Utente #' . (int)$model->id_cliente);
}

$operatoreCorrente = 'Nessuna assegnazione';
if ($assegnazione !== null && $assegnazione->operatore !== null) {
    $nomeOperatore = trim($assegnazione->operatore->nome . ' ' . $assegnazione->operatore->cognome);
    $operatoreCorrente = $nomeOperatore . ' - ' . $assegnazione->operatore->ruolo;
} elseif ($assegnazione !== null && !empty($assegnazione->id_operatore)) {
    $operatoreCorrente = 'Operatore #' . (int)$assegnazione->id_operatore;
}

$normalizedDepartment = ticketFunctions::normalizeDepartment((string)$model->reparto) ?? (string)$model->reparto;
$departmentLabel = $normalizedDepartment;
if ($normalizedDepartment === ticketFunctions::DEPARTMENT_DEVELOPMENT) {
    $departmentLabel = 'Sviluppo';
} elseif ($normalizedDepartment === ticketFunctions::DEPARTMENT_SYSTEM) {
    $departmentLabel = 'Sistemistica (ICT)';
}

$assignmentMode = $assegnazione === null ? 'auto' : 'manual';
if (empty($operatorOptions)) {
    $assignmentMode = 'auto';
}
$selectedOperatorId = $assegnazione !== null ? (int)$assegnazione->id_operatore : null;
?>

<div class="page-shell page-shell--narrow">
    <div class="page-head">
        <div>
            <h1 class="page-title"><?= Html::encode($this->title) ?></h1>
            <p class="page-subtitle">Dettaglio ticket, assegnazione operativa e comunicazioni reparto.</p>
        </div>
        <div class="page-actions">
            <?= Html::a('Torna ai ticket', ['tickets/my-ticket'], ['class' => 'btn btn-sm btn-outline-secondary']) ?>
            <?= Html::a('Invia messaggio', ['messages/compose', 'ticketId' => $model->id], ['class' => 'btn btn-sm btn-success']) ?>
        </div>
    </div>

    <div class="detail-card">
        <?= DetailView::widget([
            'model' => $model,
            'attributes' => [
                'codice_ticket',
                'problema:ntext',
                [
                    'label' => 'Reparto',
                    'value' => $departmentLabel,
                ],
                'priorita',
                'stato',
                'scadenza',
                [
                    'label' => 'Cliente',
                    'value' => $clienteLabel,
                ],
               
                'data_invio',
            ],
        ]) ?>
    </div>

    <div class="section-card assignment-card mt-3" id="assignment-panel">
        <h2 class="h5 mb-2">Assegnazione ticket</h2>
        <p class="page-subtitle mb-3">
            Reparto ticket: <strong><?= Html::encode($departmentLabel) ?></strong>.
            In modalita manuale vengono mostrati solo operatori del reparto corretto.
        </p>

        <?php if ($isAdmin): ?>
            <?php $form = ActiveForm::begin([
                'action' => ['tickets/assign', 'id' => $model->id],
                'options' => ['id' => 'ticket-assignment-form-' . (int)$model->id],
            ]); ?>

            <div class="assignment-mode-group">
                <label class="assignment-mode-option">
                    <input
                        type="radio"
                        name="assignment_mode"
                        value="auto"
                        <?= $assignmentMode === 'auto' ? 'checked' : '' ?>
                    >
                    <span>Assegna automaticamente</span>
                </label>
                <label class="assignment-mode-option">
                    <input
                        type="radio"
                        name="assignment_mode"
                        value="manual"
                        <?= $assignmentMode === 'manual' ? 'checked' : '' ?>
                    >
                    <span>Assegna manualmente</span>
                </label>
            </div>

            <div class="assignment-manual-field mt-3" data-assignment-manual-wrap>
                <?= Html::label('Operatore', 'operator_id', ['class' => 'form-label']) ?>
                <?= Html::dropDownList('operator_id', $selectedOperatorId, $operatorOptions, [
                    'id' => 'operator_id',
                    'prompt' => 'Seleziona operatore reparto',
                    'class' => 'form-control form-select',
                    'disabled' => $assignmentMode !== 'manual',
                ]) ?>
                <?php if (empty($operatorOptions)): ?>
                    <small class="text-danger d-block mt-2">Nessun operatore disponibile per questo reparto.</small>
                <?php endif; ?>
            </div>

            <div class="d-flex gap-2 flex-wrap mt-3">
                <?= Html::submitButton('Conferma assegnazione', ['class' => 'btn btn-primary']) ?>
                <?php if ($assegnazione !== null): ?>
                    <?= Html::a('Ritira assegnazione', ['tickets/ritiro', 'codice_ticket' => $model->codice_ticket], [
                        'class' => 'btn btn-outline-danger',
                        'data-method' => 'post',
                        'data-confirm' => 'Confermi il ritiro del ticket?',
                    ]) ?>
                <?php endif; ?>
            </div>

            <?php ActiveForm::end(); ?>
        <?php else: ?>
            <p class="mb-0 text-muted">La gestione assegnazioni e ritiro e riservata agli amministratori.</p>
        <?php endif; ?>
    </div>
</div>

<?php
$js = <<<'JS'
(function () {
    const forms = document.querySelectorAll('[id^="ticket-assignment-form-"]');
    forms.forEach(function (form) {
        const manualWrap = form.querySelector('[data-assignment-manual-wrap]');
        const operatorSelect = form.querySelector('select[name="operator_id"]');
        const radios = form.querySelectorAll('input[name="assignment_mode"]');

        const syncState = function () {
            const selected = form.querySelector('input[name="assignment_mode"]:checked');
            const isManual = selected && selected.value === 'manual';
            if (manualWrap) {
                manualWrap.classList.toggle('is-hidden', !isManual);
            }
            if (operatorSelect) {
                operatorSelect.disabled = !isManual;
                operatorSelect.required = !!isManual;
            }
        };

        radios.forEach(function (radio) {
            radio.addEventListener('change', syncState);
        });

        syncState();
    });
})();
JS;
$this->registerJs($js);
?>

