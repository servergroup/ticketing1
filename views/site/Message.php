<?php

use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;

$this->title ='Messagistica dei ticket';
$this->params['breadcrumbs'][] = $this->title;

?>

<div class="site-contact">

    <h1 class="mb-4"><?= Html::encode($this->title) ?></h1>

    <?php if (Yii::$app->session->hasFlash('contactFormSubmitted')): ?>

        <div class="alert alert-success shadow-sm">
            La tua richiesta è stata inviata correttamente.  
            Ti risponderemo il prima possibile.
    </div>
        <div class="row-success" style="display:hidden;">
            <div class="col-lg-6">

                <?php $form = ActiveForm::begin(['id' => 'contact-form']); ?>

                <?= $form->field($model, 'body')
                        ->textarea([
                            'rows' => 6,
                            'placeholder' => 'Scrivi qui il tuo messaggio...',
                            'class' => 'form-control form-control-lg'
                        ])
                        ->label('Messaggio') ?>

               

                    <!-- INVIO NORMALE -->
                    <div class="form-group mt-4">
                        <?= Html::submitButton('Invia richiesta', [
                            'class' => 'btn btn-primary btn-lg px-4 shadow-sm',
                            'name' => 'contact-button'
                        ]) ?>
                    </div>


                   
                <?php ActiveForm::end(); ?>

            </div>
        </div>
      
    </div>
    <?php else: ?>

        <p class="text-muted mb-4">
            Per informazioni commerciali, assistenza o richieste generiche, compila il modulo seguente.  
            Il nostro team ti contatterà al più presto.


        <div class="row">
            <div class="col-lg-6">

                <?php $form = ActiveForm::begin(['id' => 'contact-form']); ?>

                <?= $form->field($model, 'messagio')
                        ->textarea([
                            'rows' => 6,
                            'placeholder' => 'Scrivi qui il tuo messaggio...',
                            'class' => 'form-control form-control-lg'
                        ])
                        ->label('Messaggio') ?>

               

                    <!-- INVIO NORMALE -->
                    <div class="form-group mt-4">
                        <?= Html::submitButton('Invia messagio', [
                            'class' => 'btn btn-primary btn-lg px-4 shadow-sm',
                            'name' => 'contact-button'
                        ]) ?>
                    </div>


                   
                <?php ActiveForm::end(); ?>

            </div>
        </div>
        </p>

     

            </div>
        </div>

    <?php endif; ?>

</div>

<style>
.site-contact h1 {
    font-weight: 600;
    color: #1a1a1a;
}

.site-contact p {
    font-size: 16px;
}

.form-control-lg {
    border-radius: 8px;
    padding: 14px;
    font-size: 16px;
}

.btn-primary {
    background-color: #0056b3;
    border-color: #004a99;
    border-radius: 8px;
    transition: 0.2s ease-in-out;
}

.btn-primary:hover {
    background-color: #004a99;
    border-color: #003d80;
    transform: translateY(-2px);
}

.alert-success {
    font-size: 16px;
    border-radius: 8px;
}
</style>
