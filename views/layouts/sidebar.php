<?php

use app\models\TicketMessage;
use yii\helpers\Html;

$identity = Yii::$app->user->identity;
$ruolo = $identity->ruolo;
$unreadMessages = 0;
try {
    if (Yii::$app->db->schema->getTableSchema(TicketMessage::tableName(), true) !== null) {
        $unreadMessages = (int)TicketMessage::find()
            ->where(['recipient_id' => $identity->id, 'is_read' => 0])
            ->count();
    }
} catch (\Throwable $e) {
    $unreadMessages = 0;
}
?>
<div class="profiling">
<?php
$avatarPath = Yii::getAlias('@web/img/profile.png');
if (!empty($identity->immagine)) {
    $avatarPath = Yii::getAlias('@web/img/upload/' . $identity->immagine);
}
?>
</div>
<?php
$messageLabel = 'Ricevuti';
if ($unreadMessages > 0) {
    $messageLabel .= ' <span class="badge badge-warning right">' . $unreadMessages . '</span>';
}

$communicationMenu = [
    'label' => 'Messaggi',
    'icon' => 'fas fa-comments',
    'items' => [
        ['label' => $messageLabel, 'icon' => 'fas fa-inbox', 'url' => ['messages/index']],
        ['label' => 'Inviati', 'icon' => 'fas fa-paper-plane', 'url' => ['messages/index', 'box' => 'sent']],
        ['label' => 'Nuovo messaggio', 'icon' => 'fas fa-pen', 'url' => ['messages/compose']],
    ],
];

$menuItems = [];

if ($ruolo === 'amministratore') {
    $menuItems = [
        ['label' => 'Dashboard', 'icon' => 'fas fa-home', 'url' => ['site/index']],
        [
            'label' => 'Ticket',
            'icon' => 'fas fa-ticket-alt',
            'items' => [
                ['label' => 'Tutti i ticket', 'icon' => 'fas fa-list', 'url' => ['tickets/index']],
                ['label' => 'Ticket aperti', 'icon' => 'fas fa-folder-open', 'url' => ['tickets/open']],
                ['label' => 'In lavorazione', 'icon' => 'fas fa-tools', 'url' => ['tickets/lavorazione']],
                ['label' => 'Ticket chiusi', 'icon' => 'fas fa-check', 'url' => ['tickets/close']],
                ['label' => 'Ticket scaduti', 'icon' => 'fas fa-exclamation-triangle', 'url' => ['tickets/scadence']],
                ['label' => 'Nuovo ticket', 'icon' => 'fas fa-plus', 'url' => ['tickets/new-ticket']],
                ['label' => 'Tempi ticket', 'icon' => 'fas fa-clock', 'url' => ['tempi/index']],
            ],
        ],
        [
            'label' => 'Gestione utenti',
            'icon' => 'fas fa-users',
            'items' => [
                ['label' => 'Nuovo operatore/admin', 'icon' => 'fas fa-user-plus', 'url' => ['site/register']],
                ['label' => 'Utenti in attesa', 'icon' => 'fas fa-user-clock', 'url' => ['admin/attese']],
                ['label' => 'Utenti bloccati', 'icon' => 'fas fa-user-slash', 'url' => ['admin/block']],
                ['label' => 'Verifica ruoli', 'icon' => 'fas fa-user-shield', 'url' => ['admin/verify-ruolo']],
            ],
        ],
        $communicationMenu,
    ];
} elseif (in_array($ruolo, ['ict', 'itc', 'developer', 'sistemista'], true)) {
    $menuItems = [
        ['label' => 'Dashboard', 'icon' => 'fas fa-home', 'url' => ['site/index']],
        [
            'label' => 'Ticket',
            'icon' => 'fas fa-ticket-alt',
            'items' => [
                ['label' => 'Ticket assegnati', 'icon' => 'fas fa-briefcase', 'url' => ['assegnazioni/my-ticket']],
                ['label' => 'Ticket reparto', 'icon' => 'fas fa-layer-group', 'url' => ['tickets/my-reparto']],
                ['label' => 'Ticket reparto aperti', 'icon' => 'fas fa-folder-open', 'url' => ['tickets/my-reparto-open']],
            ],
        ],
        $communicationMenu,
    ];
} elseif ($ruolo === 'cliente') {
    $menuItems = [
        ['label' => 'Dashboard', 'icon' => 'fas fa-home', 'url' => ['site/index']],
        [
            'label' => 'Ticket',
            'icon' => 'fas fa-ticket-alt',
            'items' => [
                ['label' => 'Nuovo ticket', 'icon' => 'fas fa-plus', 'url' => ['tickets/new-ticket']],
                ['label' => 'I miei ticket', 'icon' => 'fas fa-history', 'url' => ['tickets/my-ticket']],
            ],
        ],
        ['label' => 'Contattaci', 'icon' => 'fas fa-life-ring', 'url' => ['site/contact']],
        $communicationMenu,
    ];
}
?>

<aside class="main-sidebar sidebar-dark-primary elevation-3 corporate-sidebar">
    <a href="<?= \yii\helpers\Url::to(['site/index']) ?>" class="brand-link corporate-brand">
        <img src="<?= Yii::getAlias('@web/img/taglio_dataseed.png') ?>" alt="Dataseed">
    </a>

    <div class="sidebar">
        <div class="user-panel mt-3 pb-3 mb-3 d-flex align-items-center corporate-user">
            <div class="image">
                <img src="<?= Html::encode($avatarPath) ?>" class="img-circle elevation-2" alt="Avatar utente">
            </div>
            <div class="info pr-2">
                <a href="<?= \yii\helpers\Url::to(['site/account']) ?>" class="d-block">
                    <?= Html::encode($identity->username) ?>
                </a>
                <small class="text-muted text-uppercase"><?= Html::encode($ruolo) ?></small>
            </div>
            <div class="ml-auto">
                <?= Html::a('<i class="fas fa-sign-out-alt"></i>', ['site/logout'], [
                    'class' => 'btn btn-tool text-white',
                    'title' => 'Logout',
                    'data-method' => 'post',
                ]) ?>
            </div>
        </div>

        <nav class="mt-2">
            <?= \hail812\adminlte\widgets\Menu::widget([
                'encodeLabels' => false,
                'items' => $menuItems,
            ]) ?>
        </nav>
    </div>
</aside>

<style>
    /* Quando la sidebar è collassata */
.image img {
    width: 30px;
    height: 30px;
    transition: all 0.3s ease;
}


</style>