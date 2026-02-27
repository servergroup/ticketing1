<?php

use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Turni operatori';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="page-shell">
    <div class="page-head">
        <div>
            <h1 class="page-title"><?= Html::encode($this->title) ?></h1>
            <p class="page-subtitle">Pianificazione orari e stato servizio dei membri operativi.</p>
        </div>
        <div class="page-actions">
            <?= Html::a('Nuovo turno', ['turni/create'], ['class' => 'btn btn-sm btn-success']) ?>
        </div>
    </div>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'id' => 'tabella',
        'summary' => '',
        'emptyText' => 'Nessun turno presente.',
        'tableOptions' => ['class' => 'table table-sm table-hover table-striped text-center align-middle'],
        'columns' => [
            'id',
            'id_operatore',
            'entrata',
            'uscita',
            'pausa',
            [
                'attribute' => 'stato',
                'value' => function ($model) {
                    return $model->stato ?: '-';
                },
            ],
            [
                'class' => ActionColumn::class,
                'template' => '{view} {update} {delete}',
                'buttons' => [
                    'view' => function ($url, $model) {
                        return Html::a('<i class="fas fa-eye"></i>', ['turni/view', 'id' => $model->id], [
                            'class' => 'btn btn-sm btn-outline-primary',
                            'title' => 'Visualizza',
                        ]);
                    },
                    'update' => function ($url, $model) {
                        return Html::a('<i class="fas fa-pen"></i>', ['turni/update', 'id' => $model->id], [
                            'class' => 'btn btn-sm btn-outline-secondary',
                            'title' => 'Modifica',
                        ]);
                    },
                    'delete' => function ($url, $model) {
                        return Html::a('<i class="fas fa-trash"></i>', ['turni/delete', 'id' => $model->id], [
                            'class' => 'btn btn-sm btn-outline-danger',
                            'title' => 'Elimina',
                            'data-method' => 'post',
                            'data-confirm' => 'Eliminare questo turno?',
                        ]);
                    },
                ],
                'contentOptions' => ['style' => 'white-space: nowrap; min-width: 150px;'],
            ],
        ],
    ]) ?>
</div>

