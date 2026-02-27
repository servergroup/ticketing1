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
/** @var app\models\Ticket[] $adminRecentTickets */

$this->title = 'Dashboard';
$ruolo = $user->ruolo;
$nome = Yii::$app->user->identity->nome;

$isClientDashboard = ($ruolo === 'cliente');
$isOperatorDashboard = !in_array($ruolo, ['cliente', 'amministratore'], true);
$isAdminDashboard = ($ruolo === 'amministratore');

if(Yii::$app->user->identity->ruolo==='amministratore')
    {
        $isAdminDashboard=true;    
}
    
// Mappa ticket assegnati per operatore
$assignedCodeMap = [];
foreach ($operatorAssignedTickets as $ticket) {
    if (!empty($ticket->codice_ticket)) {
        $assignedCodeMap[$ticket->codice_ticket] = true;
    }
}

// QUICK ACTIONS
$actions = [];
if ($isClientDashboard) {
    $actions = [
        ['label'=>'Nuovo ticket','url'=>['tickets/new-ticket'],'icon'=>'fas fa-plus-circle','variant'=>'primary','toggle'=>'create-ticket'],
        ['label'=>'I miei ticket','url'=>['tickets/my-ticket'],'icon'=>'fas fa-ticket-alt','variant'=>'neutral','toggle'=>'my-tickets','detailCount'=>count($customerRecentTickets)],
        ['label'=>'Scrivi a supporto','url'=>['messages/compose'],'icon'=>'fas fa-comments','variant'=>'neutral'],
    ];
} elseif ($isAdminDashboard) {
    $actions = [
        ['label'=>'Tutti i ticket','url'=>['tickets/index'],'icon'=>'fas fa-list','variant'=>'primary','toggle'=>'admin-tickets'],
        ['label'=>'Utenti in attesa','url'=>['admin/attese'],'icon'=>'fas fa-user-clock','variant'=>'neutral','toggle'=>'admin-users'],
        ['label'=>'Nuovo messaggio','url'=>['messages/compose'],'icon'=>'fas fa-paper-plane','variant'=>'neutral'],
    ];
} else {
    $actions = [
        ['label'=>'Ticket assegnati','url'=>['assegnazioni/my-ticket'],'icon'=>'fas fa-briefcase','variant'=>'primary','toggle'=>'assigned','detailCount'=>count($operatorAssignedTickets)],
        ['label'=>'Ticket reparto','url'=>['tickets/my-reparto'],'icon'=>'fas fa-layer-group','variant'=>'neutral','toggle'=>'department','detailCount'=>count($operatorDepartmentTickets)],
        ['label'=>'Nuovi messaggi','url'=>['messages/index'],'icon'=>'fas fa-envelope-open-text','variant'=>'neutral','toggle'=>'messages','detailCount'=>(int)$unreadMessages],
    ];
}

// Controllo toggle
$hasDashboardToggles = false;
foreach ($actions as $action) {
    if (isset($action['toggle'])) {
        $hasDashboardToggles = true;
        break;
    }
}

?>

<div class="dashboard-shell">

    <!-- HERO -->
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
            <?= Html::a('Apri inbox', ['messages/index'], ['class'=>'btn btn-sm btn-light']) ?>
        </div>
    </section>

    <!-- KPI GRID -->
    <section class="kpi-grid">
        <?php 
        $kpiList = [
            'total'=>'Ticket totali',
            'open'=>'Aperti',
            'in_progress'=>'In lavorazione',
            'closed'=>'Chiusi',
            'expired'=>'Scaduti'
        ];
        foreach ($kpiList as $key => $label): ?>
            <article class="kpi-card">
                <span class="kpi-label"><?= Html::encode($label) ?></span>
                <strong class="kpi-value"><?= (int)($dashboardStats[$key] ?? 0) ?></strong>
            </article>
        <?php endforeach; ?>
    </section>

    <!-- QUICK ACTIONS -->
    <section class="quick-actions">
        <?php foreach($actions as $action): ?>
            <?php if(isset($action['toggle'])): ?>
                <div class="quick-action-item">
                    <button type="button" class="quick-action quick-action-<?= Html::encode($action['variant']) ?> js-dashboard-toggle" data-target="<?= Html::encode($action['toggle']) ?>" aria-expanded="false">
                        <i class="<?= Html::encode($action['icon']) ?>"></i>
                        <span><?= Html::encode($action['label']) ?></span>
                        <?php if(isset($action['detailCount'])): ?>
                            <span class="quick-action-count"><?= (int)$action['detailCount'] ?></span>
                        <?php endif; ?>
                    </button>
                    <?= Html::a('Apri pagina completa', $action['url'], ['class'=>'quick-action-direct']) ?>
                </div>
            <?php else: ?>
                <a class="quick-action quick-action-<?= Html::encode($action['variant']) ?>" href="<?= Url::to($action['url']) ?>">
                    <i class="<?= Html::encode($action['icon']) ?>"></i>
                    <span><?= Html::encode($action['label']) ?></span>
                </a>
            <?php endif; ?>
        <?php endforeach; ?>
    </section>

    <!-- DASHBOARD PANELS -->
    <section class="dashboard-toggle-panels">

        <!-- CLIENT PANELS -->
        <?php if($isClientDashboard): ?>
            <article id="panel-create-ticket" class="dashboard-panel" hidden>
                <div class="dashboard-panel-head">
                    <h2>Nuovo ticket rapido</h2>
                    <div class="dashboard-panel-actions">
                        <?= Html::a('Pagina completa',['tickets/new-ticket'],['class'=>'btn btn-sm btn-outline-primary']) ?>
                        <?= Html::a('I miei ticket',['tickets/my-ticket'],['class'=>'btn btn-sm btn-outline-secondary']) ?>
                    </div>
                </div>
                <?php if($inlineTicketModel === null): ?>
                    <p class="dashboard-empty">Form non disponibile al momento.</p>
                <?php else: ?>
                    <?php $form = ActiveForm::begin(['action'=>['tickets/new-ticket'],'method'=>'post']); ?>
                    <?= $form->field($inlineTicketModel,'problema')->textarea(['rows'=>4,'placeholder'=>'Descrivi in dettaglio il problema riscontrato']) ?>
                    <div class="row">
                        <div class="col-md-4"><?= $form->field($inlineTicketModel,'reparto')->dropDownList(['ict'=>'Sistemistica (ICT)','sviluppo'=>'Sviluppo'],['prompt'=>'Seleziona reparto']) ?></div>
                        <div class="col-md-4"><?= $form->field($inlineTicketModel,'priorita')->dropDownList(['bassa'=>'Bassa','media'=>'Media','alta'=>'Alta'],['prompt'=>'Seleziona priorita']) ?></div>
                        <div class="col-md-4"><?= $form->field($inlineTicketModel,'scadenza')->input('date') ?></div>
                    </div>
                    <?= $form->field($inlineTicketModel,'id_cliente')->hiddenInput()->label(false) ?>
                    <div class="d-flex gap-2 flex-wrap">
                        <?= Html::submitButton('Invia ticket',['class'=>'btn btn-primary']) ?>
                        <?= Html::a('Vai al form completo',['tickets/new-ticket'],['class'=>'btn btn-outline-secondary']) ?>
                    </div>
                    <?php ActiveForm::end(); ?>
                <?php endif; ?>
            </article>

            <article id="panel-my-tickets" class="dashboard-panel" hidden>
                <div class="dashboard-panel-head">
                    <h2>I miei ticket recenti</h2>
                    <?= Html::a('Pagina completa',['tickets/my-ticket'],['class'=>'btn btn-sm btn-outline-primary']) ?>
                </div>
                <?php if(empty($customerRecentTickets)): ?>
                    <p class="dashboard-empty">Non hai ancora ticket aperti.</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-sm table-hover table-striped align-middle">
                            <thead><tr><th>Codice</th><th>Stato</th><th>Priorita</th><th>Scadenza</th><th>Data invio</th><th class="text-end">Azioni</th></tr></thead>
                            <tbody>
                                <?php foreach($customerRecentTickets as $t): ?>
                                    <tr>
                                        <td><?= Html::encode($t->codice_ticket) ?></td>
                                        <td><?= Html::encode($t->stato ?: '-') ?></td>
                                        <td><?= Html::encode($t->priorita ?: 'N/D') ?></td>
                                        <td><?= Html::encode($t->scadenza ?: '-') ?></td>
                                        <td><?= Html::encode($t->data_invio ?: '-') ?></td>
                                        <td class="text-end"><?= Html::a('Apri',['tickets/view','id'=>$t->id],['class'=>'btn btn-sm btn-outline-primary']) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </article>
        <?php endif; ?>

        <!-- OPERATOR PANELS -->
        <?php if($isOperatorDashboard): ?>
            <article id="panel-assigned" class="dashboard-panel" hidden>
                <div class="dashboard-panel-head">
                    <h2>Ticket assegnati</h2>
                    <?= Html::a('Pagina completa',['assegnazioni/my-ticket'],['class'=>'btn btn-sm btn-outline-primary']) ?>
                </div>
                <?php if(empty($operatorAssignedTickets)): ?>
                    <p class="dashboard-empty">Nessun ticket assegnato al momento.</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-sm table-hover table-striped align-middle">
                            <thead><tr><th>Codice</th><th>Stato</th><th>Priorita</th><th>Scadenza</th><th class="text-end">Azioni</th></tr></thead>
                            <tbody>
                                <?php foreach($operatorAssignedTickets as $t): ?>
                                    <tr>
                                        <td><?= Html::encode($t->codice_ticket) ?></td>
                                        <td><?= Html::encode($t->stato ?: '-') ?></td>
                                        <td><?= Html::encode($t->priorita ?: 'N/D') ?></td>
                                        <td><?= Html::encode($t->scadenza ?: '-') ?></td>
                                        <td class="text-end">
                                            <div class="dashboard-row-actions">
                                                <?= Html::a('Apri',['tickets/view','id'=>$t->id],['class'=>'btn btn-sm btn-outline-primary']) ?>
                                                <?= Html::a('Risolvi',['tickets/resolve','id'=>$t->id],['class'=>'btn btn-sm btn-outline-success','data-method'=>'post','data-confirm'=>'Confermi la risoluzione del ticket?']) ?>
                                                <?= Html::a('Messaggio',['messages/compose','ticketId'=>$t->id],['class'=>'btn btn-sm btn-outline-info']) ?>
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
                    <?= Html::a('Pagina completa',['tickets/my-reparto'],['class'=>'btn btn-sm btn-outline-primary']) ?>
                </div>
                <?php if(empty($operatorDepartmentTickets)): ?>
                    <p class="dashboard-empty">Nessun ticket disponibile per il reparto.</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-sm table-hover table-striped align-middle">
                            <thead><tr><th>Codice</th><th>Stato</th><th>Priorita</th><th>Data invio</th><th>Assegnato</th><th class="text-end">Azioni</th></tr></thead>
                            <tbody>
                                <?php foreach($operatorDepartmentTickets as $t):
                                    $isAssignedToMe = isset($assignedCodeMap[$t->codice_ticket]);
                                ?>
                                    <tr>
                                        <td><?= Html::encode($t->codice_ticket) ?></td>
                                        <td><?= Html::encode($t->stato ?: '-') ?></td>
                                        <td><?= Html::encode($t->priorita ?: 'N/D') ?></td>
                                        <td><?= Html::encode($t->data_invio ?: '-') ?></td>
                                        <td><span class="badge <?= $isAssignedToMe ? 'bg-success':'bg-secondary' ?>"><?= $isAssignedToMe ? 'A te':'No' ?></span></td>
                                        <td class="text-end">
                                            <div class="dashboard-row-actions">
                                                <?= Html::a('Apri',['tickets/view','id'=>$t->id],['class'=>'btn btn-sm btn-outline-primary']) ?>
                                                <?= Html::a('Messaggio',['messages/compose','ticketId'=>$t->id],['class'=>'btn btn-sm btn-outline-info']) ?>
                                                <?php if($isAssignedToMe): ?>
                                                    <?= Html::a('Risolvi',['tickets/resolve','id'=>$t->id],['class'=>'btn btn-sm btn-outline-success','data-method'=>'post','data-confirm'=>'Confermi la risoluzione del ticket?']) ?>
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
                        <?= Html::a('Inbox',['messages/index'],['class'=>'btn btn-sm btn-outline-primary']) ?>
                        <?= Html::a('Scrivi',['messages/compose'],['class'=>'btn btn-sm btn-success']) ?>
                    </div>
                </div>
                <?php if(empty($operatorRecentMessages)): ?>
                    <p class="dashboard-empty">Nessun nuovo messaggio disponibile.</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-sm table-hover table-striped align-middle">
                            <thead><tr><th>Data</th><th>Mittente</th><th>Ticket</th><th>Oggetto</th><th>Stato</th><th></th></tr></thead>
                            <tbody>
                                <?php foreach($operatorRecentMessages as $m):
                                    $mittente = $m->sender ? trim($m->sender->nome.' '.$m->sender->cognome) : '-';
                                    $ticketCode = $m->ticket ? $m->ticket->codice_ticket : '-';
                                    $isUnread = ((int)$m->is_read===0);
                                ?>
                                    <tr>
                                        <td><?= Yii::$app->formatter->asDatetime($m->created_at,'php:d/m/Y H:i') ?></td>
                                        <td><?= Html::encode($mittente) ?></td>
                                        <td><?= Html::encode($ticketCode) ?></td>
                                        <td><?= Html::encode($m->subject) ?></td>
                                        <td><span class="badge <?= $isUnread?'bg-warning text-dark':'bg-secondary' ?>"><?= $isUnread?'Da leggere':'Letto' ?></span></td>
                                        <td class="text-end"><?= Html::a('Apri',['messages/view','id'=>$m->id],['class'=>'btn btn-sm btn-outline-primary']) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </article>
        <?php endif; ?>

        <!-- ADMIN PANELS -->
         
        <?php if($isAdminDashboard): ?>
            <article id="panel-admin-tickets" class="dashboard-panel" hidden>
                <div class="dashboard-panel-head">
                    <h2>Ultimi ticket</h2>
                    <?= Html::a('Pagina completa',['tickets/index'],['class'=>'btn btn-sm btn-outline-primary']) ?>
                </div>
                <?php if(empty($adminRecentTickets)): ?>
                    <p class="dashboard-empty">Nessun ticket disponibile.</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-sm table-hover table-striped align-middle">
                            <thead><tr><th>Codice</th><th>Stato</th><th>Priorita</th><th>Cliente</th><th>Data invio</th><th class="text-end">Azioni</th></tr></thead>
                            <tbody>
                                <?php foreach($adminRecentTickets as $t): ?>
                                    <tr>
                                        <td><?= Html::encode($t->codice_ticket) ?></td>
                                        <td><?= Html::encode($t->stato ?: '-') ?></td>
                                        <td><?= Html::encode($t->priorita ?: 'N/D') ?></td>
                                        <td><?= Html::encode($t->cliente ? $t->cliente->nome.' '.$t->cliente->cognome : '-') ?></td>
                                        <td><?= Html::encode($t->data_invio ?: '-') ?></td>
                                        <td class="text-end"><?= Html::a('Apri',['tickets/view','id'=>$t->id],['class'=>'btn btn-sm btn-outline-primary']) ?></td>
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