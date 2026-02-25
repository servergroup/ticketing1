<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "ticket".
 *
 * @property int $id
 * @property string|null $problema
 * @property string|null $ambito
 * @property string|null $codice_ticket
 * @property string|null $stato
 * @property string|null $scadenza
 * @property string|null $data_invio
 * @property string|null $azienda
 * @property int|null $id_cliente
 *
 * @property Cliente $cliente
 */
class Ticket extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'ticket';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['problema', 'ambito', 'codice_ticket', 'stato', 'data_invio','scadenza', 'id_cliente','priorita'], 'default', 'value' => null],
     
            [['id_cliente'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['id_cliente' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'problema' => 'Problema',
            'ambito' => 'Ambito',
            'codice_ticket' => 'Codice Ticket',
            'stato' => 'Stato',
            'scadenza' => 'Scadenza',
            'data_invio' => 'Data Invio',
            'azienda' => 'Azienda',
            'id_cliente' => 'Id Cliente',
        ];
    }

    /**
     * Gets query for [[Cliente]].
     *
     * @return \yii\db\ActiveQuery
     */
public function getCliente()
{
    return $this->hasOne(User::class, ['id' => 'id_cliente']);
}


}
