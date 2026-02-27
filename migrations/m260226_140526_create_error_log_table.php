<?php

use yii\db\Migration;

class m260226_140526_create_error_log_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('error_log', [
            'id' => $this->primaryKey(),
            'response_http' => $this->string(10)->comment('Codice HTTP'),
            'message' => $this->text()->comment('Messaggio errore'),
            'id_cliente' => $this->integer()->null()->comment('ID Cliente'),
            'rotta' => $this->string(500)->null()->comment('URL/Rotta'),
            'created_at' => $this->dateTime()->defaultExpression('CURRENT_TIMESTAMP'),
        ]);

        $this->createIndex('idx_error_log_response_http', 'error_log', 'response_http');
        $this->createIndex('idx_error_log_created_at', 'error_log', 'created_at');
    }

    public function safeDown()
    {
        $this->dropTable('{{%error_log}}');
    }
}