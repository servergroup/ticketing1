<?php

use hail812\adminlte3\assets\AdminLteAsset;
use hail812\adminlte3\assets\FontAwesomeAsset;
use yii\helpers\Html;
use yii\web\View;

FontAwesomeAsset::register($this);
$asset = AdminLteAsset::register($this);
$assetDir = $asset->baseUrl;

$this->beginPage();
?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
    <?php
    $this->registerCssFile('@web/css/corporate-ui.css', ['depends' => [\hail812\adminlte3\assets\AdminLteAsset::class]]);
    $this->registerJsFile('@web/js/table-ux.js', ['depends' => [\yii\web\JqueryAsset::class], 'position' => View::POS_END]);
    $this->registerJsFile('https://cdn.jsdelivr.net/npm/sweetalert2@11', ['position' => View::POS_HEAD]);
    $this->registerJsFile('https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js', [
        'depends' => [\yii\web\JqueryAsset::class],
        'position' => View::POS_END,
    ]);

    foreach (['success', 'error', 'info'] as $type) {
        if (Yii::$app->session->hasFlash($type)) {
            $message = Yii::$app->session->getFlash($type);
            $icon = $type === 'error' ? 'error' : ($type === 'info' ? 'info' : 'success');
            $timer = $type === 'error' ? 6000 : 3200;
            $this->registerJs(
                "Swal.fire({icon: '{$icon}', title: " . json_encode($message) . ", timer: {$timer}, confirmButtonText: 'OK'});",
                View::POS_END
            );
        }
    }
    ?>
</head>

<body class="hold-transition sidebar-mini layout-fixed layout-navbar-fixed corporate-app">
<?php $this->beginBody() ?>

<div class="wrapper">
    <?php if (!Yii::$app->user->isGuest): ?>
        <?= $this->render('@app/views/layouts/sidebar.php', ['assetDir' => $assetDir]) ?>
        <?= $this->render('@app/views/layouts/navbar.php', ['assetDir' => $assetDir]) ?>
    <?php endif; ?>

    <div class="content-wrapper">
        <section class="content pt-3 pb-4">
            <div class="container-fluid">
                <?= $content ?>
            </div>
        </section>
    </div>

    <footer class="main-footer">
        <strong>&copy; <?= date('Y') ?> Dataseed.</strong> All rights reserved.
    </footer>
</div>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>

