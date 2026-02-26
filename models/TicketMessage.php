<?php

namespace app\models;

use Yii;

/**
 * @property int $id
 * @property int|null $ticket_id
 * @property int $sender_id
 * @property int $recipient_id
 * @property string $subject
 * @property string $body
 * @property int $is_read
 * @property string $created_at
 * @property string|null $read_at
 *
 * @property Ticket|null $ticket
 * @property User $sender
 * @property User $recipient
 */
class TicketMessage extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'ticket_message';
    }

    public function rules()
    {
        return [
            [['ticket_id', 'sender_id', 'recipient_id', 'is_read'], 'integer'],
            [['sender_id', 'recipient_id', 'subject', 'body'], 'required'],
            [['body'], 'string'],
            [['created_at', 'read_at'], 'safe'],
            [['subject'], 'string', 'max' => 255],
            [['ticket_id'], 'exist', 'skipOnError' => true, 'targetClass' => Ticket::class, 'targetAttribute' => ['ticket_id' => 'id']],
            [['sender_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['sender_id' => 'id']],
            [['recipient_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['recipient_id' => 'id']],
        ];
    }

    public function beforeValidate()
    {
        if ($this->isNewRecord && empty($this->created_at)) {
            $this->created_at = date('Y-m-d H:i:s');
        }

        if ($this->is_read === null) {
            $this->is_read = 0;
        }

        return parent::beforeValidate();
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'ticket_id' => 'Ticket',
            'sender_id' => 'Mittente',
            'recipient_id' => 'Destinatario',
            'subject' => 'Oggetto',
            'body' => 'Messaggio',
            'is_read' => 'Letto',
            'created_at' => 'Creato il',
            'read_at' => 'Letto il',
        ];
    }

    public function getTicket()
    {
        return $this->hasOne(Ticket::class, ['id' => 'ticket_id']);
    }

    public function getSender()
    {
        return $this->hasOne(User::class, ['id' => 'sender_id']);
    }

    public function getRecipient()
    {
        return $this->hasOne(User::class, ['id' => 'recipient_id']);
    }
}

