<?php
use yii\helpers\Html;
use yii\helpers\Url;
use app\models\Ticket;

/** @var app\models\User $user */
/** @var int $countTicket */
/** @var string|null $stato */
/** @var app\models\Ticket|null $ultimoTicket */

$ruolo = $user->ruolo;
$nome  = Yii::$app->user->identity->nome;
$stato = $stato ?? '—';
?>

<div class="dashboard-container">

    <!-- HERO -->
    <div class="hero-box" onclick="window.location.href='<?= Url::to(['site/account']) ?>'">
        <div class="hero-left">
            <h1>Salve, <?= Html::encode($nome) ?></h1>

            <?php if ($ruolo === 'cliente'): ?>
                <p>Area riservata clienti. Gestisci le tue richieste di assistenza.</p>
            <?php elseif (in_array($ruolo, ['developer', 'ict'])): ?>
                <p>Area operativa. Gestisci i ticket assegnati.</p>
            <?php elseif ($ruolo === 'amministratore'): ?>
                <p>Area amministrativa. Supervisione e gestione ticket.</p>
            <?php endif; ?>
        </div>

        <div class="hero-right">
            <i class="fas fa-chart-line"></i>
        </div>
    </div>

    <!-- STATISTICHE -->
    <div class="stats-grid">

        <?php if ($ruolo === 'cliente'): ?>

            <div class="stat-card clickable" onclick="window.location.href='<?= Url::to(['site/contact']) ?>'">
                <div class="stat-icon blue"><i class="fas fa-phone"></i></div>
                <div class="stat-info">
                    <h3>Contattaci</h3>
                    <p>Parla con il nostro team di supporto.</p>
                </div>
            </div>

            <?php if ($countTicket > 0 && isset($ultimoTicket)): ?>
                <div class="stat-card clickable" data-bs-toggle="modal" data-bs-target="#ticketModal">
                    <div class="stat-icon green"><i class="fas fa-flag"></i></div>
                    <div class="stat-info">
                        <h3><?= Html::encode($stato) ?></h3>
                        <p>Stato ultimo ticket</p>
                    </div>
                </div>
            <?php endif; ?>

        <?php elseif (in_array($ruolo, ['developer', 'ict'])): ?>

            <div class="stat-card clickable" onclick="window.location.href='<?= Url::to(['assegnazioni/my-ticket']) ?>'">
                <div class="stat-icon green"><i class="fas fa-ticket-alt"></i></div>
                <div class="stat-info">
                    <h3>Ticket</h3>
                    <p>Gestione ticket assegnati</p>
                </div>
            </div>

            <div class="stat-card clickable" onclick="window.location.href='<?= Url::to(['ticket/my-ticket']) ?>'">
                <div class="stat-icon blue"><i class="fas fa-envelope"></i></div>
                <div class="stat-info">
                    <h3>Messaggistica</h3>
                    <p>Comunicazioni sui ticket</p>
                </div>
            </div>

        <?php elseif ($ruolo === 'amministratore'): ?>
            <?php $countTicket = Ticket::find()->count(); ?>

            <div class="stat-card clickable" onclick="window.location.href='<?= Url::to(['admin/index']) ?>'">
                <div class="stat-icon blue"><i class="fas fa-ticket-alt"></i></div>
                <div class="stat-info">
                    <h3><?= (int)$countTicket ?></h3>
                    <p>Ticket totali in sistema</p>
                </div>
            </div>

        <?php endif; ?>

    </div>

    <!-- AZIONI -->
    <?php if (in_array($ruolo, ['cliente', 'amministratore'])): ?>
        <div class="actions-grid">

            <div class="action-card clickable" onclick="window.location.href='<?= Url::to(['ticket/new-ticket']) ?>'">
                <h2>Nuova richiesta di assistenza</h2>
                <p>Apri un ticket e ricevi supporto dal nostro team.</p>
                <?= Html::a('Apri ticket', ['ticket/new-ticket'], ['class' => 'btn-primary']) ?>
            </div>

            <?php if ($ruolo === 'cliente'): ?>
                <div class="action-card clickable" onclick="window.location.href='<?= Url::to(['ticket/my-ticket']) ?>'">
                    <h2>I tuoi ticket</h2>
                    <p>Consulta lo stato delle richieste inviate.</p>
                </div>
            <?php endif; ?>

            <?php if ($ruolo === 'amministratore'): ?>

                <div class="action-card clickable" onclick="window.location.href='<?= Url::to(['tickets/open']) ?>'">
                    <h2>Ticket aperti</h2>
                    <p>Visualizza e gestisci i ticket in attesa.</p>
                </div>

                <div class="action-card clickable" onclick="window.location.href='<?= Url::to(['tickets/lavorazione']) ?>'">
                    <h2>Ticket in lavorazione</h2>
                    <p>Visualizza e gestisci i ticket in lavorazione.</p>
                </div>

                <div class="action-card clickable" onclick="window.location.href='<?= Url::to(['tickets/close']) ?>'">
                    <h2>Ticket chiusi</h2>
                    <p>Visualizza e gestisci i ticket chiusi.</p>
                </div>

                           <div class="action-card clickable" onclick="window.location.href='<?= Url::to(['tickets/scadence']) ?>'">
                    <h2>Ticket scaduti</h2>
                    <p>Visualizza e gestisci i ticket scaduti.</p>
                </div>

            <?php endif; ?>

        </div>
    <?php endif; ?>

</div>

<style>
    /* PALETTE AZIENDALE */
:root {
    --primary: #0b3c5d;
    --primary-dark: #062f4f;
    --accent: #2e8b57;
    --text-dark: #2c3e50;
    --text-light: #6c757d;
    --bg-light: #f5f7fa;
    --card-bg: #ffffff;
    --shadow: 0 4px 14px rgba(0,0,0,0.08);
    --shadow-hover: 0 6px 20px rgba(0,0,0,0.15);
    --radius: 12px;
    --transition: .25s ease;
}

/* CONTAINER */
.dashboard-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 10px;
}

/* HERO */
.hero-box {
    background: linear-gradient(135deg, var(--primary), var(--primary-dark));
    color: #fff;
    padding: 40px;
    border-radius: var(--radius);
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 40px;
    box-shadow: var(--shadow);
    cursor: pointer;
    transition: var(--transition);
}

.hero-box:hover {
    box-shadow: var(--shadow-hover);
    transform: translateY(-3px);
}

.hero-left h1 {
    font-size: 32px;
    font-weight: 700;
    margin: 0;
}

.hero-left p {
    margin-top: 8px;
    opacity: .9;
    font-size: 16px;
}

.hero-right i {
    font-size: 70px;
    opacity: .25;
}

/* GRID */
.stats-grid,
.actions-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 25px;
    margin-bottom: 35px;
}

/* CARD BASE */
.stat-card,
.action-card {
    background: var(--card-bg);
    padding: 28px;
    border-radius: var(--radius);
    box-shadow: var(--shadow);
    transition: var(--transition);
    border: 1px solid #e6e9ec;
}

.stat-card:hover,
.action-card:hover {
    transform: translateY(-4px);
    box-shadow: var(--shadow-hover);
}

/* ICONA */
.stat-icon {
    width: 60px;
    height: 60px;
    border-radius: var(--radius);
    display: flex;
    align-items: center;
    justify-content: center;
    color: #fff;
    font-size: 26px;
    margin-bottom: 12px;
}

.stat-icon.blue { background: var(--primary); }
.stat-icon.green { background: var(--accent); }

/* TESTI */
.stat-info h3 {
    font-size: 22px;
    font-weight: 700;
    margin: 0;
    color: var(--text-dark);
}

.stat-info p {
    color: var(--text-light);
    margin: 0;
    font-size: 15px;
}

/* ACTION CARD */
.action-card h2 {
    font-size: 22px;
    font-weight: 700;
    margin-bottom: 8px;
    color: var(--text-dark);
}

.action-card p {
    color: var(--text-light);
    margin-bottom: 15px;
}

/* BUTTON */
.btn-primary {
    display: inline-block;
    padding: 10px 18px;
    border-radius: 8px;
    color: #fff;
    font-weight: 600;
    text-decoration: none;
    background: var(--primary);
    border: none;
    transition: var(--transition);
}

.btn-primary:hover {
    background: var(--primary-dark);
    color: #fff;
}

/* RESPONSIVE */
@media (max-width: 768px) {
    .hero-box {
        flex-direction: column;
        text-align: center;
    }
    .hero-right {
        margin-top: 20px;
    }
}

</style>