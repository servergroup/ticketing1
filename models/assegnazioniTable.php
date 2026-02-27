<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Assegnazioni;

/**
 * assegnazioniTable represents the model behind the search form of `app\models\Assegnazioni`.
 */
class assegnazioniTable extends Assegnazioni
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'id_operatore'], 'integer'],
            [['codice_ticket', 'reparto'], 'safe'],
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
        $query = Assegnazioni::find()->with(['operatore', 'codiceTicket'])->orderBy(['id' => SORT_DESC]);

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
            'id_operatore' => $this->id_operatore,
        ]);

        $query->andFilterWhere(['like', 'codice_ticket', $this->codice_ticket]);

        if (!empty($this->reparto)) {
            $departmentFilters = ['or'];
            if ($this->hasAttribute('reparto')) {
                $departmentFilters[] = ['like', 'reparto', $this->reparto];
            }
            if ($this->hasAttribute('ambito')) {
                $departmentFilters[] = ['like', 'ambito', $this->reparto];
            }
            if (count($departmentFilters) > 1) {
                $query->andWhere($departmentFilters);
            }
        }

        return $dataProvider;
    }
}
