<?php

use yii\db\Migration;

class m260226_170000_create_ticket_message_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('ticket_message', [
            'id' => $this->primaryKey(),
            'ticket_id' => $this->integer(),
            'sender_id' => $this->integer()->notNull(),
            'recipient_id' => $this->integer()->notNull(),
            'subject' => $this->string(255)->notNull(),
            'body' => $this->text()->notNull(),
            'is_read' => $this->boolean()->notNull()->defaultValue(false),
            'created_at' => $this->dateTime()->notNull(),
            'read_at' => $this->dateTime(),
        ]);

        $this->createIndex('idx_ticket_message_ticket', 'ticket_message', 'ticket_id');
        $this->createIndex('idx_ticket_message_sender', 'ticket_message', 'sender_id');
        $this->createIndex('idx_ticket_message_recipient_read', 'ticket_message', ['recipient_id', 'is_read']);
        $this->createIndex('idx_ticket_message_created_at', 'ticket_message', 'created_at');

        $this->addForeignKey(
            'fk_ticket_message_ticket',
            'ticket_message',
            'ticket_id',
            'ticket',
            'id',
            'SET NULL',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk_ticket_message_sender',
            'ticket_message',
            'sender_id',
            'personale',
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk_ticket_message_recipient',
            'ticket_message',
            'recipient_id',
            'personale',
            'id',
            'CASCADE',
            'CASCADE'
        );
    }

    public function safeDown()
    {
        $this->dropForeignKey('fk_ticket_message_recipient', 'ticket_message');
        $this->dropForeignKey('fk_ticket_message_sender', 'ticket_message');
        $this->dropForeignKey('fk_ticket_message_ticket', 'ticket_message');

        $this->dropIndex('idx_ticket_message_created_at', 'ticket_message');
        $this->dropIndex('idx_ticket_message_recipient_read', 'ticket_message');
        $this->dropIndex('idx_ticket_message_sender', 'ticket_message');
        $this->dropIndex('idx_ticket_message_ticket', 'ticket_message');

        $this->dropTable('ticket_message');
    }
}

