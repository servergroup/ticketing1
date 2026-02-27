<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\TicketMessage $model */

$this->title = 'Dettaglio messaggio';
$this->params['breadcrumbs'][] = 'Messaggi';
$this->params['breadcrumbs'][] = $this->title;

$senderName = $model->sender ? trim($model->sender->nome . ' ' . $model->sender->cognome) : 'Utente';
$recipientName = $model->recipient ? trim($model->recipient->nome . ' ' . $model->recipient->cognome) : 'Utente';
?>

<div class="page-shell message-view">
    <div class="page-head">
        <div>
            <h1 class="page-title"><?= Html::encode($model->subject) ?></h1>
            <p class="page-subtitle">
                <?= Yii::$app->formatter->asDatetime($model->created_at, 'php:d/m/Y H:i') ?>
            </p>
        </div>
        <div class="page-actions">
            <?= Html::a('Torna ai ricevuti', ['index'], ['class' => 'btn btn-sm btn-outline-primary']) ?>
            <?= Html::a('Rispondi', [
                'compose',
                'recipientId' => $model->sender_id,
                'ticketId' => $model->ticket_id,
            ], ['class' => 'btn btn-sm btn-success']) ?>
        </div>
    </div>

    <div class="message-meta-grid">
        <div class="meta-item">
            <span class="meta-label">Da</span>
            <span class="meta-value"><?= Html::encode($senderName) ?></span>
        </div>
        <div class="meta-item">
            <span class="meta-label">A</span>
            <span class="meta-value"><?= Html::encode($recipientName) ?></span>
        </div>
        <div class="meta-item">
            <span class="meta-label">Ticket</span>
            <span class="meta-value">
                <?php if ($model->ticket): ?>
                    <?= Html::a(
                        Html::encode($model->ticket->codice_ticket),
                        ['tickets/view', 'id' => $model->ticket->id]
                    ) ?>
                <?php else: ?>
                    Nessuno
                <?php endif; ?>
            </span>
        </div>
        <div class="meta-item">
            <span class="meta-label">Stato</span>
            <span class="meta-value">
                <?= ((int)$model->is_read === 1) ? 'Letto' : 'Da leggere' ?>
            </span>
        </div>
    </div>

    <article class="message-body">
        <?= nl2br(Html::encode($model->body)) ?>
    </article>
</div>
