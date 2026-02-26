<?php
use yii\helpers\Html;
use app\models\User;
/** @var yii\web\View $this */
/** @var app\models\Ticket[] $ticket */
?>
<h1 class="text-center">Stato dei ticket di reparto</h1>
<p class="text-center">Qui vedrai lo stato dei  ticket del tuo reparto </p>

<?php if (empty($ticket)): ?>
    <p class="text-center mt-4">Nel tuo reparto non ci sono ancora dei ticket aperti</p>
<?php else: ?>

<table class="table table-bordered table-striped mt-4">
    <thead class="table-dark">
        <tr>
            <th>Codice Ticket</th>
           <th>Azienda</th>
           <th>problema</th>
            <th>Stato</th>
            <th>Azioni</th>
        </tr>
    </thead>

    <tbody>
       <?php foreach ($ticket as $ticket_item): 
       $personale=User::findOne(['id'=>$ticket_item->id_cliente]);
       // usa l'id numerico per essere sicuro che sia valido come HTML id 
        $modalId = 'modalTicket-' . (int)$ticket_item->id; ?> 
        <tr> 
            <td> 
                <!-- Button trigger modal -->
                  <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#<?= $modalId ?>"> 
                    <?= Html::encode($ticket_item->codice_ticket) ?> 
                </button>
                 <!-- Modal --> 
                  <div class="modal fade" id="<?= $modalId ?>" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="<?= $modalId ?>Label" aria-hidden="true">
                     <div class="modal-dialog">
                         <div class="modal-content"> 
                            <div class="modal-header">
                                 <h5 class="modal-title" id="<?= $modalId ?>Label">Info ticket 
                                    <?= Html::encode($ticket_item->codice_ticket) ?></h5> 
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button> 
                                </div> 
                                <div class="modal-body"> 
                                    <p>ID: <?= Html::encode($ticket_item->id) ?></p> 
                                    <p>Codice ticket: <?= Html::encode($ticket_item->codice_ticket) ?></p> 
                                    <?php if ($ticket_item->scadenza === null): ?> <p>Scadenza: Non definita</p>
                                         <?php else: ?> <p>Scadenza: <?= Html::encode($ticket_item->scadenza) ?></p> 
                                            <?php endif; ?> </div> <div class="modal-footer">
                                                 <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Chiudi</button> 
                                                </div> 
                                            </div> 
                                        </div> 
                                    </div> 
                             
                
                    <td><?= Html::encode($personale->azienda); ?></td>
                    <td><?= Html::encode($ticket_item->problema); ?></td>
                    <td><?= Html::encode($ticket_item->stato); ?></td>
                    <td>  
                    <?= Html::a('<img src='.Yii::getAlias('@web/img/delete.png').'>',['tickets/delete-ticket','id'=>$ticket_item->id]) ?>
                    <?= Html::a(
    Html::img(Yii::getAlias('@web/img/pen.png')),
    ['tickets/modify-ticket', 'codiceTicket' => $ticket_item->codice_ticket]
) ?>


                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php endif; ?>
