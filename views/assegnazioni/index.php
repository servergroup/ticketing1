<?php

use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\assegnazioniTable $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Assegnazioni ticket';
$this->params['breadcrumbs'][] = $this->title;

$departmentAttribute = $searchModel->hasAttribute('reparto') ? 'reparto' : 'ambito';
?>

<div class="page-shell">
    <div class="page-head">
        <div>
            <h1 class="page-title"><?= Html::encode($this->title) ?></h1>
            <p class="page-subtitle">Elenco assegnazioni operative e accesso rapido a risoluzione/messaggi ticket.</p>
        </div>
        <div class="page-actions">
            <?= Html::a('Le mie assegnazioni', ['assegnazioni/my-ticket'], ['class' => 'btn btn-sm btn-outline-secondary']) ?>
            <?= Html::a('Ticket reparto', ['assegnazioni/my-reparto'], ['class' => 'btn btn-sm btn-outline-secondary']) ?>
        </div>
    </div>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'id' => 'tabella',
        'summary' => '',
        'emptyText' => 'Nessuna assegnazione trovata.',
        'tableOptions' => ['class' => 'table table-sm table-hover table-striped text-center align-middle'],
        'columns' => [
            'id',
            'codice_ticket',
            [
                'label' => 'Operatore',
                'value' => static function ($model) {
                    if ($model->operatore === null) {
                        return 'Operatore #' . (int)$model->id_operatore;
                    }
                    $nome = trim($model->operatore->nome . ' ' . $model->operatore->cognome);
                    return $nome . ' - ' . $model->operatore->ruolo;
                },
            ],
            [
                'attribute' => $departmentAttribute,
                'label' => 'Reparto',
                'value' => static function ($model) {
                    if ($model->hasAttribute('reparto') && !empty($model->reparto)) {
                        return $model->reparto;
                    }
                    if ($model->hasAttribute('ambito') && !empty($model->ambito)) {
                        return $model->ambito;
                    }
                    return '-';
                },
                'filter' => Html::activeDropDownList($searchModel, $departmentAttribute, [
                    '' => 'Tutti',
                    'sviluppo' => 'Sviluppo',
                    'ict' => 'Sistemistica (ICT)',
                ], ['class' => 'form-select form-select-sm']),
            ],
            [
                'class' => ActionColumn::class,
                'template' => '{view} {resolve} {message} {delete}',
                'header' => 'Azioni',
                'buttons' => [
                    'view' => function ($url, $model) {
                        return Html::a('<i class="fas fa-eye"></i>', ['assegnazioni/view', 'id' => $model->id], [
                            'class' => 'btn btn-sm btn-outline-primary',
                            'title' => 'Dettaglio assegnazione',
                        ]);
                    },
                    'resolve' => function ($url, $model) {
                        if (!$model->codiceTicket) {
                            return '';
                        }
                        return Html::a('<i class="fas fa-check"></i>', ['tickets/resolve', 'id' => $model->codiceTicket->id], [
                            'class' => 'btn btn-sm btn-outline-success',
                            'title' => 'Segna ticket come risolto',
                            'data-method' => 'post',
                            'data-confirm' => 'Confermi la risoluzione del ticket?',
                        ]);
                    },
                    'message' => function ($url, $model) {
                        if (!$model->codiceTicket) {
                            return '';
                        }
                        return Html::a('<i class="fas fa-comments"></i>', ['messages/compose', 'ticketId' => $model->codiceTicket->id], [
                            'class' => 'btn btn-sm btn-outline-info',
                            'title' => 'Invia messaggio sul ticket',
                        ]);
                    },
                    'delete' => function ($url, $model) {
                        return Html::a('<i class="fas fa-trash"></i>', ['assegnazioni/delete', 'id' => $model->id], [
                            'class' => 'btn btn-sm btn-outline-danger',
                            'title' => 'Rimuovi assegnazione',
                            'data-method' => 'post',
                            'data-confirm' => 'Confermi la rimozione dell\'assegnazione?',
                        ]);
                    },
                ],
                'contentOptions' => ['style' => 'white-space: nowrap; min-width: 190px;'],
            ],
        ],
    ]) ?>
</div>
