<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\Expression;
use app\models\Ticket;

/**
 * ticketfunction represents the model behind the search form of `app\models\Ticket`.
 */
class ticketfunction extends Ticket
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'id_cliente'], 'integer'],
            [['problema', 'reparto', 'codice_ticket', 'stato', 'scadenza', 'data_invio', 'priorita'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     * @param string|null $formName Form name to be used into `->load()` method.
     *
     * @return ActiveDataProvider
     */
    public function search($params, $formName = null)
    {
        $query = Ticket::find();
        $identity = Yii::$app->user->identity ?? null;
        if ($identity !== null && in_array($identity->ruolo, ['developer', 'ict', 'itc', 'sistemista'], true)) {
            $department = ticketFunctions::departmentFromRole($identity->ruolo);
            $aliases = ticketFunctions::departmentAliases($department);
            if (!empty($aliases)) {
                $query->andWhere(['in', new Expression('LOWER(reparto)'), $aliases]);
            }
        }

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params, $formName);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'scadenza' => $this->scadenza,
            'data_invio' => $this->data_invio,
            'id_cliente' => $this->id_cliente,
        ]);

        $query->andFilterWhere(['like', 'problema', $this->problema])
            ->andFilterWhere(['like', 'reparto', $this->reparto])
            ->andFilterWhere(['like', 'codice_ticket', $this->codice_ticket])
            ->andFilterWhere(['like', 'stato', $this->stato])
            ->andFilterWhere(['like', 'priorita', $this->priorita]);

        return $dataProvider;
    }
}
