<?php

use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\StringHelper;

/** @var yii\web\View $this */
/** @var app\models\ticketfunction $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Ticket';
$this->params['breadcrumbs'][] = $this->title;

$ruolo = Yii::$app->user->identity->ruolo;
$isAdmin = $ruolo === 'amministratore';
$canCreate = in_array($ruolo, ['amministratore', 'cliente'], true);

$total = (int)$dataProvider->getTotalCount();
?>

<div class="page-shell">
    <div class="page-head">
        <div>
            <h1 class="page-title">Gestione ticket</h1>
            <p class="page-subtitle">Totale risultati correnti: <strong><?= $total ?></strong></p>
        </div>
        <div class="page-actions">
            <?= Html::a('Tutti', ['tickets/index'], ['class' => 'btn btn-sm btn-outline-secondary']) ?>
            <?= Html::a('Aperti', ['tickets/open'], ['class' => 'btn btn-sm btn-outline-secondary']) ?>
            <?= Html::a('In lavorazione', ['tickets/lavorazione'], ['class' => 'btn btn-sm btn-outline-secondary']) ?>
            <?= Html::a('Chiusi', ['tickets/close'], ['class' => 'btn btn-sm btn-outline-secondary']) ?>
            <?= Html::a('Scaduti', ['tickets/scadence'], ['class' => 'btn btn-sm btn-outline-secondary']) ?>
            <?php if ($canCreate): ?>
                <?= Html::a('Nuovo ticket', ['tickets/new-ticket'], ['class' => 'btn btn-sm btn-success']) ?>
            <?php endif; ?>
        </div>
    </div>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'emptyText' => 'Nessun ticket trovato.',
        'id' => 'tabella',
        'summary' => '',
        'tableOptions' => ['class' => 'table table-sm table-hover table-striped text-center align-middle'],
        'columns' => [
            [
                'attribute' => 'codice_ticket',
                'headerOptions' => ['style' => 'width: 120px;'],
            ],
            [
                'attribute' => 'problema',
                'value' => function ($model) {
                    return StringHelper::truncateWords((string)$model->problema, 10);
                },
            ],
            [
                'attribute' => 'reparto',
                'filter' => Html::activeDropDownList($searchModel, 'reparto', [
                    '' => 'Tutti',
                    'sviluppo' => 'Sviluppo',
                    'ict' => 'ICT',
                ], ['class' => 'form-select form-select-sm']),
            ],
            [
                'attribute' => 'priorita',
                'format' => 'raw',
                'value' => function ($model) {
                    $map = [
                        'alta' => 'danger',
                        'media' => 'warning text-dark',
                        'bassa' => 'success',
                    ];
                    $key = strtolower((string)$model->priorita);
                    $class = $map[$key] ?? 'secondary';
                    return Html::tag('span', ucfirst((string)$model->priorita), ['class' => 'badge bg-' . $class]);
                },
                'filter' => Html::activeDropDownList($searchModel, 'priorita', [
                    '' => 'Tutte',
                    'alta' => 'Alta',
                    'media' => 'Media',
                    'bassa' => 'Bassa',
                ], ['class' => 'form-select form-select-sm']),
            ],
            [
                'attribute' => 'stato',
                'format' => 'raw',
                'value' => function ($model) {
                    $classMap = [
                        'aperto' => 'success',
                        'in lavorazione' => 'warning text-dark',
                        'chiuso' => 'secondary',
                        'scaduto' => 'danger',
                        'risolto' => 'primary',
                    ];
                    $stato = strtolower((string)$model->stato);
                    $class = $classMap[$stato] ?? 'info';
                    return Html::tag('span', ucfirst((string)$model->stato), ['class' => 'badge bg-' . $class]);
                },
                'filter' => Html::activeDropDownList($searchModel, 'stato', [
                    '' => 'Tutti',
                    'aperto' => 'Aperto',
                    'in lavorazione' => 'In lavorazione',
                    'chiuso' => 'Chiuso',
                    'scaduto' => 'Scaduto',
                ], ['class' => 'form-select form-select-sm']),
            ],
            [
                'attribute' => 'scadenza',
                'value' => function ($model) {
                    return $model->scadenza ?: '-';
                },
            ],
            [
                'class' => ActionColumn::class,
                'template' => '{view} {update} {message} {delete}',
                'header' => 'Azioni',
                'buttons' => [
                    'view' => function ($url, $model) {
                        return Html::a('<i class="fas fa-eye"></i>', ['tickets/view', 'id' => $model->id], [
                            'class' => 'btn btn-sm btn-outline-primary',
                            'title' => 'Visualizza',
                        ]);
                    },
                    'update' => function ($url, $model) use ($isAdmin, $ruolo) {
                        if (!$isAdmin && $ruolo !== 'cliente') {
                            return '';
                        }
                        return Html::a('<i class="fas fa-pen"></i>', ['tickets/update', 'id' => $model->id], [
                            'class' => 'btn btn-sm btn-outline-secondary',
                            'title' => 'Modifica',
                        ]);
                    },
                    'message' => function ($url, $model) {
                        return Html::a('<i class="fas fa-comments"></i>', ['messages/compose', 'ticketId' => $model->id], [
                            'class' => 'btn btn-sm btn-outline-success',
                            'title' => 'Invia messaggio sul ticket',
                        ]);
                    },
                    'delete' => function ($url, $model) use ($isAdmin, $ruolo) {
                        if (!$isAdmin && $ruolo !== 'cliente') {
                            return '';
                        }
                        return Html::a('<i class="fas fa-trash"></i>', ['tickets/delete', 'id' => $model->id], [
                            'class' => 'btn btn-sm btn-outline-danger',
                            'title' => 'Elimina',
                            'data-confirm' => 'Confermi l\'eliminazione del ticket?',
                            'data-method' => 'post',
                        ]);
                    },
                ],
                'contentOptions' => ['style' => 'white-space: nowrap; min-width: 170px;'],
            ],
        ],
    ]) ?>
</div>

