<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\User $user */
/** @var int $countTicket */
/** @var app\models\Ticket|null $ultimoTicket */
/** @var array $dashboardStats */
/** @var int $unreadMessages */
/** @var app\models\Ticket[] $operatorAssignedTickets */
/** @var app\models\Ticket[] $operatorDepartmentTickets */
/** @var app\models\TicketMessage[] $operatorRecentMessages */
/** @var string|null $operatorDepartment */
/** @var app\models\Ticket[] $customerRecentTickets */
/** @var app\models\ticketfunction|null $inlineTicketModel */

$this->title = 'Dashboard';
$ruolo = $user->ruolo;
$actions = [];
$nome = Yii::$app->user->identity->nome;
$isClientDashboard = ($ruolo === 'cliente');
$isOperatorDashboard = !in_array($ruolo, ['cliente', 'amministratore'], true);
$assignedCodeMap = [];
foreach ($operatorAssignedTickets as $assignedTicket) {
    if (!empty($assignedTicket->codice_ticket)) {
        $assignedCodeMap[$assignedTicket->codice_ticket] = true;
    }
}


if ($ruolo === 'cliente') {
    $actions = [
        [
            'label' => 'Nuovo ticket',
            'url' => ['tickets/new-ticket'],
            'icon' => 'fas fa-plus-circle',
            'variant' => 'primary',
            'toggle' => 'create-ticket',
        ],
        [
            'label' => 'I miei ticket',
            'url' => ['tickets/my-ticket'],
            'icon' => 'fas fa-ticket-alt',
            'variant' => 'neutral',
            'toggle' => 'my-tickets',
            'detailCount' => count($customerRecentTickets),
        ],
        ['label' => 'Scrivi a supporto', 'url' => ['messages/compose'], 'icon' => 'fas fa-comments', 'variant' => 'neutral'],
    ];
} elseif ($ruolo === 'amministratore') {
    $actions = [
        ['label' => 'Tutti i ticket', 'url' => ['tickets/index'], 'icon' => 'fas fa-list', 'variant' => 'primary'],
        ['label' => 'Utenti in attesa', 'url' => ['admin/attese'], 'icon' => 'fas fa-user-clock', 'variant' => 'neutral'],
        ['label' => 'Nuovo messaggio', 'url' => ['messages/compose'], 'icon' => 'fas fa-paper-plane', 'variant' => 'neutral'],
    ];
} else {
    $actions = [
        [
            'label' => 'Ticket assegnati',
            'url' => ['assegnazioni/my-ticket'],
            'icon' => 'fas fa-briefcase',
            'variant' => 'primary',
            'toggle' => 'assigned',
            'detailCount' => count($operatorAssignedTickets),
        ],
        [
            'label' => 'Ticket reparto',
            'url' => ['tickets/my-reparto'],
            'icon' => 'fas fa-layer-group',
            'variant' => 'neutral',
            'toggle' => 'department',
            'detailCount' => count($operatorDepartmentTickets),
        ],
        [
            'label' => 'Nuovi messaggi',
            'url' => ['messages/index'],
            'icon' => 'fas fa-envelope-open-text',
            'variant' => 'neutral',
            'toggle' => 'messages',
            'detailCount' => (int)$unreadMessages,
        ],
    ];
}

$hasDashboardToggles = false;
foreach ($actions as $action) {
    if (isset($action['toggle'])) {
        $hasDashboardToggles = true;
        break;
    }
}
?>

<div class="dashboard-shell">
    <section class="dashboard-hero">
        <div class="hero-content">
            <p class="hero-eyebrow">Portale Ticketing Dataseed</p>
            <h1 class="hero-title">Benvenuto, <?= Html::encode($nome) ?></h1>
            <p class="hero-subtitle">
                Ruolo attivo: <strong><?= Html::encode($ruolo) ?></strong>.
                Dashboard ottimizzata per gestione ticket e comunicazioni operative.
            </p>
        </div>
        <div class="hero-messages">
            <span class="hero-messages-label">Messaggi non letti</span>
            <span class="hero-messages-value"><?= (int)$unreadMessages ?></span>
            <?= Html::a('Apri inbox', ['messages/index'], ['class' => 'btn btn-sm btn-light']) ?>
        </div>
    </section>

    <section class="kpi-grid">
        <article class="kpi-card">
            <span class="kpi-label">Ticket totali</span>
            <strong class="kpi-value"><?= (int)($dashboardStats['total'] ?? 0) ?></strong>
        </article>
        <article class="kpi-card">
            <span class="kpi-label">Aperti</span>
            <strong class="kpi-value"><?= (int)($dashboardStats['open'] ?? 0) ?></strong>
        </article>
        <article class="kpi-card">
            <span class="kpi-label">In lavorazione</span>
            <strong class="kpi-value"><?= (int)($dashboardStats['in_progress'] ?? 0) ?></strong>
        </article>
        <article class="kpi-card">
            <span class="kpi-label">Chiusi</span>
            <strong class="kpi-value"><?= (int)($dashboardStats['closed'] ?? 0) ?></strong>
        </article>
        <article class="kpi-card">
            <span class="kpi-label">Scaduti</span>
            <strong class="kpi-value"><?= (int)($dashboardStats['expired'] ?? 0) ?></strong>
        </article>
    </section>

    <section class="quick-actions">
        <?php foreach ($actions as $action): ?>
            <?php if (isset($action['toggle'])): ?>
                <div class="quick-action-item">
                    <button
                        type="button"
                        class="quick-action quick-action-<?= Html::encode($action['variant']) ?> js-dashboard-toggle"
                        data-target="<?= Html::encode($action['toggle']) ?>"
                        aria-expanded="false"
                    >
                        <i class="<?= Html::encode($action['icon']) ?>"></i>
                        <span><?= Html::encode($action['label']) ?></span>
                        <?php if (isset($action['detailCount'])): ?>
                            <span class="quick-action-count"><?= (int)$action['detailCount'] ?></span>
                        <?php endif; ?>
                    </button>
                    <?= Html::a('Apri pagina completa', $action['url'], ['class' => 'quick-action-direct']) ?>
                </div>
            <?php else: ?>
                <a class="quick-action quick-action-<?= Html::encode($action['variant']) ?>" href="<?= Url::to($action['url']) ?>">
                    <i class="<?= Html::encode($action['icon']) ?>"></i>
                    <span><?= Html::encode($action['label']) ?></span>
                </a>
            <?php endif; ?>
        <?php endforeach; ?>
    </section>

    <?php if ($isClientDashboard): ?>
        <section class="dashboard-toggle-panels">
            <article id="panel-create-ticket" class="dashboard-panel" hidden>
                <div class="dashboard-panel-head">
                    <h2>Nuovo ticket rapido</h2>
                    <div class="dashboard-panel-actions">
                        <?= Html::a('Pagina completa', ['tickets/new-ticket'], ['class' => 'btn btn-sm btn-outline-primary']) ?>
                        <?= Html::a('I miei ticket', ['tickets/my-ticket'], ['class' => 'btn btn-sm btn-outline-secondary']) ?>
                    </div>
                </div>
                <?php if ($inlineTicketModel === null): ?>
                    <p class="dashboard-empty">Form non disponibile al momento.</p>
                <?php else: ?>
                    <?php $form = ActiveForm::begin([
                        'action' => ['tickets/new-ticket'],
                        'method' => 'post',
                    ]); ?>
                    <?= $form->field($inlineTicketModel, 'problema')->textarea([
                        'rows' => 4,
                        'placeholder' => 'Descrivi in dettaglio il problema riscontrato',
                    ]) ?>
                    <div class="row">
                        <div class="col-md-4">
                            <?= $form->field($inlineTicketModel, 'reparto')->dropDownList([
                                'ict' => 'Sistemistica (ICT)',
                                'sviluppo' => 'Sviluppo',
                            ], ['prompt' => 'Seleziona reparto']) ?>
                        </div>
                        <div class="col-md-4">
                            <?= $form->field($inlineTicketModel, 'priorita')->dropDownList([
                                'bassa' => 'Bassa',
                                'media' => 'Media',
                                'alta' => 'Alta',
                            ], ['prompt' => 'Seleziona priorita']) ?>
                        </div>
                        <div class="col-md-4">
                            <?= $form->field($inlineTicketModel, 'scadenza')->input('date') ?>
                        </div>
                    </div>
                    <?= $form->field($inlineTicketModel, 'id_cliente')->hiddenInput()->label(false) ?>
                    <div class="d-flex gap-2 flex-wrap">
                        <?= Html::submitButton('Invia ticket', ['class' => 'btn btn-primary']) ?>
                        <?= Html::a('Vai al form completo', ['tickets/new-ticket'], ['class' => 'btn btn-outline-secondary']) ?>
                    </div>
                    <?php ActiveForm::end(); ?>
                <?php endif; ?>
            </article>

            <article id="panel-my-tickets" class="dashboard-panel" hidden>
                <div class="dashboard-panel-head">
                    <h2>I miei ticket recenti</h2>
                    <?= Html::a('Pagina completa', ['tickets/my-ticket'], ['class' => 'btn btn-sm btn-outline-primary']) ?>
                </div>
                <?php if (empty($customerRecentTickets)): ?>
                    <p class="dashboard-empty">Non hai ancora ticket aperti.</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-sm table-hover table-striped align-middle">
                            <thead>
                                <tr>
                                    <th>Codice</th>
                                    <th>Stato</th>
                                    <th>Priorita</th>
                                    <th>Scadenza</th>
                                    <th>Data invio</th>
                                    <th class="text-end">Azioni</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($customerRecentTickets as $ticketModel): ?>
                                    <tr>
                                        <td><?= Html::encode($ticketModel->codice_ticket) ?></td>
                                        <td><?= Html::encode($ticketModel->stato ?: '-') ?></td>
                                        <td><?= Html::encode($ticketModel->priorita ?: 'N/D') ?></td>
                                        <td><?= Html::encode($ticketModel->scadenza ?: '-') ?></td>
                                        <td><?= Html::encode($ticketModel->data_invio ?: '-') ?></td>
                                        <td class="text-end">
                                            <?= Html::a('Apri', ['tickets/view', 'id' => $ticketModel->id], ['class' => 'btn btn-sm btn-outline-primary']) ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </article>
        </section>
    <?php endif; ?>

    <?php if ($isOperatorDashboard): ?>
        <section class="dashboard-toggle-panels">
            <article id="panel-assigned" class="dashboard-panel" hidden>
                <div class="dashboard-panel-head">
                    <h2>Ticket assegnati</h2>
                    <?= Html::a('Pagina completa', ['assegnazioni/my-ticket'], ['class' => 'btn btn-sm btn-outline-primary']) ?>
                </div>
                <?php if (empty($operatorAssignedTickets)): ?>
                    <p class="dashboard-empty">Nessun ticket assegnato al momento.</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-sm table-hover table-striped align-middle">
                            <thead>
                                <tr>
                                    <th>Codice</th>
                                    <th>Stato</th>
                                    <th>Priorita</th>
                                    <th>Scadenza</th>
                                    <th class="text-end">Azioni</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($operatorAssignedTickets as $ticketModel): ?>
                                    <tr>
                                        <td><?= Html::encode($ticketModel->codice_ticket) ?></td>
                                        <td><?= Html::encode($ticketModel->stato ?: '-') ?></td>
                                        <td><?= Html::encode($ticketModel->priorita ?: 'N/D') ?></td>
                                        <td><?= Html::encode($ticketModel->scadenza ?: '-') ?></td>
                                        <td class="text-end">
                                            <div class="dashboard-row-actions">
                                                <?= Html::a('Apri', ['tickets/view', 'id' => $ticketModel->id], ['class' => 'btn btn-sm btn-outline-primary']) ?>
                                                <?= Html::a('Risolvi', ['tickets/resolve', 'id' => $ticketModel->id], [
                                                    'class' => 'btn btn-sm btn-outline-success',
                                                    'data-method' => 'post',
                                                    'data-confirm' => 'Confermi la risoluzione del ticket?',
                                                ]) ?>
                                                <?= Html::a('Messaggio', ['messages/compose', 'ticketId' => $ticketModel->id], ['class' => 'btn btn-sm btn-outline-info']) ?>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </article>

            <article id="panel-department" class="dashboard-panel" hidden>
                <div class="dashboard-panel-head">
                    <h2>Ticket reparto <?= Html::encode($operatorDepartment ?: '-') ?></h2>
                    <?= Html::a('Pagina completa', ['tickets/my-reparto'], ['class' => 'btn btn-sm btn-outline-primary']) ?>
                </div>
                <?php if (empty($operatorDepartmentTickets)): ?>
                    <p class="dashboard-empty">Nessun ticket disponibile per il reparto.</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-sm table-hover table-striped align-middle">
                            <thead>
                                <tr>
                                    <th>Codice</th>
                                    <th>Stato</th>
                                    <th>Priorita</th>
                                    <th>Data invio</th>
                                    <th>Assegnato</th>
                                    <th class="text-end">Azioni</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($operatorDepartmentTickets as $ticketModel): ?>
                                    <?php $isAssignedToMe = isset($assignedCodeMap[$ticketModel->codice_ticket]); ?>
                                    <tr>
                                        <td><?= Html::encode($ticketModel->codice_ticket) ?></td>
                                        <td><?= Html::encode($ticketModel->stato ?: '-') ?></td>
                                        <td><?= Html::encode($ticketModel->priorita ?: 'N/D') ?></td>
                                        <td><?= Html::encode($ticketModel->data_invio ?: '-') ?></td>
                                        <td>
                                            <span class="badge <?= $isAssignedToMe ? 'bg-success' : 'bg-secondary' ?>">
                                                <?= $isAssignedToMe ? 'A te' : 'No' ?>
                                            </span>
                                        </td>
                                        <td class="text-end">
                                            <div class="dashboard-row-actions">
                                                <?= Html::a('Apri', ['tickets/view', 'id' => $ticketModel->id], ['class' => 'btn btn-sm btn-outline-primary']) ?>
                                                <?= Html::a('Messaggio', ['messages/compose', 'ticketId' => $ticketModel->id], ['class' => 'btn btn-sm btn-outline-info']) ?>
                                                <?php if ($isAssignedToMe): ?>
                                                    <?= Html::a('Risolvi', ['tickets/resolve', 'id' => $ticketModel->id], [
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
            </article>

            <article id="panel-messages" class="dashboard-panel" hidden>
                <div class="dashboard-panel-head">
                    <h2>Nuovi messaggi</h2>
                    <div class="dashboard-panel-actions">
                        <?= Html::a('Inbox', ['messages/index'], ['class' => 'btn btn-sm btn-outline-primary']) ?>
                        <?= Html::a('Scrivi', ['messages/compose'], ['class' => 'btn btn-sm btn-success']) ?>
                    </div>
                </div>
                <?php if (empty($operatorRecentMessages)): ?>
                    <p class="dashboard-empty">Nessun nuovo messaggio disponibile.</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-sm table-hover table-striped align-middle">
                            <thead>
                                <tr>
                                    <th>Data</th>
                                    <th>Mittente</th>
                                    <th>Ticket</th>
                                    <th>Oggetto</th>
                                    <th>Stato</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($operatorRecentMessages as $message): ?>
                                    <?php
                                        $mittente = $message->sender ? trim($message->sender->nome . ' ' . $message->sender->cognome) : '-';
                                        $ticketCode = $message->ticket ? $message->ticket->codice_ticket : '-';
                                        $isUnread = ((int)$message->is_read === 0);
                                    ?>
                                    <tr>
                                        <td><?= Yii::$app->formatter->asDatetime($message->created_at, 'php:d/m/Y H:i') ?></td>
                                        <td><?= Html::encode($mittente) ?></td>
                                        <td><?= Html::encode($ticketCode) ?></td>
                                        <td><?= Html::encode($message->subject) ?></td>
                                        <td>
                                            <span class="badge <?= $isUnread ? 'bg-warning text-dark' : 'bg-secondary' ?>">
                                                <?= $isUnread ? 'Da leggere' : 'Letto' ?>
                                            </span>
                                        </td>
                                        <td class="text-end">
                                            <?= Html::a('Apri', ['messages/view', 'id' => $message->id], ['class' => 'btn btn-sm btn-outline-primary']) ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </article>
        </section>
    <?php endif; ?>

    <?php if ($hasDashboardToggles): ?>
        <?php
        $js = <<<'JS'
(function () {
    const toggles = document.querySelectorAll('.js-dashboard-toggle');
    if (!toggles.length) {
        return;
    }

    const closePanels = function () {
        document.querySelectorAll('.dashboard-panel').forEach(function (panel) {
            panel.setAttribute('hidden', 'hidden');
        });
        toggles.forEach(function (toggle) {
            toggle.classList.remove('is-active');
            toggle.setAttribute('aria-expanded', 'false');
        });
    };

    toggles.forEach(function (toggle) {
        toggle.addEventListener('click', function () {
            const panel = document.getElementById('panel-' + toggle.dataset.target);
            if (!panel) {
                return;
            }

            const isOpen = !panel.hasAttribute('hidden');
            closePanels();

            if (isOpen) {
                return;
            }

            panel.removeAttribute('hidden');
            toggle.classList.add('is-active');
            toggle.setAttribute('aria-expanded', 'true');
        });
    });
})();
JS;
        $this->registerJs($js);
        ?>
    <?php endif; ?>

    <?php if ($ultimoTicket): ?>
        <section class="last-ticket-card">
            <div>
                <h2>Ultimo ticket</h2>
                <p>
                    Codice <strong><?= Html::encode($ultimoTicket->codice_ticket) ?></strong>,
                    stato <strong><?= Html::encode($ultimoTicket->stato) ?></strong>,
                    priorita <strong><?= Html::encode($ultimoTicket->priorita ?: 'N/D') ?></strong>.
                </p>
            </div>
            <?= Html::a('Apri dettaglio', ['tickets/view', 'id' => $ultimoTicket->id], ['class' => 'btn btn-outline-primary']) ?>
        </section>
    <?php endif; ?>
</div>
