<?php

use johnsnook\ipFilter\models\VisitorAgent;
use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\helpers\Html;
use yii\helpers\Url;

Pjax::begin();
// echo $this->render('_search', ['model' => $searchModel]);
echo GridView::widget([
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'columns' => [
        [
            'class' => '\yii\grid\DataColumn',
            'attribute' => 'created_at',
            'value' => function($data) {
                return $data->createdAt;
            }
        ],
        [
            'class' => '\yii\grid\DataColumn',
            'attribute' => 'request',
            'format' => 'html',
            'value' => function($data) {
                return Html::a($data['request'], Url::toRoute($data['request']));
            }
        ],
        'referer',
        [
            'class' => '\yii\grid\DataColumn',
            'attribute' => 'user_agent',
            'value' => function($data) {
                static $agent = '666';
                if ($agent === '666') {
                    $agent = VisitorAgent::findOne($data['user_agent']);
                }
                if (!empty($agent)) {
                    $type = ($agent->agentType == 'Browser' ? '' : "$agent->agentType: ");
                    return "{$type}{$agent->agentName} {$agent->agentVersion}";
                } else {
                    return $data['user_agent'];
                }
            }
        ],
    ],
]);
Pjax::end();
?>
