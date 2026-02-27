<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\web\View;

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
/** @var app\models\TicketMessage[] $customerRecentMessages */
/** @var app\models\ticketfunction|null $inlineTicketModel */
/** @var app\models\TicketMessage|null $inlineMessageModel */
/** @var array $messageTicketOptions */
/** @var array $messageRecipientOptions */
/** @var bool $messagesModuleEnabled */
/** @var app\models\Ticket[] $adminRecentTickets */
/** @var app\models\User[] $adminPendingUsers */
/** @var app\models\TicketMessage[] $adminRecentMessages */

$this->title = 'Dashboard';
$ruolo = $user->ruolo;
$nome = Yii::$app->user->identity->nome;

$isClientDashboard = ($ruolo === 'cliente');
$isOperatorDashboard = !in_array($ruolo, ['cliente', 'amministratore'], true);
$isAdminDashboard = ($ruolo === 'amministratore');

$assignedCodeMap = [];
foreach ($operatorAssignedTickets as $ticketItem) {
    if (!empty($ticketItem->codice_ticket)) {
        $assignedCodeMap[$ticketItem->codice_ticket] = true;
    }
}

$actions = [];
if ($isClientDashboard) {
    $actions = [
        ['label' => 'Nuovo ticket rapido', 'url' => ['tickets/new-ticket'], 'icon' => 'fas fa-plus-circle', 'variant' => 'primary', 'panel' => 'create-ticket'],
        ['label' => 'I miei ticket (ultimi 4)', 'url' => ['tickets/my-ticket'], 'icon' => 'fas fa-ticket-alt', 'variant' => 'neutral', 'panel' => 'my-tickets', 'detailCount' => count($customerRecentTickets)],
        ['label' => 'Messaggi rapidi', 'url' => ['messages/index'], 'icon' => 'fas fa-comments', 'variant' => 'neutral', 'panel' => 'client-messages', 'detailCount' => count($customerRecentMessages)],
    ];
} elseif ($isAdminDashboard) {
    $actions = [
        ['label' => 'Ultimi ticket', 'url' => ['tickets/index'], 'icon' => 'fas fa-list', 'variant' => 'primary', 'panel' => 'admin-tickets', 'detailCount' => count($adminRecentTickets)],
        ['label' => 'Utenti in attesa', 'url' => ['admin/attese'], 'icon' => 'fas fa-user-clock', 'variant' => 'neutral', 'panel' => 'admin-users', 'detailCount' => count($adminPendingUsers)],
        ['label' => 'Messaggi rapidi', 'url' => ['messages/index'], 'icon' => 'fas fa-paper-plane', 'variant' => 'neutral', 'panel' => 'admin-messages', 'detailCount' => count($adminRecentMessages)],
    ];
} else {
    $actions = [
        ['label' => 'Ticket assegnati', 'url' => ['assegnazioni/my-ticket'], 'icon' => 'fas fa-briefcase', 'variant' => 'primary', 'panel' => 'assigned', 'detailCount' => count($operatorAssignedTickets)],
        ['label' => 'Ticket reparto', 'url' => ['tickets/my-reparto'], 'icon' => 'fas fa-layer-group', 'variant' => 'neutral', 'panel' => 'department', 'detailCount' => count($operatorDepartmentTickets)],
        ['label' => 'Messaggi rapidi', 'url' => ['messages/index'], 'icon' => 'fas fa-envelope-open-text', 'variant' => 'neutral', 'panel' => 'operator-messages', 'detailCount' => count($operatorRecentMessages)],
    ];
}

$toggleScript = <<<JS
(function () {
    const toggles = document.querySelectorAll('.js-dashboard-toggle');
    const panels = document.querySelectorAll('.dashboard-panel');
    if (!toggles.length || !panels.length) {
        return;
    }

    const setState = function (toggle, panel, active, shouldScroll) {
        if (active) {
            panel.removeAttribute('hidden');
            toggle.classList.add('is-active');
            toggle.setAttribute('aria-expanded', 'true');
            if (shouldScroll) {
                panel.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
            return;
        }

        panel.setAttribute('hidden', 'hidden');
        toggle.classList.remove('is-active');
        toggle.setAttribute('aria-expanded', 'false');
    };

    const closeAll = function () {
        toggles.forEach(function (btn) {
            const targetId = btn.getAttribute('data-target');
            const panel = targetId ? document.getElementById(targetId) : null;
            if (panel) {
                setState(btn, panel, false, false);
            }
        });
    };

    toggles.forEach(function (btn) {
        btn.addEventListener('click', function () {
            const targetId = btn.getAttribute('data-target');
            const panel = targetId ? document.getElementById(targetId) : null;
            if (!panel) {
                return;
            }

            const shouldOpen = panel.hasAttribute('hidden');
            closeAll();
            if (shouldOpen) {
                setState(btn, panel, true, true);
            }
        });
    });

    const firstToggle = toggles[0];
    const firstPanelId = firstToggle ? firstToggle.getAttribute('data-target') : null;
    const firstPanel = firstPanelId ? document.getElementById(firstPanelId) : null;
    if (firstToggle && firstPanel) {
        setState(firstToggle, firstPanel, true, false);
    }
})();
JS;
$this->registerJs($toggleScript, View::POS_END);
?>

<div class="dashboard-shell">
    <section class="dashboard-hero">
        <div class="hero-content">
            <p class="hero-eyebrow">Portale Ticketing Dataseed</p>
            <h1 class="hero-title">Benvenuto, <?= Html::encode($nome) ?></h1>
            <p class="hero-subtitle">
                Ruolo attivo: <strong><?= Html::encode($ruolo) ?></strong>.
                Dashboard operativa con ticket e messaggi rapidi integrati.
            </p>
        </div>
        <div class="hero-messages">
            <span class="hero-messages-label">Messaggi non letti</span>
            <span class="hero-messages-value"><?= (int)$unreadMessages ?></span>
            <?= Html::a('Apri inbox', ['messages/index'], ['class' => 'btn btn-sm btn-light']) ?>
        </div>
    </section>

    <section class="kpi-grid">
        <?php
        $kpiList = [
            'total' => 'Ticket totali',
            'open' => 'Aperti',
            'in_progress' => 'In lavorazione',
            'closed' => 'Chiusi',
            'expired' => 'Scaduti'
        ];
        foreach ($kpiList as $key => $label): ?>
            <article class="kpi-card">
                <span class="kpi-label"><?= Html::encode($label) ?></span>
                <strong class="kpi-value"><?= (int)($dashboardStats[$key] ?? 0) ?></strong>
            </article>
        <?php endforeach; ?>
    </section>

    <section class="quick-actions">
        <?php foreach ($actions as $action): ?>
            <div class="quick-action-item">
                <button
                    type="button"
                    class="quick-action quick-action-<?= Html::encode($action['variant']) ?> js-dashboard-toggle"
                    data-target="panel-<?= Html::encode($action['panel']) ?>"
                    aria-controls="panel-<?= Html::encode($action['panel']) ?>"
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
        <?php endforeach; ?>
    </section>

    <section class="dashboard-toggle-panels">
        <?php if ($isClientDashboard): ?>
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
                    <?php $ticketForm = ActiveForm::begin(['action' => ['tickets/new-ticket'], 'method' => 'post']); ?>
                    <?= $ticketForm->field($inlineTicketModel, 'problema')->textarea(['rows' => 4, 'placeholder' => 'Descrivi in dettaglio il problema riscontrato']) ?>
                    <div class="row">
                        <div class="col-md-4"><?= $ticketForm->field($inlineTicketModel, 'reparto')->dropDownList(['ict' => 'Sistemistica (ICT)', 'sviluppo' => 'Sviluppo'], ['prompt' => 'Seleziona reparto']) ?></div>
                        <div class="col-md-4"><?= $ticketForm->field($inlineTicketModel, 'priorita')->dropDownList(['bassa' => 'Bassa', 'media' => 'Media', 'alta' => 'Alta'], ['prompt' => 'Seleziona priorita']) ?></div>
                        <div class="col-md-4"><?= $ticketForm->field($inlineTicketModel, 'scadenza')->input('date') ?></div>
                    </div>
                    <?= $ticketForm->field($inlineTicketModel, 'id_cliente')->hiddenInput()->label(false) ?>
                    <div class="d-flex gap-2 flex-wrap">
                        <?= Html::submitButton('Invia ticket', ['class' => 'btn btn-primary']) ?>
                        <?= Html::a('Vai al form completo', ['tickets/new-ticket'], ['class' => 'btn btn-outline-secondary']) ?>
                    </div>
                    <?php ActiveForm::end(); ?>
                <?php endif; ?>
            </article>

            <article id="panel-my-tickets" class="dashboard-panel" hidden>
                <div class="dashboard-panel-head">
                    <h2>I miei ticket recenti (ultimi 4)</h2>
                    <?= Html::a('Pagina completa', ['tickets/my-ticket'], ['class' => 'btn btn-sm btn-outline-primary']) ?>
                </div>
                <?php if (empty($customerRecentTickets)): ?>
                    <p class="dashboard-empty">Non hai ancora ticket aperti.</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-sm table-hover table-striped align-middle">
                            <thead>
                            <tr><th>Codice</th><th>Stato</th><th>Priorita</th><th>Scadenza</th><th>Data invio</th><th class="text-end">Azioni</th></tr>
                            </thead>
                            <tbody>
                            <?php foreach ($customerRecentTickets as $t): ?>
                                <tr>
                                    <td><?= Html::encode($t->codice_ticket) ?></td>
                                    <td><?= Html::encode($t->stato ?: '-') ?></td>
                                    <td><?= Html::encode($t->priorita ?: 'N/D') ?></td>
                                    <td><?= Html::encode($t->scadenza ?: '-') ?></td>
                                    <td><?= Html::encode($t->data_invio ?: '-') ?></td>
                                    <td class="text-end"><?= Html::a('Apri', ['tickets/view', 'id' => $t->id], ['class' => 'btn btn-sm btn-outline-primary']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </article>

            <article id="panel-client-messages" class="dashboard-panel" hidden>
                <div class="dashboard-panel-head">
                    <h2>Nuovo messaggio rapido</h2>
                    <div class="dashboard-panel-actions">
                        <?= Html::a('Inbox', ['messages/index'], ['class' => 'btn btn-sm btn-outline-primary']) ?>
                        <?= Html::a('Composer completo', ['messages/compose'], ['class' => 'btn btn-sm btn-outline-secondary']) ?>
                    </div>
                </div>

                <?php if (!$messagesModuleEnabled || $inlineMessageModel === null): ?>
                    <p class="dashboard-empty">Modulo messaggi non disponibile.</p>
                <?php elseif (empty($messageRecipientOptions)): ?>
                    <p class="dashboard-empty">Nessun destinatario disponibile in questo momento.</p>
                <?php else: ?>
                    <?php $msgForm = ActiveForm::begin(['action' => ['messages/compose'], 'method' => 'post']); ?>
                    <div class="row">
                        <div class="col-md-6"><?= $msgForm->field($inlineMessageModel, 'ticket_id')->dropDownList($messageTicketOptions, ['prompt' => 'Nessun ticket']) ?></div>
                        <div class="col-md-6"><?= $msgForm->field($inlineMessageModel, 'recipient_id')->dropDownList($messageRecipientOptions, ['prompt' => 'Seleziona destinatario']) ?></div>
                    </div>
                    <?= $msgForm->field($inlineMessageModel, 'subject')->textInput(['maxlength' => true, 'placeholder' => 'Oggetto messaggio']) ?>
                    <?= $msgForm->field($inlineMessageModel, 'body')->textarea(['rows' => 4, 'placeholder' => 'Scrivi qui il messaggio']) ?>
                    <div class="d-flex gap-2 flex-wrap">
                        <?= Html::submitButton('Invia messaggio', ['class' => 'btn btn-success']) ?>
                        <?= Html::a('Vai alla messaggistica', ['messages/index'], ['class' => 'btn btn-outline-secondary']) ?>
                    </div>
                    <?php ActiveForm::end(); ?>
                <?php endif; ?>

                <hr>
                <h3 class="h6 mb-3">Ultimi messaggi ricevuti</h3>
                <?php if (empty($customerRecentMessages)): ?>
                    <p class="dashboard-empty">Nessun messaggio ricevuto.</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-sm table-hover table-striped align-middle">
                            <thead>
                            <tr><th>Data</th><th>Mittente</th><th>Ticket</th><th>Oggetto</th><th>Stato</th><th></th></tr>
                            </thead>
                            <tbody>
                            <?php foreach ($customerRecentMessages as $m):
                                $mittente = $m->sender ? trim($m->sender->nome . ' ' . $m->sender->cognome) : '-';
                                $ticketCode = $m->ticket ? $m->ticket->codice_ticket : '-';
                                $isUnread = ((int)$m->is_read === 0);
                            ?>
                                <tr>
                                    <td><?= Yii::$app->formatter->asDatetime($m->created_at, 'php:d/m/Y H:i') ?></td>
                                    <td><?= Html::encode($mittente) ?></td>
                                    <td><?= Html::encode($ticketCode) ?></td>
                                    <td><?= Html::encode($m->subject) ?></td>
                                    <td><span class="badge <?= $isUnread ? 'bg-warning text-dark' : 'bg-secondary' ?>"><?= $isUnread ? 'Da leggere' : 'Letto' ?></span></td>
                                    <td class="text-end"><?= Html::a('Apri', ['messages/view', 'id' => $m->id], ['class' => 'btn btn-sm btn-outline-primary']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </article>
        <?php endif; ?>

        <?php if ($isOperatorDashboard): ?>
            <article id="panel-assigned" class="dashboard-panel" hidden>
                <div class="dashboard-panel-head">
                    <h2>Ticket assegnati (ultimi 4)</h2>
                    <?= Html::a('Pagina completa', ['assegnazioni/my-ticket'], ['class' => 'btn btn-sm btn-outline-primary']) ?>
                </div>
                <?php if (empty($operatorAssignedTickets)): ?>
                    <p class="dashboard-empty">Nessun ticket assegnato al momento.</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-sm table-hover table-striped align-middle">
                            <thead>
                            <tr><th>Codice</th><th>Stato</th><th>Priorita</th><th>Scadenza</th><th class="text-end">Azioni</th></tr>
                            </thead>
                            <tbody>
                            <?php foreach ($operatorAssignedTickets as $t): ?>
                                <tr>
                                    <td><?= Html::encode($t->codice_ticket) ?></td>
                                    <td><?= Html::encode($t->stato ?: '-') ?></td>
                                    <td><?= Html::encode($t->priorita ?: 'N/D') ?></td>
                                    <td><?= Html::encode($t->scadenza ?: '-') ?></td>
                                    <td class="text-end">
                                        <div class="dashboard-row-actions">
                                            <?= Html::a('Apri', ['tickets/view', 'id' => $t->id], ['class' => 'btn btn-sm btn-outline-primary']) ?>
                                            <?= Html::a('Risolvi', ['tickets/resolve', 'id' => $t->id], ['class' => 'btn btn-sm btn-outline-success', 'data-method' => 'post', 'data-confirm' => 'Confermi la risoluzione del ticket?']) ?>
                                            <?= Html::a('Messaggio', ['messages/compose', 'ticketId' => $t->id], ['class' => 'btn btn-sm btn-outline-info']) ?>
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
                    <h2>Ticket reparto <?= Html::encode($operatorDepartment ?: '-') ?> (ultimi 4)</h2>
                    <?= Html::a('Pagina completa', ['tickets/my-reparto'], ['class' => 'btn btn-sm btn-outline-primary']) ?>
                </div>
                <?php if (empty($operatorDepartmentTickets)): ?>
                    <p class="dashboard-empty">Nessun ticket disponibile per il reparto.</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-sm table-hover table-striped align-middle">
                            <thead>
                            <tr><th>Codice</th><th>Stato</th><th>Priorita</th><th>Data invio</th><th>Assegnato</th><th class="text-end">Azioni</th></tr>
                            </thead>
                            <tbody>
                            <?php foreach ($operatorDepartmentTickets as $t):
                                $isAssignedToMe = isset($assignedCodeMap[$t->codice_ticket]);
                            ?>
                                <tr>
                                    <td><?= Html::encode($t->codice_ticket) ?></td>
                                    <td><?= Html::encode($t->stato ?: '-') ?></td>
                                    <td><?= Html::encode($t->priorita ?: 'N/D') ?></td>
                                    <td><?= Html::encode($t->data_invio ?: '-') ?></td>
                                    <td><span class="badge <?= $isAssignedToMe ? 'bg-success' : 'bg-secondary' ?>"><?= $isAssignedToMe ? 'A te' : 'No' ?></span></td>
                                    <td class="text-end">
                                        <div class="dashboard-row-actions">
                                            <?= Html::a('Apri', ['tickets/view', 'id' => $t->id], ['class' => 'btn btn-sm btn-outline-primary']) ?>
                                            <?= Html::a('Messaggio', ['messages/compose', 'ticketId' => $t->id], ['class' => 'btn btn-sm btn-outline-info']) ?>
                                            <?php if ($isAssignedToMe): ?>
                                                <?= Html::a('Risolvi', ['tickets/resolve', 'id' => $t->id], ['class' => 'btn btn-sm btn-outline-success', 'data-method' => 'post', 'data-confirm' => 'Confermi la risoluzione del ticket?']) ?>
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

            <article id="panel-operator-messages" class="dashboard-panel" hidden>
                <div class="dashboard-panel-head">
                    <h2>Nuovo messaggio rapido</h2>
                    <div class="dashboard-panel-actions">
                        <?= Html::a('Inbox', ['messages/index'], ['class' => 'btn btn-sm btn-outline-primary']) ?>
                        <?= Html::a('Composer completo', ['messages/compose'], ['class' => 'btn btn-sm btn-outline-secondary']) ?>
                    </div>
                </div>

                <?php if (!$messagesModuleEnabled || $inlineMessageModel === null): ?>
                    <p class="dashboard-empty">Modulo messaggi non disponibile.</p>
                <?php elseif (empty($messageRecipientOptions)): ?>
                    <p class="dashboard-empty">Nessun destinatario disponibile in questo momento.</p>
                <?php else: ?>
                    <?php $msgForm = ActiveForm::begin(['action' => ['messages/compose'], 'method' => 'post']); ?>
                    <div class="row">
                        <div class="col-md-6"><?= $msgForm->field($inlineMessageModel, 'ticket_id')->dropDownList($messageTicketOptions, ['prompt' => 'Nessun ticket']) ?></div>
                        <div class="col-md-6"><?= $msgForm->field($inlineMessageModel, 'recipient_id')->dropDownList($messageRecipientOptions, ['prompt' => 'Seleziona destinatario']) ?></div>
                    </div>
                    <?= $msgForm->field($inlineMessageModel, 'subject')->textInput(['maxlength' => true, 'placeholder' => 'Oggetto messaggio']) ?>
                    <?= $msgForm->field($inlineMessageModel, 'body')->textarea(['rows' => 4, 'placeholder' => 'Scrivi qui il messaggio']) ?>
                    <div class="d-flex gap-2 flex-wrap">
                        <?= Html::submitButton('Invia messaggio', ['class' => 'btn btn-success']) ?>
                        <?= Html::a('Vai alla messaggistica', ['messages/index'], ['class' => 'btn btn-outline-secondary']) ?>
                    </div>
                    <?php ActiveForm::end(); ?>
                <?php endif; ?>

                <hr>
                <h3 class="h6 mb-3">Ultimi messaggi ricevuti</h3>
                <?php if (empty($operatorRecentMessages)): ?>
                    <p class="dashboard-empty">Nessun messaggio ricevuto.</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-sm table-hover table-striped align-middle">
                            <thead>
                            <tr><th>Data</th><th>Mittente</th><th>Ticket</th><th>Oggetto</th><th>Stato</th><th></th></tr>
                            </thead>
                            <tbody>
                            <?php foreach ($operatorRecentMessages as $m):
                                $mittente = $m->sender ? trim($m->sender->nome . ' ' . $m->sender->cognome) : '-';
                                $ticketCode = $m->ticket ? $m->ticket->codice_ticket : '-';
                                $isUnread = ((int)$m->is_read === 0);
                            ?>
                                <tr>
                                    <td><?= Yii::$app->formatter->asDatetime($m->created_at, 'php:d/m/Y H:i') ?></td>
                                    <td><?= Html::encode($mittente) ?></td>
                                    <td><?= Html::encode($ticketCode) ?></td>
                                    <td><?= Html::encode($m->subject) ?></td>
                                    <td><span class="badge <?= $isUnread ? 'bg-warning text-dark' : 'bg-secondary' ?>"><?= $isUnread ? 'Da leggere' : 'Letto' ?></span></td>
                                    <td class="text-end"><?= Html::a('Apri', ['messages/view', 'id' => $m->id], ['class' => 'btn btn-sm btn-outline-primary']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </article>
        <?php endif; ?>

        <?php if ($isAdminDashboard): ?>
            <article id="panel-admin-tickets" class="dashboard-panel" hidden>
                <div class="dashboard-panel-head">
                    <h2>Ultimi ticket (ultimi 4)</h2>
                    <?= Html::a('Pagina completa', ['tickets/index'], ['class' => 'btn btn-sm btn-outline-primary']) ?>
                </div>
                <?php if (empty($adminRecentTickets)): ?>
                    <p class="dashboard-empty">Nessun ticket disponibile.</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-sm table-hover table-striped align-middle">
                            <thead>
                            <tr><th>Codice</th><th>Stato</th><th>Priorita</th><th>Cliente</th><th>Data invio</th><th class="text-end">Azioni</th></tr>
                            </thead>
                            <tbody>
                            <?php foreach ($adminRecentTickets as $t): ?>
                                <tr>
                                    <td><?= Html::encode($t->codice_ticket) ?></td>
                                    <td><?= Html::encode($t->stato ?: '-') ?></td>
                                    <td><?= Html::encode($t->priorita ?: 'N/D') ?></td>
                                    <td><?= Html::encode($t->cliente ? trim($t->cliente->nome . ' ' . $t->cliente->cognome) : '-') ?></td>
                                    <td><?= Html::encode($t->data_invio ?: '-') ?></td>
                                    <td class="text-end"><?= Html::a('Apri', ['tickets/view', 'id' => $t->id], ['class' => 'btn btn-sm btn-outline-primary']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </article>

            <article id="panel-admin-users" class="dashboard-panel" hidden>
                <div class="dashboard-panel-head">
                    <h2>Utenti in attesa approvazione (ultimi 4)</h2>
                    <?= Html::a('Pagina completa', ['admin/attese'], ['class' => 'btn btn-sm btn-outline-primary']) ?>
                </div>
                <?php if (empty($adminPendingUsers)): ?>
                    <p class="dashboard-empty">Nessun utente in attesa.</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-sm table-hover table-striped align-middle">
                            <thead>
                            <tr><th>Nome</th><th>Email</th><th>Ruolo</th><th class="text-end">Azioni</th></tr>
                            </thead>
                            <tbody>
                            <?php foreach ($adminPendingUsers as $pendingUser): ?>
                                <tr>
                                    <td><?= Html::encode(trim($pendingUser->nome . ' ' . $pendingUser->cognome)) ?></td>
                                    <td><?= Html::encode($pendingUser->email ?: '-') ?></td>
                                    <td><?= Html::encode($pendingUser->ruolo ?: '-') ?></td>
                                    <td class="text-end">
                                        <div class="dashboard-row-actions">
                                            <?= Html::a('Approva', ['admin/approve', 'id' => $pendingUser->id], ['class' => 'btn btn-sm btn-outline-success']) ?>
                                            <?= Html::a('Profilo', ['admin/view', 'id' => $pendingUser->id], ['class' => 'btn btn-sm btn-outline-primary']) ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </article>

            <article id="panel-admin-messages" class="dashboard-panel" hidden>
                <div class="dashboard-panel-head">
                    <h2>Nuovo messaggio rapido</h2>
                    <div class="dashboard-panel-actions">
                        <?= Html::a('Inbox', ['messages/index'], ['class' => 'btn btn-sm btn-outline-primary']) ?>
                        <?= Html::a('Composer completo', ['messages/compose'], ['class' => 'btn btn-sm btn-outline-secondary']) ?>
                    </div>
                </div>

                <?php if (!$messagesModuleEnabled || $inlineMessageModel === null): ?>
                    <p class="dashboard-empty">Modulo messaggi non disponibile.</p>
                <?php elseif (empty($messageRecipientOptions)): ?>
                    <p class="dashboard-empty">Nessun destinatario disponibile in questo momento.</p>
                <?php else: ?>
                    <?php $msgForm = ActiveForm::begin(['action' => ['messages/compose'], 'method' => 'post']); ?>
                    <div class="row">
                        <div class="col-md-6"><?= $msgForm->field($inlineMessageModel, 'ticket_id')->dropDownList($messageTicketOptions, ['prompt' => 'Nessun ticket']) ?></div>
                        <div class="col-md-6"><?= $msgForm->field($inlineMessageModel, 'recipient_id')->dropDownList($messageRecipientOptions, ['prompt' => 'Seleziona destinatario']) ?></div>
                    </div>
                    <?= $msgForm->field($inlineMessageModel, 'subject')->textInput(['maxlength' => true, 'placeholder' => 'Oggetto messaggio']) ?>
                    <?= $msgForm->field($inlineMessageModel, 'body')->textarea(['rows' => 4, 'placeholder' => 'Scrivi qui il messaggio']) ?>
                    <div class="d-flex gap-2 flex-wrap">
                        <?= Html::submitButton('Invia messaggio', ['class' => 'btn btn-success']) ?>
                        <?= Html::a('Vai alla messaggistica', ['messages/index'], ['class' => 'btn btn-outline-secondary']) ?>
                    </div>
                    <?php ActiveForm::end(); ?>
                <?php endif; ?>

                <hr>
                <h3 class="h6 mb-3">Ultimi messaggi ricevuti</h3>
                <?php if (empty($adminRecentMessages)): ?>
                    <p class="dashboard-empty">Nessun messaggio ricevuto.</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-sm table-hover table-striped align-middle">
                            <thead>
                            <tr><th>Data</th><th>Mittente</th><th>Ticket</th><th>Oggetto</th><th>Stato</th><th></th></tr>
                            </thead>
                            <tbody>
                            <?php foreach ($adminRecentMessages as $m):
                                $mittente = $m->sender ? trim($m->sender->nome . ' ' . $m->sender->cognome) : '-';
                                $ticketCode = $m->ticket ? $m->ticket->codice_ticket : '-';
                                $isUnread = ((int)$m->is_read === 0);
                            ?>
                                <tr>
                                    <td><?= Yii::$app->formatter->asDatetime($m->created_at, 'php:d/m/Y H:i') ?></td>
                                    <td><?= Html::encode($mittente) ?></td>
                                    <td><?= Html::encode($ticketCode) ?></td>
                                    <td><?= Html::encode($m->subject) ?></td>
                                    <td><span class="badge <?= $isUnread ? 'bg-warning text-dark' : 'bg-secondary' ?>"><?= $isUnread ? 'Da leggere' : 'Letto' ?></span></td>
                                    <td class="text-end"><?= Html::a('Apri', ['messages/view', 'id' => $m->id], ['class' => 'btn btn-sm btn-outline-primary']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </article>
        <?php endif; ?>

    </section>
</div>
