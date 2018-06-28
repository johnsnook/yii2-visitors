<?php

namespace johnsnook\ipFilter\models;

use yii\db\Expression;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use johnsnook\ipFilter\models\Visitor;

/**
 * VisitorSearch represents the model behind the search form about `common\models\Visitor`.
 */
class VisitorSearch extends Visitor {

    public $location;

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['ip', 'access_type', 'created_at', 'updated_at', 'name', 'message', 'city', 'info'], 'safe'],
            [['user_id', 'visits'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios() {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    public function setCity($val) {
        $this->city = $val;
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
        #$query->andFilterWhere(['=', new Expression("info->>'city'"), $this->city]);
        #$query->andFilterWhere(['=', new Expression("info->>'city' as city"), $this->city]);
        // grid filtering conditions
        $query->andFilterWhere([
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'user_id' => $this->user_id,
            'visits' => $this->visits,
        ]);

        $query->andFilterWhere(['like', 'ip', $this->ip])
                ->andFilterWhere(['like', 'access_type', $this->access_type])
                ->andFilterWhere(['like', 'name', $this->name])
                ->andFilterWhere(['like', 'message', $this->message])
                ->andFilterWhere(['like', 'info', $this->info]);

        return $dataProvider;
    }

}
