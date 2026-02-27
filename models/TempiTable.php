<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\TempiTicket;

/**
 * TempiTable represents the model behind the search form of `app\models\TempiTicket`.
 */
class TempiTable extends TempiTicket
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'id_ticket', 'id_operatore', 'tempo_lavorazione', 'pause_effettuate'], 'integer'],
            [['ora_inizio', 'ora_fine', 'tempi_pause', 'ora_pause', 'chiuso_il', 'stato', 'tempo_sospensione'], 'safe'],
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
        $query = TempiTicket::find();

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
            'id_ticket' => $this->id_ticket,
            'id_operatore' => $this->id_operatore,
            'ora_inizio' => $this->ora_inizio,
            'ora_fine' => $this->ora_fine,
            'tempo_lavorazione' => $this->tempo_lavorazione,
            'pause_effettuate' => $this->pause_effettuate,
            'chiuso_il' => $this->chiuso_il,
            'tempo_sospensione' => $this->tempo_sospensione,
        ]);

        $query->andFilterWhere(['like', 'tempi_pause', $this->tempi_pause])
            ->andFilterWhere(['like', 'ora_pause', $this->ora_pause])
            ->andFilterWhere(['like', 'stato', $this->stato]);

        return $dataProvider;
    }
}
