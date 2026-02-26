<?php

use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\TempiTable $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Tempi lavorazione ticket';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="page-shell">
    <div class="page-head">
        <div>
            <h1 class="page-title"><?= Html::encode($this->title) ?></h1>
            <p class="page-subtitle">Monitoraggio SLA e durata effettiva delle attività su ticket.</p>
        </div>
    </div>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'id' => 'tabella',
        'summary' => '',
        'emptyText' => 'Nessun record tempi disponibile.',
        'tableOptions' => ['class' => 'table table-sm table-hover table-striped text-center align-middle'],
        'columns' => [
            'id',
            'id_ticket',
            'id_operatore',
            'ora_inizio',
            'ora_fine',
            [
                'attribute' => 'tempo_lavorazione',
                'label' => 'Tempo effettivo (sec)',
            ],
            [
                'attribute' => 'stato',
                'value' => function ($model) {
                    return $model->stato ?: '-';
                },
            ],
            [
                'class' => ActionColumn::class,
                'template' => '{view} {delete}',
                'buttons' => [
                    'view' => function ($url, $model) {
                        return Html::a('<i class="fas fa-eye"></i>', ['tempi/view', 'id' => $model->id], [
                            'class' => 'btn btn-sm btn-outline-primary',
                            'title' => 'Visualizza',
                        ]);
                    },
                    'delete' => function ($url, $model) {
                        return Html::a('<i class="fas fa-trash"></i>', ['tempi/delete', 'id' => $model->id], [
                            'class' => 'btn btn-sm btn-outline-danger',
                            'title' => 'Elimina record',
                            'data-method' => 'post',
                            'data-confirm' => 'Eliminare il record tempi?',
                        ]);
                    },
                ],
                'contentOptions' => ['style' => 'white-space: nowrap; min-width: 110px;'],
            ],
        ],
    ]) ?>
</div>

