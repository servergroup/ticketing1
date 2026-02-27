<?php

use yii\db\Migration;

class m260227_120000_add_nazione_to_personale extends Migration
{
    public function safeUp()
    {
        $this->addColumn('personale', 'nazione', $this->string()->null());
    }

    public function safeDown()
    {
        $this->dropColumn('personale', 'nazione');
    }
}
