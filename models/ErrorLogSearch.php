<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\ErrorLog;

/**
 * ErrorLogSearch represents the model behind the search form of `app\models\ErrorLog`.
 */
class ErrorLogSearch extends ErrorLogHttp
{
    public function rules()
    {
        return [
            [['response_http', 'message'], 'required'],
            [['response_http'], 'string', 'max' => 10],
            [['message'], 'string'],
            [['id_cliente'], 'integer'],
            [['rotta'], 'string', 'max' => 500],
        ];
    }

    public function search($params)
    {
        $query = ErrorLogHttp::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => ['created_at' => SORT_DESC],
            ],
            'pagination' => [
                'pageSize' => 50,
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'type' => $this->type,
            'code' => $this->code,
            'user_id' => $this->user_id,
            'status_code' => $this->status_code,
            'is_handled' => $this->is_handled,
        ]);

        $query->andFilterWhere(['like', 'message', $this->message])
              ->andFilterWhere(['like', 'file', $this->file])
              ->andFilterWhere(['like', 'user_ip', $this->user_ip])
              ->andFilterWhere(['like', 'request_method', $this->request_method]);

        if ($this->created_at) {
            $query->andFilterWhere(['>=', 'created_at', $this->created_at]);
        }

        return $dataProvider;
    }
}