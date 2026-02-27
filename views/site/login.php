<?php
use yii\helpers\Html;
use app\assets\LogAsset;
use yii\web\View;

$this->title = '';
LogAsset::register($this);
?>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


<div class="login-box">
   

            <p class="login-box-msg">
               <img src="<?= Yii::getAlias('@web/img/taglio_dataseed.png') ?>">
            </p>

            <h1 class="text-center mb-4">Accedi</h1>

            <?php $form = \yii\bootstrap4\ActiveForm::begin(['id' => 'login-form']) ?>

            <!-- USERNAME -->
            <?= $form->field($model, 'username', [
                'template' => '{beginWrapper}{input}{icon}{error}{endWrapper}',
                'wrapperOptions' => ['class' => 'input-group mb-3'],
                'parts' => [
                    '{icon}' => '
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-user"></span>
                            </div>
                        </div>'
                ]
            ])->label(false)->textInput(['placeholder' => 'Username']) ?>

            <!-- PASSWORD -->
            <?= $form->field($model, 'password', [
                'template' => '{beginWrapper}{input}{icon}{error}{endWrapper}',
                'wrapperOptions' => ['class' => 'input-group mb-3'],
                'parts' => [
                    '{icon}' => '
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-lock"></span>
                            </div>
                        </div>'
                ]
            ])->label(false)->passwordInput(['placeholder' => 'Password']) ?>

            <div class="text-center mt-3">
                <?= Html::submitButton('Accedi', [
                    'class' => 'btn btn-primary btn-block'
                ]) ?>
            </div>

            <div class="text-center mt-3">
                <?= Html::a('Hai dimenticato la password?', ['site/mail']) ?>
            </div>

            <div class="text-center mt-2">
                <?= Html::a('Non sei registrato?', ['site/register']) ?>
            </div>

            <?php \yii\bootstrap4\ActiveForm::end(); ?>

</div>
