<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var app\models\Assegnazioni $model */

$this->title = 'Assegnazione #' . (int)$model->id;
$this->params['breadcrumbs'][] = ['label' => 'Assegnazioni ticket', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);

$department = '-';
if ($model->hasAttribute('reparto') && !empty($model->reparto)) {
    $department = (string)$model->reparto;
} elseif ($model->hasAttribute('ambito') && !empty($model->ambito)) {
    $department = (string)$model->ambito;
}
?>

<div class="page-shell page-shell--narrow">
    <div class="page-head">
        <div>
            <h1 class="page-title"><?= Html::encode($this->title) ?></h1>
            <p class="page-subtitle">Dettaglio assegnazione operativa del ticket.</p>
        </div>
        <div class="page-actions">
            <?= Html::a('Torna elenco', ['assegnazioni/index'], ['class' => 'btn btn-sm btn-outline-secondary']) ?>
            <?php if ($model->codiceTicket !== null): ?>
                <?= Html::a('Apri ticket', ['tickets/view', 'id' => $model->codiceTicket->id], ['class' => 'btn btn-sm btn-outline-primary']) ?>
            <?php endif; ?>
        </div>
    </div>

    <div class="detail-card">
        <?= DetailView::widget([
            'model' => $model,
            'attributes' => [
                'id',
                'codice_ticket',
                [
                    'label' => 'Operatore',
                    'value' => function ($model) {
                        if ($model->operatore === null) {
                            return 'Operatore #' . (int)$model->id_operatore;
                        }
                        $nome = trim($model->operatore->nome . ' ' . $model->operatore->cognome);
                        return $nome . ' - ' . $model->operatore->ruolo;
                    },
                ],
                [
                    'label' => 'Reparto',
                    'value' => $department,
                ],
            ],
        ]) ?>
    </div>

    <div class="d-flex gap-2 flex-wrap mt-3">
        <?php if ($model->codiceTicket !== null): ?>
            <?= Html::a('Segna come risolto', ['tickets/resolve', 'id' => $model->codiceTicket->id], [
                'class' => 'btn btn-success',
                'data-method' => 'post',
                'data-confirm' => 'Confermi la risoluzione del ticket?',
            ]) ?>
            <?= Html::a('Invia messaggio', ['messages/compose', 'ticketId' => $model->codiceTicket->id], ['class' => 'btn btn-outline-info']) ?>
        <?php endif; ?>
        <?= Html::a('Rimuovi assegnazione', ['assegnazioni/delete', 'id' => $model->id], [
            'class' => 'btn btn-outline-danger',
            'data-method' => 'post',
            'data-confirm' => 'Confermi la rimozione dell\'assegnazione?',
        ]) ?>
    </div>
</div>

