<?php
use hail812\adminlte3\assets\AdminLteAsset;
use yii\helpers\Html;
use app\models\User;
use app\models\Turni;
use yii\helpers\Url;
use app\models\userService;
use yii\web\View;
use hail812\adminlte3\assets\FontAwesomeAsset;


    
FontAwesomeAsset::register($this);
   
$asset = AdminLteAsset::register($this);
$assetDir = $asset->baseUrl;

$this->beginPage();
?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>


    <?php




header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
?>









    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <?php
    // FLASH SUCCESS
    if (Yii::$app->session->hasFlash('success')) {
        $msg = Yii::$app->session->getFlash('success');
        $this->registerJs("
            Swal.fire({
                icon: 'success',
                title: " . json_encode($msg) . ",
                timer:2900,
                confirmButtonText: 'OK'
            });
        ", View::POS_END);
    }

    // FLASH ERROR
    if (Yii::$app->session->hasFlash('error')) {
        $msg = Yii::$app->session->getFlash('error');
        $this->registerJs("
            Swal.fire({
                icon: 'error',
                timer:6000,
                title: " . json_encode($msg) . ",
                confirmButtonText: 'OK'
            });
        ", View::POS_END);
    }

        // FLASH INFO
    if (Yii::$app->session->hasFlash('info')) {
        $msg = Yii::$app->session->getFlash('info');
        $this->registerJs("
            Swal.fire({
                icon: 'info',
               
               title: " . json_encode($msg) . ",
                confirmButtonText: 'OK'
            });
        ", View::POS_END);
    }
    ?>
</head>

<body class="hold-transition sidebar-mini layout-fixed">
<?php $this->beginBody() ?>

<div class="wrapper">

 

    <!-- SIDEBAR -->
    <?php if (!Yii::$app->user->isGuest ): ?>
        <?= $this->render('@app/views/layouts/sidebar.php', [
            'assetDir' => $assetDir
        ]) ?>

              <?= $this->render('@app/views/layouts/navbar.php', [
            'assetDir' => $assetDir
        ]) ?>
    <?php endif; ?>

    <!-- CONTENT -->
    <div class="content-wrapper">
        <section class="content">
            <div class="container-fluid">
                <?= $content ?>
            </div>
        </section>
    </div>

    <!-- FOOTER -->
    <footer class="main-footer">
        <strong>&copy; <?= date('Y') ?> Dataseed.</strong> All rights reserved.
    </footer>

</div>

<?php $this->endBody() ?>
<?php
$this->registerJsFile(
    'https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js',
    ['depends' => [\yii\web\JqueryAsset::class]]
);
?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
<?php $this->endPage() ?>


<style>
/* Nascondi navbar su desktop */
.mobile-navbar {
    display: none;
}

/* Mostra navbar su mobile */
@media (max-width: 768px) {
    .mobile-navbar {
        display: flex !important;
        justify-content: space-between;
        padding-left: 10px;
    }

    .mobile-nav {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .page-title {
        font-weight: 600;
        font-size: 16px;
    }

    /* Riduci larghezza sidebar su mobile */
    .main-sidebar {
        width: 220px !important;
    }

    /* Content più largo su mobile */
    .content-wrapper {
        margin-left: 0 !important;
    }

    /* Stile aziendale per SweetAlert2 */
.swal2-corporate {
    font-family: "Segoe UI", Roboto, Arial, sans-serif !important;
    padding: 20px !important;
    border-radius: 10px !important;
    max-width: 90% !important;
}

/* Responsive per schermi piccoli */
@media (max-width: 480px) {
    .swal2-corporate {
        font-size: 14px !important;
        padding: 15px !important;
    }
}

}
</style>
