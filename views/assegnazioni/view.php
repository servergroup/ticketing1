<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var app\models\Assegnazioni $model */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Assegnazioni', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="assegnazioni-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Contrassegna come risolto', ['tickets/resolve', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
       
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'codice_ticket',
            'id_operatore',
            'ambito',
        ],
    ]) ?>

</div>
