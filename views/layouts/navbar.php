<?php

use app\models\TicketMessage;
use yii\helpers\Html;

if (Yii::$app->user->isGuest) {
    return;
}

$unreadCount = 0;
try {
    if (Yii::$app->db->schema->getTableSchema(TicketMessage::tableName(), true) !== null) {
        $unreadCount = (int)TicketMessage::find()
            ->where(['recipient_id' => Yii::$app->user->id, 'is_read' => 0])
            ->count();
    }
} catch (\Throwable $e) {
    $unreadCount = 0;
}
?>

<nav class="main-header navbar navbar-expand navbar-light corporate-topbar">
    <ul class="navbar-nav">
        <li class="nav-item">
            <a class="nav-link" data-widget="pushmenu" href="#" role="button" aria-label="Apri menu laterale">
                <i class="fas fa-bars"></i>
            </a>
        </li>
        <li class="nav-item d-none d-sm-inline-block">
            <span class="nav-link corporate-page-title"><?= Html::encode($this->title ?: 'Dashboard') ?></span>
        </li>
    </ul>

    <ul class="navbar-nav ml-auto align-items-center">
        <li class="nav-item">
            <?= Html::a(
                '<i class="fas fa-envelope"></i><span class="badge badge-warning navbar-badge">' . $unreadCount . '</span>',
                ['messages/index'],
                ['class' => 'nav-link', 'title' => 'Messaggi ricevuti']
            ) ?>
        </li>
        <li class="nav-item d-none d-sm-inline-block">
            <?= Html::a('Nuovo messaggio', ['messages/compose'], ['class' => 'btn btn-sm btn-primary ml-2']) ?>
        </li>
    </ul>
</nav>
