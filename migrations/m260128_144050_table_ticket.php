<?php

use yii\db\Migration;

class m260128_144050_table_ticket extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('ticket',[
            'id'=>$this->primaryKey(),
            'problema'=>$this->string(),
            'reparto'=>$this->string(),
            'codice_ticket'=>$this->string()->unique(),
            'stato'=>$this->string(),
            'scadenza'=>$this->date(),
            'data_invio'=>$this->dateTime(),
            'id_cliente'=>$this->integer(),
            'priorita'=>$this->string()
            
         
            
        ]);

        $this->addforeignKey(
            'fk_id_cliente',
            'ticket',
            'id_cliente',
            'personale',
            'id',
            $delete=null,
            $delete=null
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dopTable('fk_id_cliente','ticket');
       $this->dropTable('ticket');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m260128_144050_table_operatore cannot be reverted.\n";

        return false;
    }
    */
}
