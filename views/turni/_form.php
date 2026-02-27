<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
?>

<div class="turni-form">

    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Gestione Turno</h5>
        </div>

        <div class="card-body">

            <?php $form = ActiveForm::begin([
                'options' => ['class' => 'needs-validation'],
            ]); ?>

        

                <div class="col-md-4">
                    <?= $form->field($model, 'entrata')
                        ->input('time') ?>
                </div>

                <div class="col-md-4">
                    <?= $form->field($model, 'uscita')
                        ->input('time') ?>
                </div>

            </div>

           

                

                <div class="col-md-6">
                    <?= $form->field($model, 'stato')
                        ->dropDownList([
                            'attivo' => 'Attivo',
                            'in_attesa' => 'In Attesa',
                            'completato' => 'Completato'
                        ], ['prompt' => 'Seleziona stato']) ?>
                </div>

            </div>

            <div class="form-group text-end mt-3">
                <?= Html::submitButton('💾 Salva', [
                    'class' => 'btn btn-success px-4'
                ]) ?>
            </div>

            <?php ActiveForm::end(); ?>

        </div>
    </div>

</div>