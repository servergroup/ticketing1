<?php

use yii\db\Migration;

class m260226_101500_add_telegram_columns_to_personale extends Migration
{
    public function safeUp()
    {
        $this->addColumn('personale', 'telegram_username', $this->string()->null());
        $this->addColumn('personale', 'telegram_chat_id', $this->string()->null());
    }

    public function safeDown()
    {
        $this->dropColumn('personale', 'telegram_chat_id');
        $this->dropColumn('personale', 'telegram_username');
    }
}
