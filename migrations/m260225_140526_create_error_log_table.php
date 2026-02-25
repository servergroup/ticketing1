<?php

use yii\db\Migration;

/**
 * Class m260225_140526_create_error_log_table
 */
class m260225_140526_create_error_log_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('{{%error_log}}', [
            'id' => $this->primaryKey(),
            'type' => $this->string(100)->notNull()->comment('Tipo di errore'),
            'message' => $this->text()->notNull()->comment('Messaggio'),
            'code' => $this->integer()->null()->comment('Codice'),
            'file' => $this->string(500)->null()->comment('File'),
            'line' => $this->integer()->null()->comment('Linea'),
            'trace' => $this->text()->null()->comment('Stack trace'),
            'url' => $this->string(500)->null()->comment('URL'),
            'user_id' => $this->integer()->null()->comment('Utente'),
            'user_ip' => $this->string(45)->null()->comment('IP'),
            'request_method' => $this->string(10)->null()->comment('Metodo HTTP'),
            'status_code' => $this->integer()->null()->comment('Stato HTTP'),
            'is_handled' => $this->boolean()->defaultValue(false)->comment('Gestito'),
            'created_at' => $this->dateTime()->defaultExpression('CURRENT_TIMESTAMP'),
        ]);

        // Indici
        $this->createIndex(
            'idx_error_log_type',
            '{{%error_log}}',
            'type'
        );

        $this->createIndex(
            'idx_error_log_created_at',
            '{{%error_log}}',
            'created_at'
        );

        $this->createIndex(
            'idx_error_log_user_id',
            '{{%error_log}}',
            'user_id'
        );
    }

    public function safeDown()
    {
        $this->dropTable('{{%error_log}}');
    }
}