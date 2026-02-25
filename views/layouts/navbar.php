<?php

use yii\helpers\Html;
use yii\helpers\Url;

/* 
    IMPORTANTE:
    - $turni deve essere passato dal controller tramite:
      Yii::$app->view->params['turni']
*/

$user = Yii::$app->user->identity;
$turni = Yii::$app->view->params['turni'] ?? null;

$this->title = '';

?>

<nav class="mobile-navbar d-flex align-items-center justify-content-between px-3">

<?php if (!Yii::$app->user->isGuest): ?>

    <nav class="main-header navbar navbar-expand navbar-white navbar-light mobile-navbar">
        <ul class="navbar-nav mobile-nav">
            <li class="nav-item">
                <a class="nav-link" data-widget="pushmenu" href="#">
                    <img src="<?= Yii::getAlias('@web/img/menu.png') ?>" style="width:22px;">
                </a>
            </li>
            <li class="nav-item">
                <span class="nav-link page-title"><?= Html::encode($this->title) ?></span>
            </li>
        </ul>
    </nav>

    <div style="width: 32px;"></div>

    <?php if ($turni): ?>
        <?php if ($turni->stato == 'In pausa' && $user->ruolo != 'cliente'): ?>
            <?= Html::a(
                'Torna in servizio',
                ['site/salta-pausa', 'id' => $user->id],
                ['class' => 'btn btn-primary']
            ) ?>
        <?php endif; ?>
    <?php endif; ?>

 

<?php endif; ?>

</nav>
