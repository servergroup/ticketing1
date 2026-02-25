<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\Assegnazioni $model */

$this->title = 'Create Assegnazioni';
$this->params['breadcrumbs'][] = ['label' => 'Assegnazionis', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="assegnazioni-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
