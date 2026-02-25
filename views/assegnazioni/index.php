<?php

use app\models\Assegnazioni;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var app\models\assegnazioniTable $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Assegnazioni';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="assegnazioni-index">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h3"><?= Html::encode($this->title) ?></h1>
    </div>

      <?php if(Yii::$app->user->identity->ruolo=='developer'): ?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'emptyText' => 'Nessuna assegnazione trovata.',
        'id' => 'tabella',
        'tableOptions' => ['class' => 'table table-sm table-hover table-striped table-borderless text-center align-middle'],
        'summary' => '',
        'columns' => [
            [
                'attribute' => 'id',
                'headerOptions' => ['class' => 'text-center', 'style' => 'width: 70px; min-width: 70px;'],
                'contentOptions' => ['class' => 'text-center', 'style' => 'min-width: 70px;'],
            ],
            [
                'attribute' => 'codice_ticket',
                'headerOptions' => ['class' => 'text-center'],
                'contentOptions' => ['class' => 'text-center'],
            ],
            [
                'attribute' => 'id_operatore',
                'headerOptions' => ['class' => 'text-center'],
                'contentOptions' => ['class' => 'text-center'],
            ],
            [
                'attribute' => 'ambito',
              
                'filter' => Html::activeDropDownList($searchModel, 'ambito', [
                    
                    'sviluppo' => 'Sviluppo',
                 
                 
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
                        $items = [];
                        
                        // Visualizza
                        $items[] = [
                            'label' => '<i class="bi bi-eye me-2"></i> Visualizza',
                            'url' => ['assegnazioni/view', 'id' => $model->id],
                        ];
                        
                        // Risolvi (se assegnato)
                        $items[] = [
                            'label' => '<i class="bi bi-check-lg text-success me-2"></i> Risolvi',
                            'url' => ['tickets/resolve', 'id' => $model->id],
                            'linkOptions' => ['data' => ['method' => 'post', 'confirm' => 'Sei sicuro di voler contrassegnare come risolto?']],
                        ];
                        
                        // Elimina
                        $items[] = [
                            'label' => '<i class="bi bi-trash text-danger me-2"></i> Elimina',
                            'url' => ['assegnazioni/delete', 'id' => $model->id],
                            'linkOptions' => ['data' => ['method' => 'post', 'confirm' => 'Sei sicuro di voler eliminare questa assegnazione?']],
                        ];
                        
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
    <?php elseif( Yii::$app->user->identity->ruolo=='ict') : ?>

         <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'emptyText' => 'Nessuna assegnazione trovata.',
        'id' => 'tabella',
        'tableOptions' => ['class' => 'table table-sm table-hover table-striped table-borderless text-center align-middle'],
        'summary' => '',
        'columns' => [
            [
                'attribute' => 'id',
                'headerOptions' => ['class' => 'text-center', 'style' => 'width: 70px; min-width: 70px;'],
                'contentOptions' => ['class' => 'text-center', 'style' => 'min-width: 70px;'],
            ],
            [
                'attribute' => 'codice_ticket',
                'headerOptions' => ['class' => 'text-center'],
                'contentOptions' => ['class' => 'text-center'],
            ],
            [
                'attribute' => 'id_operatore',
                'headerOptions' => ['class' => 'text-center'],
                'contentOptions' => ['class' => 'text-center'],
            ],
            [
                'attribute' => 'ambito',
              
                'filter' => Html::activeDropDownList($searchModel, 'ambito', [
                    
                    'ict' => 'ict',
                 
                 
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
                        $items = [];
                        
                        // Visualizza
                        $items[] = [
                            'label' => '<i class="bi bi-eye me-2"></i> Visualizza',
                            'url' => ['assegnazioni/view', 'id' => $model->id],
                        ];
                        
                        // Risolvi (se assegnato)
                        $items[] = [
                            'label' => '<i class="bi bi-check-lg text-success me-2"></i> Risolvi',
                            'url' => ['tickets/resolve', 'id' => $model->id],
                            'linkOptions' => ['data' => ['method' => 'post', 'confirm' => 'Sei sicuro di voler contrassegnare come risolto?']],
                        ];
                        
                        // Elimina
                        $items[] = [
                            'label' => '<i class="bi bi-trash text-danger me-2"></i> Elimina',
                            'url' => ['assegnazioni/delete', 'id' => $model->id],
                            'linkOptions' => ['data' => ['method' => 'post', 'confirm' => 'Sei sicuro di voler eliminare questa assegnazione?']],
                        ];
                        
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
    <?php endif; ?>
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
    .assegnazioni-index .d-flex { gap: 0.5rem; }
    .assegnazioni-index h1 { margin-bottom: 0; }
    .assegnazioni-index p { margin-bottom: 1rem; }
    .btn-group > .btn { margin-left: 0.25rem; }
</style>