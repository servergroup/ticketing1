<?php

namespace app\models;

/**
 * This is the model class for table "assegnazioni".
 *
 * @property int $id
 * @property string|null $codice_ticket
 * @property int|null $id_operatore
 * @property string|null $reparto
 * @property string|null $ambito
 *
 * @property Ticket $codiceTicket
 * @property History[] $histories
 * @property User $operatore
 */
class Assegnazioni extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'assegnazioni';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        $rules = [
            [['codice_ticket', 'id_operatore'], 'default', 'value' => null],
            [['id_operatore'], 'integer'],
            [['codice_ticket'], 'string', 'max' => 255],
            [['codice_ticket'], 'unique'],
            [['id_operatore'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['id_operatore' => 'id']],
            [['codice_ticket'], 'exist', 'skipOnError' => true, 'targetClass' => Ticket::class, 'targetAttribute' => ['codice_ticket' => 'codice_ticket']],
        ];

        $departmentAttributes = [];
        if ($this->hasAttribute('reparto')) {
            $departmentAttributes[] = 'reparto';
        }
        if ($this->hasAttribute('ambito')) {
            $departmentAttributes[] = 'ambito';
        }
        if (!empty($departmentAttributes)) {
            $rules[] = [$departmentAttributes, 'default', 'value' => null];
            $rules[] = [$departmentAttributes, 'string', 'max' => 255];
        }

        return $rules;
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        $labels = [
            'id' => 'ID',
            'codice_ticket' => 'Codice Ticket',
            'id_operatore' => 'Operatore',
        ];

        if ($this->hasAttribute('reparto')) {
            $labels['reparto'] = 'Reparto';
        }
        if ($this->hasAttribute('ambito')) {
            $labels['ambito'] = 'Ambito';
        }

        return $labels;
    }

    public function getDepartmentValue(): ?string
    {
        if ($this->hasAttribute('reparto') && !empty($this->reparto)) {
            return (string)$this->reparto;
        }
        if ($this->hasAttribute('ambito') && !empty($this->ambito)) {
            return (string)$this->ambito;
        }

        return null;
    }

    public function setDepartmentValue(string $department): void
    {
        if ($this->hasAttribute('reparto')) {
            $this->setAttribute('reparto', $department);
        }
        if ($this->hasAttribute('ambito')) {
            $this->setAttribute('ambito', $department);
        }
    }

    /**
     * Gets query for [[CodiceTicket]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCodiceTicket()
    {
        return $this->hasOne(Ticket::class, ['codice_ticket' => 'codice_ticket']);
    }

    /**
     * Gets query for [[Histories]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getHistories()
    {
        return $this->hasMany(History::class, ['id_operatore' => 'id_operatore']);
    }

    /**
     * Gets query for [[Operatore]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getOperatore()
    {
        return $this->hasOne(User::class, ['id' => 'id_operatore']);
    }
}

