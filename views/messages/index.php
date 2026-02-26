<?php

use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\StringHelper;

/** @var yii\web\View $this */
/** @var yii\data\ActiveDataProvider $dataProvider */
/** @var string $box */
/** @var string $title */
/** @var int $unreadCount */

$this->title = $title;
$this->params['breadcrumbs'][] = 'Messaggi';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="page-shell">
    <div class="page-head">
        <div>
            <h1 class="page-title"><?= Html::encode($this->title) ?></h1>
            <p class="page-subtitle">
                Inbox non letta: <strong><?= (int)$unreadCount ?></strong>
            </p>
        </div>
        <div class="page-actions">
            <?= Html::a('Ricevuti', ['index', 'box' => 'inbox'], [
                'class' => 'btn btn-sm ' . ($box === 'inbox' ? 'btn-primary' : 'btn-outline-primary'),
            ]) ?>
            <?= Html::a('Inviati', ['index', 'box' => 'sent'], [
                'class' => 'btn btn-sm ' . ($box === 'sent' ? 'btn-primary' : 'btn-outline-primary'),
            ]) ?>
            <?= Html::a('Nuovo messaggio', ['compose'], ['class' => 'btn btn-sm btn-success']) ?>
        </div>
    </div>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'id' => 'tabella',
        'emptyText' => 'Nessun messaggio disponibile.',
        'summary' => '',
        'tableOptions' => ['class' => 'table table-sm table-hover table-striped text-center align-middle'],
        'columns' => [
            [
                'attribute' => 'created_at',
                'label' => 'Data',
                'value' => function ($model) {
                    return Yii::$app->formatter->asDatetime($model->created_at, 'php:d/m/Y H:i');
                },
            ],
            [
                'label' => ($box === 'sent') ? 'Destinatario' : 'Mittente',
                'value' => function ($model) use ($box) {
                    $user = ($box === 'sent') ? $model->recipient : $model->sender;
                    if ($user === null) {
                        return '-';
                    }
                    return trim($user->nome . ' ' . $user->cognome) . ' (' . $user->ruolo . ')';
                },
            ],
            [
                'label' => 'Ticket',
                'value' => function ($model) {
                    return $model->ticket ? $model->ticket->codice_ticket : '-';
                },
            ],
            [
                'attribute' => 'subject',
                'format' => 'raw',
                'value' => function ($model) {
                    $subject = Html::encode($model->subject);
                    if ((int)$model->is_read === 0 && (int)$model->recipient_id === (int)Yii::$app->user->id) {
                        $subject = '<strong>' . $subject . '</strong>';
                    }
                    return Html::a($subject, ['view', 'id' => $model->id]);
                },
            ],
            [
                'label' => 'Anteprima',
                'value' => function ($model) {
                    return StringHelper::truncateWords($model->body, 12);
                },
            ],
            [
                'label' => 'Stato',
                'format' => 'raw',
                'value' => function ($model) use ($box) {
                    if ($box === 'sent') {
                        return Html::tag('span', 'Inviato', ['class' => 'badge bg-info']);
                    }
                    $class = ((int)$model->is_read === 1) ? 'bg-secondary' : 'bg-warning text-dark';
                    $label = ((int)$model->is_read === 1) ? 'Letto' : 'Da leggere';
                    return Html::tag('span', $label, ['class' => 'badge ' . $class]);
                },
            ],
            [
                'label' => '',
                'format' => 'raw',
                'value' => function ($model) {
                    return Html::a('Apri', ['view', 'id' => $model->id], ['class' => 'btn btn-sm btn-outline-primary']);
                },
            ],
        ],
    ]) ?>
</div>

