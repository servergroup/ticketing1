<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\User;

/**
 * UserTable represents the model behind the search form of `app\models\User`.
 */
class UserTable extends User
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'tentativi', 'approvazione', 'blocco'], 'integer'],
            [['nome', 'cognome', 'username', 'password', 'auth_key', 'access_token', 'email', 'ruolo', 'partita_iva', 'azienda', 'recapito_telefonico', 'immagine', 'token', 'telegram_username', 'telegram_chat_id'], 'safe'],
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
        $query = User::find();

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
            'tentativi' => $this->tentativi,
            'approvazione' => $this->approvazione,
            'blocco' => $this->blocco,
        ]);

        $query->andFilterWhere(['like', 'nome', $this->nome])
            ->andFilterWhere(['like', 'cognome', $this->cognome])
            ->andFilterWhere(['like', 'username', $this->username])
            ->andFilterWhere(['like', 'password', $this->password])
            ->andFilterWhere(['like', 'auth_key', $this->auth_key])
            ->andFilterWhere(['like', 'access_token', $this->access_token])
            ->andFilterWhere(['like', 'email', $this->email])
            ->andFilterWhere(['like', 'ruolo', $this->ruolo])
            ->andFilterWhere(['like', 'partita_iva', $this->partita_iva])
            ->andFilterWhere(['like', 'azienda', $this->azienda])
            ->andFilterWhere(['like', 'recapito_telefonico', $this->recapito_telefonico])
            ->andFilterWhere(['like', 'immagine', $this->immagine])
            ->andFilterWhere(['like', 'token', $this->token])
            ->andFilterWhere(['like', 'telegram_username', $this->telegram_username])
            ->andFilterWhere(['like', 'telegram_chat_id', $this->telegram_chat_id]);

        return $dataProvider;
    }
}
