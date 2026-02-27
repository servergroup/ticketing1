<?php

use app\models\Assegnazioni;
use app\models\ticketFunctions;
use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\Ticket[] $ticket */
/** @var string|null $department */

$this->title = 'Ticket reparto';
$this->params['breadcrumbs'][] = ['label' => 'Ticket', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$departmentLabel = ticketFunctions::normalizeDepartment($department);
if ($departmentLabel === ticketFunctions::DEPARTMENT_DEVELOPMENT) {
    $departmentLabel = 'Sviluppo';
} elseif ($departmentLabel === ticketFunctions::DEPARTMENT_SYSTEM) {
    $departmentLabel = 'Sistemistica (ICT)';
}

$assignedCodes = [];
$codiciTicket = [];
foreach ($ticket as $ticketItem) {
    if (!empty($ticketItem->codice_ticket)) {
        $codiciTicket[] = $ticketItem->codice_ticket;
    }
}
if (!empty($codiciTicket)) {
    $assegnazioni = Assegnazioni::find()
        ->where(['codice_ticket' => array_values(array_unique($codiciTicket))])
        ->andWhere(['id_operatore' => Yii::$app->user->id])
        ->all();
    foreach ($assegnazioni as $assegnazione) {
        $assignedCodes[(string)$assegnazione->codice_ticket] = true;
    }
}
?>

<div class="page-shell">
    <div class="page-head">
        <div>
            <h1 class="page-title"><?= Html::encode($this->title) ?></h1>
            <p class="page-subtitle">
                Elenco ticket reparto <?= Html::encode($departmentLabel ?: '-') ?>.
            </p>
        </div>
        <div class="page-actions">
            <?= Html::a('Aperti reparto', ['tickets/my-reparto-open'], ['class' => 'btn btn-sm btn-outline-secondary']) ?>
            <?= Html::a('Tutti i ticket', ['tickets/index'], ['class' => 'btn btn-sm btn-outline-primary']) ?>
        </div>
    </div>

    <?php if (empty($ticket)): ?>
        <div class="detail-card text-center">
            <p class="mb-0">Non ci sono ticket disponibili per questo reparto.</p>
        </div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-sm table-hover table-striped align-middle text-center">
                <thead>
                    <tr>
                        <th>Codice</th>
                        <th>Cliente</th>
                        <th>Problema</th>
                        <th>Priorita</th>
                        <th>Stato</th>
                        <th>Scadenza</th>
                        <th>Azioni</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($ticket as $ticketItem): ?>
                        <?php
                        $cliente = $ticketItem->cliente;
                        $clienteNome = '-';
                        if ($cliente !== null) {
                            $clienteNome = trim($cliente->nome . ' ' . $cliente->cognome);
                            if ($clienteNome === '') {
                                $clienteNome = 'Cliente #' . (int)$ticketItem->id_cliente;
                            }
                        }
                        $isAssignedToMe = isset($assignedCodes[(string)$ticketItem->codice_ticket]);
                        ?>
                        <tr>
                            <td><?= Html::encode($ticketItem->codice_ticket) ?></td>
                            <td><?= Html::encode($clienteNome) ?></td>
                            <td><?= Html::encode(mb_strimwidth((string)$ticketItem->problema, 0, 70, '...')) ?></td>
                            <td><?= Html::encode($ticketItem->priorita ?: 'N/D') ?></td>
                            <td><?= Html::encode($ticketItem->stato ?: '-') ?></td>
                            <td><?= Html::encode($ticketItem->scadenza ?: '-') ?></td>
                            <td>
                                <div class="d-flex gap-1 justify-content-center flex-wrap">
                                    <?= Html::a('Apri', ['tickets/view', 'id' => $ticketItem->id], ['class' => 'btn btn-sm btn-outline-primary']) ?>
                                    <?= Html::a('Messaggio', ['messages/compose', 'ticketId' => $ticketItem->id], ['class' => 'btn btn-sm btn-outline-info']) ?>
                                    <?php if ($isAssignedToMe): ?>
                                        <?= Html::a('Risolvi', ['tickets/resolve', 'id' => $ticketItem->id], [
                                            'class' => 'btn btn-sm btn-outline-success',
                                            'data-method' => 'post',
                                            'data-confirm' => 'Confermi la risoluzione del ticket?',
                                        ]) ?>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

