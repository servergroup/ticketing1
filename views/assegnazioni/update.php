<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\Assegnazioni $model */

$this->title = 'Update Assegnazioni: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Assegnazionis', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="assegnazioni-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
