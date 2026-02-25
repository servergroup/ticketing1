<?php
use yii\helpers\Html;
use app\models\Ticket;

/** @var app\models\Assegnazioni[] $assegnazione */
?>

<style>
/* Stile aziendale per la tabella */
.ticket {
    font-family: "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
    color: #2c3e50;
    margin: 20px 0;
}

.ticket-table {
    width: 100%;
    border-collapse: collapse;
    background: #ffffff;
    box-shadow: 0 1px 3px rgba(0,0,0,0.06);
    overflow: hidden;
    border-radius: 6px;
}

.ticket-table thead {
    background: linear-gradient(180deg,#f7f9fb,#eef3f7);
    color: #34495e;
    text-align: left;
    font-weight: 600;
    font-size: 14px;
    border-bottom: 1px solid #e6eef6;
}

.ticket-table th,
.ticket-table td {
    padding: 12px 14px;
    border-bottom: 1px solid #f1f5f8;
    vertical-align: middle;
    font-size: 14px;
}

.ticket-table tbody tr:hover {
    background: #fbfdff;
}

.ticket-table .muted {
    color: #7f8c8d;
    font-size: 13px;
}

.badge {
    display: inline-block;
    padding: 4px 8px;
    border-radius: 12px;
    font-size: 12px;
    color: #fff;
    font-weight: 600;
}

.badge.in-lavorazione { background: #0d6efd; }
.badge.chiuso { background: #198754; }
.badge.scaduto { background: #dc3545; }
.badge.default { background: #6c757d; }

/* Responsive */
@media (max-width: 768px) {
    .ticket-table th, .ticket-table td { padding: 10px 8px; font-size: 13px; }
}
</style>

<h1 class="text-center">Stato dei ticket assegnati a <?= Yii::$app->user->identity->nome ?> <?= Yii::$app->user->identity->cognome  ?></h1>
<p class="text-center">Qui vedrai lo stato dei  ticket assegnati a <?= Yii::$app->user->identity->nome ?> <?= Yii::$app->user->identity->cognome  ?> </p>
<div class="ticket">
    <table class="ticket-table">
        <thead>
            <tr>
                <th style="width:6%;">Id</th>
                <th style="width:22%;">Codice ticket</th>
                <th style="width:18%;">Stato</th>
                <th style="width:18%;">Ambito</th>
                <th style="width:18%;">Scadenza</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach($assegnazione as $assegnazioni_item):
            $ticket = Ticket::findOne(['codice_ticket' => $assegnazioni_item->codice_ticket]);
            // badge per stato (mantieni la logica di visualizzazione, solo presentazione)
            $stato = $ticket ? $ticket->stato : null;
            $badgeClass = 'badge default';
            if ($stato === 'In lavorazione') $badgeClass = 'badge in-lavorazione';
            if ($stato === 'Chiuso') $badgeClass = 'badge chiuso';
            if ($stato === 'Scaduto') $badgeClass = 'badge scaduto';
        ?>
            <tr>
                <td><?= Html::encode($assegnazioni_item->id) ?></td>
                <td><?= Html::encode($assegnazioni_item->codice_ticket) ?></td>
                <td>
                    <?php if ($stato): ?>
                        <span class="<?= $badgeClass ?>"><?= Html::encode($stato) ?></span>
                    <?php else: ?>
                        <span class="muted">N/D</span>
                    <?php endif; ?>
                </td>
                <td><?= Html::encode($assegnazioni_item->ambito) ?></td>
                <td>
                    <?php if ($ticket && $ticket->scadenza): ?>
                        <?= Html::encode($ticket->scadenza) ?>
                        <?php if (strtotime($ticket->scadenza) < time()): ?>
                            <span class="muted" style="margin-left:8px;color:#c0392b;font-weight:600;">(scaduto)</span>
                        <?php endif; ?>
                    <?php else: ?>
                        <span class="muted">N/D</span>
                    <?php endif; ?>
                </td>

                <td>
                    <?php if($ticket->stato == 'in lavorazione'){?>

                        <td>
                            <?= Html::a('<img src="'.Yii::getAlias('@web/img/icone/support-ticket.png').'">Comunica risoluzione',['ticket/resolve','id'=>$ticket->id],['class'=>'btn btn-primary']) ?>
                        </td>
                <?php
                }else if($ticket->stato == 'chiuso' || $ticket->stato=='risolto'){ ?>

                <td>
                    <?= Html::a('Avanza una ripaertura',['site/avanza-riapertura','codice_ticket'=>$ticket->codice_ticket,'id_operatore'=>$assegnazioni_item->id_operatore],['class'=>'btn btn-primary']); ?>
                </td>


                <?php } ?>
                
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
