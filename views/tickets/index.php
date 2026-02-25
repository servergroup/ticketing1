<?php

use app\models\Ticket;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var app\models\ticketfunction $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Tutti i ticket';
$this->params['breadcrumbs'][] = $this->title;

// Colori stato ticket
$statiTicket = [
    'aperto' => ['label' => 'Aperto', 'class' => 'badge bg-success'],
    'in lavorazione' => ['label' => 'In Lavorazione', 'class' => 'badge bg-warning text-dark'],
    'chiuso' => ['label' => 'Chiuso', 'class' => 'badge bg-secondary'],
    'scaduto' => ['label' => 'Scaduto', 'class' => 'badge bg-danger'],
];
?>
<div class="ticket-index">

    <div class="d-flex justify-content-between align-items-center mb-3" id='container-titolo'>
        <h1 class="h3" style="margin-left:650px;margin-bottom:60px;margin-top:50px;"><?= Html::encode($this->title) ?></h1>
        <?php if (Yii::$app->user->identity->ruolo === 'amministratore' || Yii::$app->user->identity->ruolo === 'cliente'): ?>
            <p><?= Html::a('<i class="bi bi-plus-lg"></i> Crea Ticket', ['new-ticket'], ['class' => 'btn btn-success btn-sm']) ?></p>
        <?php endif; ?>
    </div>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'emptyText' => 'Nessun ticket trovato.',
        'id' => 'tabella',
        'tableOptions' => ['class' => 'table table-sm table-hover table-striped table-borderless text-center align-middle'],
        'summary' => '',
        'columns' => [
            [
                'attribute' => 'codice_ticket',
                'headerOptions' => ['class' => 'text-center', 'style' => 'width: 120px; min-width: 120px;'],
                'contentOptions' => ['class' => 'text-center', 'style' => 'min-width: 120px;'],
            ],
            [
                'attribute' => 'problema',
                'headerOptions' => ['class' => 'text-center'],
                'contentOptions' => ['class' => 'text-center'],
            ],
            [
                'attribute' => 'reparto',
                'filter' => Html::activeDropDownList($searchModel, 'reparto', [
                    '' => 'Tutti',
                    'sviluppo' => 'sviluppo',
                    'ict' => 'ict',
                    
                ], ['class' => 'form-select form-select-sm']),
                'headerOptions' => ['class' => 'text-center'],
                'contentOptions' => ['class' => 'text-center'],
            ],
            [
                'attribute' => 'stato',
                'format' => 'raw',
                'value' => function ($model) use ($statiTicket) {
                    $stato = $statiTicket[$model->stato] ?? ['label' => ucfirst($model->stato), 'class' => 'badge bg-info'];
                    return Html::tag('span', $stato['label'], ['class' => $stato['class']]);
                },
                'filter' => Html::activeDropDownList($searchModel, 'stato', [
                    '' => 'Tutti',
                    'aperto' => 'Aperto',
                    'in lavorazione' => 'In Lavorazione',
                    'chiuso' => 'Chiuso',
                    'scaduto' => 'Scaduto',
                ], ['class' => 'form-select form-select-sm']),
                'headerOptions' => ['class' => 'text-center'],
                'contentOptions' => ['class' => 'text-center'],
            ],
            [
                'class' => ActionColumn::className(),
                'template' => '{actions}',
                'header' => '<i class="bi bi-gear"></i>',
                'headerOptions' => ['class' => 'text-center', 'style' => 'width: 50px;'],
                'contentOptions' => ['class' => 'text-center'],
                'buttons' => [
                    'actions' => function ($url, $model, $key) {
                        $ruolo = Yii::$app->user->identity->ruolo;
                        $isAdmin = $ruolo === 'amministratore';
                        
                        $items = [];
                        
                        // Visualizza (sempre visibile)
                        $items[] = [
                            'label' => '<i class="bi bi-eye me-2"></i> Visualizza',
                            'url' => ['view', 'id' => $model->id],
                        ];
                        
                        // Modifica (admin e cliente)
                        if ($isAdmin || $ruolo === 'cliente') {
                            $items[] = [
                                'label' => '<i class="bi bi-pencil me-2"></i> Modifica',
                                'url' => ['update', 'id' => $model->id],
                            ];
                        }
                        
                        // Assegna operatore (solo admin)
                        if ($isAdmin) {
                            $items[] = [
                                'label' => '<i class="bi bi-person-check text-primary me-2"></i> Assegna',
                                'url' => ['admin/delegate', 'id' => $model->id],
                                'linkOptions' => ['data' => ['method' => 'post', 'confirm' => 'Vuoi assegnare questo ticket?']],
                            ];
                        }
                        
                        // Elimina (admin e cliente)
                        if ($isAdmin || $ruolo === 'cliente') {
                            $items[] = [
                                'label' => '<i class="bi bi-trash text-danger me-2"></i> Elimina',
                                'url' => ['delete', 'id' => $model->id],
                                'linkOptions' => ['data' => ['method' => 'post', 'confirm' => 'Sei sicuro di voler eliminare questo ticket?']],
                            ];
                        }
                        
                        if (empty($items)) {
                            $items[] = ['label' => '<i class="bi bi-dash-lg me-2"></i> Nessuna azione', 'url' => '#', 'options' => ['class' => 'disabled']];
                        }
                        
                        $menuHtml = Html::tag('ul', implode('', array_map(function($item) {
                            $options = $item['options'] ?? [];
                            $options['class'] = ($options['class'] ?? '') . ' dropdown-item';
                            if (isset($item['linkOptions']['data'])) {
                                $options['data-confirm'] = $item['linkOptions']['data']['confirm'] ?? null;
                                $options['data-method'] = $item['linkOptions']['data']['method'] ?? null;
                            }
                            return Html::tag('li', Html::a($item['label'], $item['url'], $options));
                        }, $items)), ['class' => 'dropdown-menu dropdown-menu-end shadow-sm']);
                        
                        return Html::tag('span',
                            Html::button(Html::tag('span', '⋮', ['class' => 'dots-menu']), [
                                'class' => 'btn btn-sm py-0 px-1',
                                'data' => ['bs-toggle' => 'dropdown'],
                                'aria' => ['expanded' => 'false'],
                            ]) . $menuHtml,
                            ['class' => 'dropdown']
                        );
                    },
                ],
            ],
        ],
    ]); ?>

</div>

<style>
    .table { border: none; }
    .table td, .table th { border: none; vertical-align: middle; padding: 0.3rem 0.5rem; }
    .table-sm td, .table-sm th { padding: 0.25rem 0.5rem; }
    .dots-menu { font-size: 16px; color: #6c757d; background: none; border: none; cursor: pointer; padding: 2px 6px; transition: color 0.2s; }
    .dots-menu:hover { color: #0d6efd; }
    .dropdown .btn { border: none; box-shadow: none; }
    .dropdown .btn:focus { box-shadow: none; }
    .dropdown-menu { border: 1px solid #dee2e6; min-width: 160px; }
    .dropdown-item { padding: 0.4rem 1rem; font-size: 0.875rem; }
    .ticket-index .d-flex { gap: 0.5rem; }
    .ticket-index h1 { margin-bottom: 0; }
    .ticket-index p { margin-bottom: 1rem; }
    .btn-group > .btn { margin-left: 0.25rem; }
    .badge { font-size: 0.75rem; }
    
</style>