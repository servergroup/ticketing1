<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\Assegnazioni $model */

$this->title = 'Nuova assegnazione ticket';
$this->params['breadcrumbs'][] = ['label' => 'Assegnazioni ticket', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="page-shell page-shell--narrow">
    <div class="page-head">
        <div>
            <h1 class="page-title"><?= Html::encode($this->title) ?></h1>
            <p class="page-subtitle">Assegna il ticket a un operatore reparto.</p>
        </div>
    </div>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>

