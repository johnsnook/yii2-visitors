<?php

namespace johnsnook\ipFilter\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use johnsnook\ipFilter\models\Visitor;

/**
 * VisitorSearch represents the model behind the search form about `frontend\models\Visitor`.
 */
class VisitorSearch extends Visitor {

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['ip_address', 'access_type', 'created_at', 'updated_at', 'name', 'message', 'ip_info', 'access_log', 'proxy_check'], 'safe'],
            [['user_id'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios() {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params) {
        $query = Visitor::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'user_id' => $this->user_id,
        ]);

        $query->andFilterWhere(['like', 'ip_address', $this->ip_address])
                ->andFilterWhere(['like', 'access_type', $this->access_type])
                ->andFilterWhere(['like', 'name', $this->name])
                ->andFilterWhere(['like', 'message', $this->message])
                ->andFilterWhere(['like', 'ip_info', $this->ip_info])
                ->andFilterWhere(['like', 'access_log', $this->access_log])
                ->andFilterWhere(['like', 'proxy_check', $this->proxy_check]);

        return $dataProvider;
    }

}
