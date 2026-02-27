<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;

/** @var yii\web\View $this */
/** @var app\models\User $model */
/** @var yii\widgets\ActiveForm $form */

?>

<div class="user-form card shadow-sm">
    <div class="card-body">
        <?php $form = ActiveForm::begin(['options' => ['autocomplete' => 'off']]); ?>

        <div class="row">
            <div class="col-md-6">
                <?= $form->field($model, 'nome')->textInput(['maxlength' => true])->label('Nome') ?>
            </div>
            <div class="col-md-6">
                <?= $form->field($model, 'cognome')->textInput(['maxlength' => true])->label('Cognome') ?>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <?= $form->field($model, 'email')->textInput(['maxlength' => true])->label('Email aziendale') ?>
            </div>
            <div class="col-md-6">
                <?= $form->field($model, 'telegram_username')->textInput(['maxlength' => true, 'placeholder' => '@utente'])->label('Telegram Username') ?>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <?= $form->field($model, 'telegram_chat_id')->textInput(['maxlength' => true, 'placeholder' => 'es. 123456789'])->label('Telegram Chat ID') ?>
            </div>
            <div class="col-md-6">
                <?= $form->field($model, 'ruolo')->dropDownList([
                    'cliente' => 'Cliente',
                    'amministratore' => 'Amministratore',
                    'developer' => 'Developer',
                    'ict' => 'ICT / Operatore'
                ], ['prompt' => 'Seleziona ruolo'])->label('Ruolo')
                
                ?>
            </div>
        </div>

        <div class="row">
            <div class="col-md-4">
                <?= $form->field($model, 'tentativi')->input('number', ['min' => 0])->label('Tentativi di accesso') ?>
            </div>
            <div class="col-md-4">
                <?php if ($model->approvazione): ?>
                    <?= $form->field($model, 'approvazione')->dropDownList([0 => 'Revoca autorizzazione di accesso'])->label('Autorizzazione:consentita') ?>
                <?php else : ?>
                    <?= $form->field($model, 'approvazione')->dropDownList([1 => 'Autorizza '. $model->nome . ' all\' accesso '])->label('Autorizzazione:non consentita') ?>
                <?php endif; ?>
            </div>
            <div class="col-md-4 d-flex align-items-center">
                <div>
                    <label class="form-label mb-1">Stato blocco</label>
                    <div>
                        <?php if ($model->blocco): ?>
                            <span class="badge bg-danger">Account bloccato</span>
                        <?php else: ?>
                            <span class="badge bg-success">Account attivo</span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <hr>

        <div class="d-flex align-items-center mb-3">
            <?php
            $imgPath = Yii::getAlias('@web') . '/img/lucchetto_bloccato.png';
            $imgOptions = ['alt' => $model->blocco ? 'Sblocca account' : 'Blocca account', 'class' => 'img-icon me-2'];

            $currentUser = Yii::$app->user->identity ?? null;
            $isAdmin = $currentUser && isset($currentUser->ruolo) && $currentUser->ruolo === 'amministratore';

            if ($isAdmin) {
                echo Html::a(
                    Html::img($imgPath, $imgOptions),
                    Url::to(['admin/reset', 'id' => $model->id]),
                    [
                        'class' => 'btn btn-outline-secondary btn-sm d-inline-flex align-items-center',
                        'data' => [
                            'method' => 'post',
                            'confirm' => $model->blocco
                                ? 'Sei sicuro di voler sbloccare questo account?'
                                : 'Sei sicuro di voler bloccare questo account?',
                        ],
                        'title' => $model->blocco ? 'Sblocca account' : 'Blocca account',
                    ]
                );
                echo Html::tag('span', $model->blocco ? 'Sblocca account' : 'Blocca account', ['class' => 'ms-2']);
            } else {
                echo Html::img($imgPath, array_merge($imgOptions, ['style' => 'opacity:0.6;']));
                echo Html::tag('span', 'Azione riservata agli amministratori', ['class' => 'ms-2 text-muted small']);
            }
            ?>
        </div>

        <div class="form-group d-flex gap-2">
            <?= Html::submitButton('Salva', ['class' => 'btn btn-primary']) ?>
            <?= Html::a('Annulla', ['index'], ['class' => 'btn btn-outline-secondary']) ?>
        </div>

        <?php ActiveForm::end(); ?>
    </div>
</div>

<style>
    .user-form .img-icon {
        width: 28px;
        height: auto;
    }

    .card {
        border-radius: 8px;
    }
</style>
