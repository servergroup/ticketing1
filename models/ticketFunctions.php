<?php

namespace app\models;

use Yii;
use yii\base\Model;
use app\models\History;
use app\models\User;
use app\models\Ticket;
use app\models\Assegnazioni;
use app\models\TempiTicket;

use yii\db\Expression;

class ticketFunctions extends Model
{
    /**
     * Genera codice casuale alfanumerico
     */
    public function code_random(): string
    {
        $length = 6;
        $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $code = '';

        for ($i = 0; $i < $length; $i++) {
            $code .= $characters[random_int(0, strlen($characters) - 1)];
        }

        return $code;
    }

    /**
     * Invia una mail semplice (testo/HTML)
     */
    public function contact(string $email, string $messaggio, string $oggetto): bool
    {
        try {
            Yii::$app->mailer->compose()
                ->setTo($email)
                ->setFrom([Yii::$app->params['senderEmail'] => Yii::$app->params['senderName']])
                ->setReplyTo([$email => $email])
                ->setSubject($oggetto)
                ->setHtmlBody($messaggio)
                ->send();

            return true;
        } catch (\Throwable $e) {
            Yii::error('Errore invio mail: ' . $e->getMessage(), __METHOD__);
            return false;
        }
    }

    /**
     * Verifica se esiste un ticket con lo stesso problema per l'utente loggato
     */
    public function verifyTicket(string $problema)
    {
        $userId = Yii::$app->user->identity->id ?? null;
        if ($userId === null) {
            return null;
        }

        return Ticket::findOne(['problema' => $problema, 'id_cliente' => $userId]);
    }

    /**
     * Crea solo il ticket (non crea record in tempi_ticket)
     */
    public function newTicket($problema, $ambito, $scadenza, $priorita): bool
    {
        $ticket = new ticketfunction();
        

        $cliente = User::findOne(['username' => Yii::$app->user->identity->username]);
        if (!$cliente) {
            Yii::error("Utente loggato non trovato.", __METHOD__);
            throw new \Exception("Utente loggato non trovato.");
        }

        $ticket->problema = $problema;
        $ticket->ambito = $ambito;
        $ticket->stato = 'aperto';
        $ticket->codice_ticket = (string)$this->code_random();
        $ticket->scadenza = $scadenza ?? null;
        $ticket->data_invio = date('Y-m-d H:i:s');
        $ticket->id_cliente = $cliente->id;
        $ticket->priorita = $priorita;

      

       return $ticket->save();
    }

    /**
     * Chiude un ticket (imposta stato risolto)
     */
    public function chiudiTicket(int $id_ticket): bool
    {
        $ticket = Ticket::findOne($id_ticket);
        $tempi=TempiTicket::findOne(['id_ticket'=>$id_ticket]);
        if (!$ticket) {
            return false;
        }
        $ticket->stato = 'risolto';
        $tempi->chiuso_il=date('Y-m-d H:i:s');
        $tempi->ora_fine=date('H:i:s');
        $tempi->save();
        return $ticket->save();
    }

    /**
     * Controlla ticket scaduti e notifica il personale
     */
    public function ticketScaduto(): void
    {
        $tickets = Ticket::find()->where(['stato' => 'aperto'])->all();
        $now = new \DateTime();
      

        foreach ($tickets as $ticket) {
            if (empty($ticket->scadenza)) {
                continue;
            }

            // Assicuriamoci che scadenza sia DateTime
            try {
                $scadenza = new \DateTime($ticket->scadenza);
            } catch (\Exception $e) {
                Yii::error('Formato scadenza non valido per ticket ' . $ticket->id, __METHOD__);
                continue;
            }

            if ($now > $scadenza) {
                $ticket->stato = 'scaduto';
                $ticket->save(false);

               

                $history = new History();
                $history->id_ticket = $ticket->id;
                $history->id_operatore = 1;
                $history->id_cliente = $ticket->id_cliente;
                $history->stato = $ticket->stato;
                $history->save(false);
            }
        }
    }

    /**
     * Elimina un ticket e registra la cancellazione in history, notifica personale
     */
    public function deleteTicket(int $id): bool
    {
        $ticket = Ticket::findOne($id);
        $tempi=TempiTicket::findOne(['id_ticket'=>$id]);
        
       
        $cliente = User::findOne(['username' => Yii::$app->user->identity->username]);
       
        $history = new History();
        $history->id_ticket = $ticket->id;
        $history->id_operatore = $cliente ? $cliente->id : null;
        $history->id_cliente = $ticket->id_cliente;
        $history->stato = $ticket->stato;
        $history->save(false);

        if($tempi){
        $tempi->delete();
        }

        return $ticket->delete();
    }

    /**
     * Restituisce un id operatore casuale in base all'ambito
     */
    public function random_num(string $ambito): ?int
    {
        $ruolo = null;
        $ambitoLower = mb_strtolower($ambito);

        if ($ambitoLower === 'sviluppo' || $ambitoLower === 'sviluppatore') {
            $ruolo = 'developer';
        } elseif ($ambitoLower === 'ict'|| $ambitoLower === 'ict') {
            $ruolo = 'ict';
        }

        if ($ruolo === null) {
            return null;
        }

        $ids = User::find()->select('id')->where(['ruolo' => $ruolo])->column();
        if (empty($ids)) {
            return null;
        }

        $randomIndex = array_rand($ids);
        return (int)$ids[$randomIndex];
    }

    /**
     * Verifica se esiste già un'assegnazione per il codice ticket
     */
    public function verifyDelegate(string $codice_ticket)
    {
        return Assegnazioni::findOne(['codice_ticket' => $codice_ticket]);
    }

    /**
     * Assegna un ticket: crea assegnazione, imposta stato e avvia tracking tempi
     */
    public function assegnaTicket(string $codice_ticket, string $ambito): bool
    {
        $ticket = Ticket::findOne(['codice_ticket' => $codice_ticket]);
        if (!$ticket) {
            Yii::$app->session->setFlash('error', 'Ticket non trovato');
            return false;
        }

        $operatoreId = $this->random_num($ambito);
        Yii::info('OperatoreId da random_num: ' . var_export($operatoreId, true), __METHOD__);

        if ($operatoreId === null) {
            Yii::$app->session->setFlash('error', 'Nessun operatore disponibile per questo ambito');
            return false;
        }

        $assegnazioni = new Assegnazioni();
        $assegnazioni->id_operatore = (int)$operatoreId;
        $assegnazioni->codice_ticket = $codice_ticket;
        $assegnazioni->ambito = $ambito;

        $ticket->stato = 'in lavorazione';

        $transaction = Yii::$app->db->beginTransaction();
        try {
            if (!$ticket->save()) {
                throw new \Exception('Errore salvataggio ticket: ' . json_encode($ticket->getErrors()));
            }
            if (!$assegnazioni->save()) {
                throw new \Exception('Errore salvataggio assegnazione: ' . json_encode($assegnazioni->getErrors()));
            }

            // crea o aggiorna record TempiTicket e avvia tracking
            $tempi = TempiTicket::findOne(['id_ticket' => $ticket->id]);
            if ($tempi === null) {
                $tempi = TempiTicket::creaRecordTempi($ticket->id, (int)$operatoreId);
                if ($tempi === null) {
                    throw new \Exception('Errore creazione record tempi_ticket');
                }
            } else {
                $tempi->id_operatore = (int)$operatoreId;
                // usa datetime completo per ora_inizio se preferisci
                if (empty($tempi->ora_inizio)) {
                    $tempi->ora_inizio = date('Y-m-d H:i:s');
                }
                if (!$tempi->save(false)) {
                    throw new \Exception('Errore aggiornamento tempi_ticket: ' . json_encode($tempi->getErrors()));
                }
            }

            $transaction->commit();
            return true;
        } catch (\Exception $e) {
            $transaction->rollBack();
            Yii::error($e->getMessage(), __METHOD__);
            Yii::$app->session->setFlash('error', 'Errore durante l\'assegnazione: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Ritira un'assegnazione (cancella assegnazione e riporta ticket ad aperto)
     */
    public function ritiraAssegnazione(string $codice_ticket): bool
    {
        $assegnazione = Assegnazioni::findOne(['codice_ticket' => $codice_ticket]);
        $ticket = Ticket::findOne(['codice_ticket' => $codice_ticket]);

        if (!$ticket) {
            return false;
        }

        $ticket->stato = 'aperto';
        if ($assegnazione) {
            $assegnazione->delete();
        }

        return $ticket->save();
    }

    /**
     * Modifica i campi principali del ticket
     */
    public function modificaTicket(string $codice_ticket, string $problema, $priorita, string $ambito, $scadenza): bool
    {
        $ticket = Ticket::findOne(['codice_ticket' => $codice_ticket]);
        if (!$ticket) {
            return false;
        }

        $ticket->problema = $problema;
        $ticket->priorita = $priorita;
        $ticket->ambito = $ambito;
        $ticket->scadenza = $scadenza;
        return $ticket->save();
    }

    /**
     * Verifica validità data (assume $scadenza è timestamp o stringa compatibile)
     */
    public function verifyData($scadenza): bool
    {
        if (is_numeric($scadenza)) {
            return ((int)$scadenza) >= time();
        }

        try {
            $d = new \DateTime($scadenza);
            return $d->getTimestamp() >= time();
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Inserisce/aggiorna scadenza per un ticket
     */
    public function insertScadence(string $codice_ticket, $scadenza): bool
    {
        $ticket = Ticket::findOne(['codice_ticket' => $codice_ticket]);
        if (!$ticket) {
            return false;
        }

        $ticket->scadenza = $scadenza;
        return $ticket->save();
    }

    /**
     * Riapre un ticket (porta a 'aperto')
     */
    public function prolungate(string $codice_ticket): bool
    {
        $ticket = Ticket::findOne(['codice_ticket' => $codice_ticket]);
        if (!$ticket) {
            return false;
        }

        $ticket->stato = 'aperto';
        return $ticket->save();
    }
}
