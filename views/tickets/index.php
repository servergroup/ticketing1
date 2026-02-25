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
?>
<div class="ticket-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php if (Yii::$app->user->identity->ruolo === 'amministratore' || Yii::$app->user->identity->ruolo === 'cliente'): ?>
        <p>
            <?= Html::a(' <icon class="fas fa-plus"> Crea Ticket </icon>', ['new-ticket'], ['class' => 'btn btn-success']) ?>
        </p>
    <?php endif; ?>

    <?php // echo $this->render('_search', ['model' => $searchModel]); 
    ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
       
        'tableOptions' => ['class' => 'table table-striped table-bordered table-hover shadow-sm'],
        'headerRowOptions' => ['class' => 'table-dark'],
        'columns' => [

           
            'problema',
            'ambito',
            'codice_ticket',

            [
                'attribute' => 'stato',
                'format' => 'raw',
                'value' => function ($model) {
                    $color = match ($model->stato) {
                        'aperto' => 'badge bg-success',
                        'in lavorazione' => 'badge bg-warning text-dark',
                        'chiuso' => 'badge bg-secondary',
                        default => 'badge bg-info',
                    };
                    return "<span class='$color'>" . Html::encode($model->stato) . "</span>";
                }
            ],

            [
                'class' => ActionColumn::className(),
                'template' => '{menu}',
                'buttons' => [
                    'menu' => function ($url, Ticket $model) {

                        $items = [];

                        // Visualizza
                        $items[] = Html::a(
                            '<i class="bi bi-eye me-2"></i> Visualizza',
                            ['view', 'id' => $model->id],
                            ['class' => 'dropdown-item']
                        );

                        // Modifica
                        $items[] = Html::a(
                            '<i class="bi bi-pencil me-2"></i> Modifica',
                            ['update', 'id' => $model->id],
                            ['class' => 'dropdown-item']
                        );

                        // Elimina
                        $items[] = Html::a(
                            '<i class="bi bi-trash me-2"></i> Elimina',
                            ['delete', 'id' => $model->id],
                            [
                                'class' => 'dropdown-item text-danger',
                                'data-method' => 'post',
                                'data-confirm' => 'Sei sicuro di voler eliminare questo ticket?'
                            ]
                        );

                        // Assegna (solo admin)
                        if (Yii::$app->user->identity->ruolo === 'amministratore') {
                            $items[] = Html::a(
                                '<i class="bi bi-person-check me-2"></i> Assegna operatore',
                                ['admin/delegate', 'id' => $model->id],
                                [
                                    'class' => 'dropdown-item',
                                    'data-method' => 'post',
                                    'data-confirm' => 'Vuoi assegnare questo ticket?'
                                ]
                            );
                        }

                      return '
<div class="dropdown">

    <button class="btn btn-sm dropdown-toggle no-caret kebab-btn" 
            type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        <span style="font-size:22px; line-height:0;">⋮</span>
    </button>

    <div class="dropdown-menu dropdown-menu-right shadow-sm kebab-menu">
        ' . implode('', $items) . '
    </div>

</div>';

                    }
                ]
            ],
        ],
    ]); ?>



</div>

<style>

    h1{
        text-align:center;
    }
    .table-hover tbody tr:hover {
        background-color: #f5f7fa !important;
    }

    .table-dark th {
        background-color: #cfd4da !important;
        color: #fff !important;
        text-transform: uppercase;
        letter-spacing: .5px;
    }

    .btn-sm i {
        font-size: 1rem;
    }

    .ticket-index h1 {
        font-weight: 600;
        color: #2c3e50;
        margin-bottom: 25px;
    }

    .no-caret::after {
        border:none;
    display: none !important;
}

.kebab-btn {
    background: transparent !important;
    border: none !important;
    box-shadow: none !important;
    padding: 4px 6px;
}



.kebab-menu {
    transform: translateX(-25%);
    min-width: 150px;
}

h1{
    text-align: center;
}
</style>