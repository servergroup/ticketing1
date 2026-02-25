<?php

use app\models\User;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var app\models\UserTable $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */
/** @var string|null $mode */
/** @var string|null $title */

$this->title = $title ?? 'Gestione Utenti';
$this->params['breadcrumbs'][] = $this->title;

$stati = [
    0 => ['label' => 'In attesa', 'class' => 'badge bg-warning text-dark'],
    1 => ['label' => 'Assegnato', 'class' => 'badge bg-success'],
    'bloccato' => ['label' => 'Bloccato', 'class' => 'badge bg-danger'],
];
?>
<div class="user-index">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h3"><?= Html::encode($this->title) ?></h1>
        <div class="btn-group btn-group-sm">
            <?= Html::a('Tutti', ['admin/index', 'mode' => 'all'], ['class' => 'btn btn-outline-secondary']) ?>
            <?= Html::a('In attesa', ['admin/attese', 'mode' => 'pending'], ['class' => 'btn btn-outline-secondary']) ?>
            <?= Html::a('Bloccati', ['admin/block', 'mode' => 'blocked'], ['class' => 'btn btn-outline-secondary']) ?>
            <?= Html::a('Operatori', ['admin/view-operatori'], ['class' => 'btn btn-outline-secondary']) ?>
        </div>
    </div>

    <p><?= Html::a('<i class="bi bi-plus-lg"></i> Nuovo utente', ['site/register'], ['class' => 'btn btn-success btn-sm']) ?></p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'emptyText' => 'Nessun utente trovato.',
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
                'attribute' => 'nome',
                'value' => fn($model) => Html::encode($model->nome . ' ' . $model->cognome),
                'headerOptions' => ['class' => 'text-center'],
                'contentOptions' => ['class' => 'text-center'],
            ],
            [
                'attribute' => 'username',
                'headerOptions' => ['class' => 'text-center'],
                'contentOptions' => ['class' => 'text-center'],
            ],
            [
                'attribute' => 'ruolo',
                'filter' => Html::activeDropDownList($searchModel, 'ruolo', [
                    '' => 'Tutti',
                    'developer' => 'Developer',
                    'ict' => 'ICT',
                    'amministratore' => 'Amministratore',
                ], ['class' => 'form-select form-select-sm']),
                'headerOptions' => ['class' => 'text-center'],
                'contentOptions' => ['class' => 'text-center'],
            ],
            [
                'header' => 'Stato',
                'format' => 'raw',
                'value' => function($model) use ($stati) {
                    $approv = (int) $model->approvazione;
                    $blocco = (int) $model->blocco;
                    
                    if ($blocco != 0) {
                        $stato = $stati['bloccato'];
                    } elseif ($approv == 1) {
                        $stato = $stati[1];
                    } else {
                        $stato = $stati[0];
                    }
                    
                    return Html::tag('span', $stato['label'], ['class' => $stato['class']]);
                },
                'filter' => Html::activeDropDownList($searchModel, 'approvazione', [
                    '' => 'Tutti',
                    0 => 'In attesa',
                    1 => 'Assegnato',
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
                        $approv = (int) $model->approvazione;
                        $blocco = (int) $model->blocco;
                        
                        $items = [];
                        
                        $items[] = [
                            'label' => '<i class="bi bi-eye me-2"></i> Visualizza',
                            'url' => ['admin/view', 'id' => $model->id],
                        ];
                        
                        if ($approv === 0 && $blocco === 0) {
                            $items[] = [
                                'label' => '<i class="bi bi-check-lg text-success me-2"></i> Assegna',
                                'url' => ['admin/approve', 'id' => $model->id],
                                'linkOptions' => ['data' => ['method' => 'post', 'confirm' => 'Sei sicuro di voler assegnare questo utente?']],
                            ];
                        }
                        
                        if ($approv === 1 && $blocco === 0) {
                            $items[] = [
                                'label' => '<i class="bi bi-x-lg text-warning me-2"></i> Revoca',
                                'url' => ['admin/reset-login', 'id' => $model->id],
                                'linkOptions' => ['data' => ['method' => 'post', 'confirm' => 'Sei sicuro di voler revocare l\'assegnazione?']],
                            ];
                        }
                        
                        if ($blocco !== 0) {
                            $items[] = [
                                'label' => '<i class="bi bi-unlock text-danger me-2"></i> Sblocca',
                                'url' => ['admin/unblock', 'id' => $model->id],
                                'linkOptions' => ['data' => ['method' => 'post', 'confirm' => 'Sei sicuro di voler sbloccare questo utente?']],
                            ];
                        }
                        
                        if ($blocco === 0) {
                            $items[] = [
                                'label' => '<i class="bi bi-lock text-danger me-2"></i> Blocca',
                                'url' => ['admin/block', 'id' => $model->id],
                                'linkOptions' => ['data' => ['method' => 'post', 'confirm' => 'Sei sicuro di voler bloccare questo utente?']],
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
    .user-index .d-flex { gap: 0.5rem; }
    .user-index h1 { margin-bottom: 0; }
    .user-index p { margin-bottom: 1rem; }
    .btn-group > .btn { margin-left: 0.25rem; }
</style>