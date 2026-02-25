<?php
use yii\widgets\DetailView;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\models\User;

$user=User::findOne(Yii::$app->user->identity->id);
/* @var $this yii\web\View */
/* @var $account app\models\User */

$imageUrl = $account->immagine
    ? Yii::getAlias('@web/uploads/' . $account->immagine)
    : Yii::getAlias('@web/img/avatar-placeholder.png');
?>

<h1 style="text-align:center;margin-top:10px; margin-bottom:50px;">Il mio account</h1>

<?php $form = ActiveForm::begin([
    'action' => ['site/modify-image'],
    'options' => ['enctype' => 'multipart/form-data', 'id' => 'form-image'],
]); ?>

<?= DetailView::widget([
    'model' => $account,
    'options' => ['class' => 'table table-bordered'],
    'attributes' => [
        [
         'label'=>'',   
            'format' => 'raw',
            'value' => function($model) use ($imageUrl) {
                return Html::tag('div',
                    Html::img($imageUrl, ['class' => 'profile-pic', 'alt' => Html::encode($model->nome . ' ' . $model->cognome)]),
                    ['class' => 'profile-pic-wrapper']
                );
            },
            'contentOptions' => ['style' => 'text-align:center;'],
        ],
        [
            'attribute' => 'nome',
            'label' => 'Nome',
            'format' => 'text',
            'value' => function($m){ return Html::encode($m->nome); },
            'contentOptions' => ['style' => 'text-align:center;'],
        ],
        [
            'attribute' => 'cognome',
            'label' => 'Cognome',
            'format' => 'text',
            'value' => function($m){ return Html::encode($m->cognome); },
            'contentOptions' => ['style' => 'text-align:center;'],
        ],
        [
            'attribute' => 'email',
            'label' => 'Email',
            'format' => 'email',
            'value' => function($m){ return Html::encode($m->email); },
            'contentOptions' => ['style' => 'text-align:center;'],
        ],
        [
            'attribute' => 'ruolo',
            'label' => 'Ruolo',
            'format' => 'text',
            'value' => function($m){ return Html::encode($m->ruolo); },
            'contentOptions' => ['style' => 'text-align:center;'],
        ],
        [
            'attribute' => 'partita_iva',
            'label' => 'Partita IVA',
            'format' => 'text',
            'value' => function($m){ return Html::encode($m->partita_iva); },
            'contentOptions' => ['style' => 'text-align:center;'],
            'visible' => (Yii::$app->user->identity->ruolo === 'cliente'),
        ],
    ],
]) ?>

    <!-- input file nascosto collegato al form ActiveForm -->
    <?= $form->field($account, 'immagine')->fileInput([
        'accept' => 'image/*',
        'id' => 'upload-img',
        'style' => 'display:none'
    ])->label(false) ?>

    <div class="mt-3 text-center">
        <!-- Bottone che apre il selettore file -->
        <?= Html::button('Modifica immagine', [
            'class' => 'btn btn-primary me-2',
            'id' => 'btn-change-image',
            'type' => 'button'
        ]) ?>

        
        <?= Html::a('Modifica email', ['site/modify-email'], ['class' => 'btn btn-outline-primary me-2']) ?>
        <?= Html::a('Modifica password', ['site/mail'], ['class' => 'btn btn-outline-primary']) ?>
        <?php if(!$user->is_totp_enabled):?>
        <?= Html::a('Attiva 2FA', ['site/enable-2fa'], ['class' => 'btn btn-warning']) ?>
        <?php else: ?>
            <?= Html::a('Disattiva 2FA', ['site/disable-2fa'], ['class' => 'btn btn-warning']) ?>
<?php endif; ?>
        <?php if (Yii::$app->user->identity->ruolo === 'cliente'): ?>
            <?= Html::a('Modifica Partita IVA', ['site/modify-iva'], ['class' => 'btn btn-outline-secondary ms-2']) ?>
        <?php endif; ?>
    </div>

<?php ActiveForm::end(); ?>
        

<!-- JS: apre file dialog e invia il form automaticamente dopo la selezione -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    const btn = document.getElementById('btn-change-image');
    const fileInput = document.getElementById('upload-img');
    const form = document.getElementById('form-image');

    if (!btn || !fileInput || !form) return;

    btn.addEventListener('click', function (e) {
        e.preventDefault();
        fileInput.click();
    });

    fileInput.addEventListener('change', function () {
        if (fileInput.files && fileInput.files.length > 0) {
            form.submit();
        }
    });
});
</script>