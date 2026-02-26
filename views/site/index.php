<?php

use yii\helpers\Html;
use yii\helpers\Url;

/** @var yii\web\View $this */
/** @var app\models\User $user */
/** @var int $countTicket */
/** @var app\models\Ticket|null $ultimoTicket */
/** @var array $dashboardStats */
/** @var int $unreadMessages */

$this->title = 'Dashboard';
$ruolo = $user->ruolo;
$nome = Yii::$app->user->identity->nome;

$actions = [];
if ($ruolo === 'cliente') {
    $actions = [
        ['label' => 'Apri ticket', 'url' => ['tickets/new-ticket'], 'icon' => 'fas fa-plus-circle', 'variant' => 'primary'],
        ['label' => 'I miei ticket', 'url' => ['tickets/my-ticket'], 'icon' => 'fas fa-ticket-alt', 'variant' => 'neutral'],
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
        ['label' => 'Ticket assegnati', 'url' => ['assegnazioni/my-ticket'], 'icon' => 'fas fa-briefcase', 'variant' => 'primary'],
        ['label' => 'Ticket reparto', 'url' => ['tickets/my-reparto'], 'icon' => 'fas fa-layer-group', 'variant' => 'neutral'],
        ['label' => 'Nuovo messaggio', 'url' => ['messages/compose'], 'icon' => 'fas fa-paper-plane', 'variant' => 'neutral'],
    ];
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
            <a class="quick-action quick-action-<?= Html::encode($action['variant']) ?>" href="<?= Url::to($action['url']) ?>">
                <i class="<?= Html::encode($action['icon']) ?>"></i>
                <span><?= Html::encode($action['label']) ?></span>
            </a>
        <?php endforeach; ?>
    </section>

    <?php if ($ultimoTicket): ?>
        <section class="last-ticket-card">
            <div>
                <h2>Ultimo ticket</h2>
                <p>
                    Codice <strong><?= Html::encode($ultimoTicket->codice_ticket) ?></strong>,
                    stato <strong><?= Html::encode($ultimoTicket->stato) ?></strong>,
                    priorità <strong><?= Html::encode($ultimoTicket->priorita ?: 'N/D') ?></strong>.
                </p>
            </div>
            <?= Html::a('Apri dettaglio', ['tickets/view', 'id' => $ultimoTicket->id], ['class' => 'btn btn-outline-primary']) ?>
        </section>
    <?php endif; ?>
</div>

