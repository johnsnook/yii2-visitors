<?php

/**
 * This file is part of the Yii2 extension module, yii2-ip-filter
 *
 * @author John Snook
 * @date 2018-06-28
 * @license https://github.com/johnsnook/yii2-ip-filter/LICENSE
 * @copyright 2018 John Snook Consulting
 */

namespace johnsnook\ipFilter\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use johnsnook\ipFilter\models\VisitorLog;

/**
 * VisitorLogSearch represents the model behind the search form of `common\models\VisitorLog`.
 */
class VisitorLogSearch extends VisitorLog {

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['id'], 'integer'],
            [['ip', 'created_at', 'request', 'referer', 'user_agent'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
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
        $query = VisitorLog::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['created_at' => SORT_DESC]]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'ip' => $this->ip,
            'created_at' => $this->created_at,
        ]);

        $query->andFilterWhere(['ilike', 'request', $this->request])
                ->andFilterWhere(['ilike', 'referer', $this->referer])
                ->andFilterWhere(['ilike', 'user_agent', $this->user_agent]);

        return $dataProvider;
    }

}
