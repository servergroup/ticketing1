<?php
use yii\helpers\Html;
?>
<aside class="main-sidebar sidebar-dark-primary elevation-4" style="border-radius:10px !important">

<div class="zona-logo" style="background-color:white;">
    <!-- Brand Logo -->
    <a href="#" class="brand-link">
        
        <span class="brand-text font-weight-light" oncontextmenu="return false;"><img src="<?= Yii::getAlias('@web/img/taglio_dataseed.svg') ?>" width="190px"></span>
    </a> 
</div>
    <div class="sidebar">

        <!-- User panel -->
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            <?php
            if(Yii::$app->user->identity->immagine!=null && Yii::$app->user->identity->immagine !='' )
                {
            ?>
            
            <div class="image">
                <img id='logo_utente' src='<?=Yii::getAlias('@web/img/upload/'.Yii::$app->user->identity->immagine)?>' class="img-circle elevation-2" alt="User Image">
           
            </div>
            <?php
}else{
?>
 <div class="image">
                <img src=<?= Yii::getAlias('@web/img/profile.png') ?>
                     class="img-circle elevation-2" alt="User Image">
            </div>

<?php
}
?>
            <div class="info">
                <a href="/site/account" class="d-block">
                    <?= Yii::$app->user->isGuest ? 'Ospite' : Yii::$app->user->identity->username ?>
                </a>
    
            </div>
                                     <?= Html::a(
        '<img src="'.Yii::getAlias('@web/img/logout.png').'" style="width:30px;margin-left:100px;">',
        ['site/logout'],
        ['class' => 'logout']
    ) ?>
        </div>

        <!-- Sidebar Menu -->
        <nav class="mt-2">

            <?php
            if (Yii::$app->user->isGuest) {
                return;
            }

            $ruolo = Yii::$app->user->identity->ruolo;
            $menuItems = [];

            /* ============================
               AMMINISTRATORE
            ============================ */
            if ($ruolo === 'amministratore') {
                $menuItems = [

                    ['label' => 'Home', 'icon' => 'fas fa-home', 'url' => ['site/index']],

                    [
                        'label' => 'Ticket',
                        'icon' => 'fas fa-ticket-alt',
                        'items' => [
                            ['label' => 'Tutti i ticket', 'icon' => 'fas fa-list', 'url' => ['tickets/index']],
                            ['label' => 'Ticket aperti', 'icon' => 'fas fa-exclamation-circle', 'url' => ['tickets/open']],
                            ['label' => 'Ticket chiusi', 'icon' => 'fas fa-check', 'url' => ['tickets/close']],
                             ['label' => 'Ticket scaduti', 'icon' => 'fas fa-check', 'url' => ['tickets/scadence']],
                            ['label' => 'Nuovo ticket', 'icon' => 'fas fa-plus', 'url' => ['tickets/new-ticket']],
                             ['label' => 'Tempi del ticket', 'icon' => 'fas fa-ticket-alt', 'url' => ['tempi/index']],
                           
                        ]
                    ],

                    [
                        'label' => 'Gestione utenti',
                        'icon' => 'fas fa-user-alt',
                        'items' => [
                    ['label' => 'Nuovo operatore/amministratore', 'icon' => 'fas fa-user-plus', 'url' => ['site/register']],
                    ['label' => 'Utenti in attesa', 'icon' => 'fas fa-user-clock', 'url' => ['admin/attese']],
                    ['label' => 'Utenti bloccati', 'icon' => 'fas fa-user-slash', 'url' => ['admin/block']],
                    

                        ]
                    ],

                    [
                        'label'=>'Dipendenti',
                        'icon'=>'fas fa-user-alt',
                        'items'=>[
                    ['label' => 'Gestione operatori', 'icon' => 'fas fa-plus', 'url' => ['admin/index']],
                     ['label' => 'Verifica i ruoli', 'icon' => 'fas fa-plus', 'url' => ['admin/verify-ruolo']],
                     
                ]
                    ],
                   
                ];             
            }

            /*  ============================
               |            ICT             |
                ============================ */
            else if ($ruolo === 'ict') {
                $menuItems = [

                    ['label' => 'Home', 'icon' => 'fas fa-home', 'url' => ['site/index']],

                    [
                        'label' => 'Ticket',
                        'icon' => 'fas fa-ticket-alt',
                        'items' => [
                            ['label' => 'Ticket assegnati', 'icon' => 'fas fa-file-alt', 'url' => ['assegnazioni/my-ticket']],
                             ['label' => 'Ticket del mio reparto', 'icon' => 'fas fa-file-alt', 'url' => ['ticket/my-reparto']],
                             ['label' => 'Ticket del mio reparto aperti', 'icon' => 'fas fa-file-alt', 'url' => ['ticket/my-reparto-open']],
                        ]
                    ],

                    
                ];
            }

            /* ============================
               CLIENTE
            ============================ */
            else if ($ruolo === 'cliente') {
                $menuItems = [

                    ['label' => 'Home', 'icon' => 'fas fa-home', 'url' => ['site/index']],

                    [
                        'label' => 'Ticket',
                        'icon' => 'fas fa-ticket-alt',
                        'items' => [
                            ['label' => 'Nuovo ticket', 'icon' => 'fas fa-plus', 'url' => ['tickets/new-ticket']],
                            ['label' => 'Evoluzione ticket', 'icon' => 'fas fa-history', 'url' => ['tickets/my-ticket']],
                        ]
                    ],
                    ['label' => 'Contattaci', 'icon' => 'fas fa-contact', 'url' => ['site/contact']],
                ];
            }

            /* =============================
               DEVELOPER
              ============================= */
            else if ($ruolo === 'developer') {
                $menuItems = [

                    ['label' => 'Home', 'icon' => 'fas fa-home', 'url' => ['site/index']],

                    [
                        'label' => 'Ticket',
                        'icon' => 'fas fa-ticket-alt',
                        'items' => [
                            ['label' => 'Ticket assegnati', 'icon' => 'fas fa-file-alt', 'url' => ['assegnazioni/my-ticket']],
                            ['label' => 'Ticket del mio reparto', 'icon' => 'fas fa-file-alt', 'url' => ['ticket/my-reparto']],
                             ['label' => 'Ticket del mio reparto aperti', 'icon' => 'fas fa-file-alt', 'url' => ['ticket/my-reparto-open']],
                        ]
                    ],

                   
                   
                ];
            }

            echo \hail812\adminlte\widgets\Menu::widget([
                'encodeLabels' => false,
                'items' => $menuItems,
            ]);
            ?>

        </nav>
    </div>
</aside>

<style>
/* Arrotonda e migliora l'immagine utente */
#logo_utente,
.user-panel .image img {
    width: 48px;
    height: 48px;
    object-fit: cover;
    border-radius: 50% !important;
    border: 2px solid rgba(255,255,255,0.3);
    box-shadow: 0 2px 6px rgba(0,0,0,0.25);
}

/* Arrotonda leggermente il logo */
.brand-link img {
    border-radius: 12px;
    transition: transform 0.2s ease;
}

.brand-link img:hover {
    transform: scale(1.03);
}

/* Migliora il pannello utente */
.user-panel {
    background: rgba(255,255,255,0.05);
    border-radius: 10px;
    padding: 10px 12px;
}

/* Migliora i link del menu */
.sidebar .nav-link {
    border-radius: 8px;
    margin: 2px 0;
    transition: background 0.2s ease, padding-left 0.2s ease;
}

.sidebar .nav-link:hover {
    background: rgba(255,255,255,0.15);
    padding-left: 18px;
}

/* Migliora le icone */
.sidebar .nav-icon {
    margin-right: 10px;
}

/* Migliora la sezione brand */
.brand-link {
    padding: 18px 15px;
    display: flex;
    justify-content: center;
    align-items: center;
}
</style>