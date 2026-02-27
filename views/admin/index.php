<?php

use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\UserTable $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */
/** @var string|null $mode */
/** @var string|null $title */

$this->title = $title ?? 'Gestione utenti';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="page-shell">
    <div class="page-head">
        <div>
            <h1 class="page-title"><?= Html::encode($this->title) ?></h1>
            <p class="page-subtitle">Gestione ruoli, abilitazioni e comunicazioni verso utenti interni/clienti.</p>
        </div>
        <div class="page-actions">
            <?= Html::a('Tutti', ['admin/index', 'mode' => 'all'], ['class' => 'btn btn-sm btn-outline-secondary']) ?>
            <?= Html::a('In attesa', ['admin/index', 'mode' => 'pending'], ['class' => 'btn btn-sm btn-outline-secondary']) ?>
            <?= Html::a('Bloccati', ['admin/index', 'mode' => 'blocked'], ['class' => 'btn btn-sm btn-outline-secondary']) ?>
            <?= Html::a('Operatori', ['admin/view-operatori'], ['class' => 'btn btn-sm btn-outline-secondary']) ?>
            <?= Html::a('Nuovo utente', ['site/register'], ['class' => 'btn btn-sm btn-success']) ?>
        </div>
    </div>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'id' => 'tabella',
        'summary' => '',
        'emptyText' => 'Nessun utente trovato.',
        'tableOptions' => ['class' => 'table table-sm table-hover table-striped text-center align-middle'],
        'columns' => [
            'id',
            [
                'label' => 'Nome',
                'value' => function ($model) {
                    return trim($model->nome . ' ' . $model->cognome);
                },
            ],
            'username',
            'email:email',
            [
                'attribute' => 'ruolo',
                'filter' => Html::activeDropDownList($searchModel, 'ruolo', [
                    '' => 'Tutti',
                    'cliente' => 'Cliente',
                    'developer' => 'Developer',
                    'ict' => 'ICT',
                    'itc' => 'ITC',
                    'amministratore' => 'Amministratore',
                ], ['class' => 'form-select form-select-sm']),
            ],
            [
                'label' => 'Stato',
                'format' => 'raw',
                'value' => function ($model) {
                    if ((int)$model->blocco !== 0) {
                        return Html::tag('span', 'Bloccato', ['class' => 'badge bg-danger']);
                    }

                    if ((int)$model->approvazione === 1) {
                        return Html::tag('span', 'Approvato', ['class' => 'badge bg-success']);
                    }

                    return Html::tag('span', 'In attesa', ['class' => 'badge bg-warning text-dark']);
                },
            ],
            [
                'class' => ActionColumn::class,
                'template' => '{view} {approve} {revoke} {reset} {message}',
                'header' => 'Azioni',
                'buttons' => [
                    'view' => function ($url, $model) {
                        return Html::a('<i class="fas fa-eye"></i>', ['admin/view', 'id' => $model->id], [
                            'class' => 'btn btn-sm btn-outline-primary',
                            'title' => 'Dettaglio',
                        ]);
                    },
                    'approve' => function ($url, $model) {
                        if ((int)$model->approvazione === 1) {
                            return '';
                        }
                        return Html::a('<i class="fas fa-user-check"></i>', ['admin/approve', 'id' => $model->id], [
                            'class' => 'btn btn-sm btn-outline-success',
                            'title' => 'Approva',
                            'data-method' => 'post',
                            'data-confirm' => 'Approvi questo utente?',
                        ]);
                    },
                    'revoke' => function ($url, $model) {
                        if ((int)$model->approvazione !== 1) {
                            return '';
                        }
                        return Html::a('<i class="fas fa-user-times"></i>', ['admin/ritira', 'id' => $model->id], [
                            'class' => 'btn btn-sm btn-outline-warning',
                            'title' => 'Revoca approvazione',
                            'data-method' => 'post',
                            'data-confirm' => 'Revocare l\'approvazione?',
                        ]);
                    },
                    'reset' => function ($url, $model) {
                        return Html::a('<i class="fas fa-redo"></i>', ['admin/reset-login', 'id' => $model->id], [
                            'class' => 'btn btn-sm btn-outline-secondary',
                            'title' => 'Reset login',
                            'data-method' => 'post',
                            'data-confirm' => 'Reset dei tentativi login per questo utente?',
                        ]);
                    },
                    'message' => function ($url, $model) {
                        return Html::a('<i class="fas fa-paper-plane"></i>', ['messages/compose', 'recipientId' => $model->id], [
                            'class' => 'btn btn-sm btn-outline-info',
                            'title' => 'Invia messaggio',
                        ]);
                    },
                ],
                'contentOptions' => ['style' => 'white-space: nowrap; min-width: 220px;'],
            ],
        ],
    ]) ?>
</div>
