<?php

/**
 * This file is part of the Yii2 extension module, yii2-visitor
 *
 * @author John Snook
 * @date 2018-06-28
 * @license https://github.com/johnsnook/yii2-visitor/LICENSE
 * @copyright 2018 John Snook Consulting
 */
use johnsnook\visitors\models\VisitorAgent;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
$visitor = Yii::$app->getModule('visitor');

//$visitor = Yii::$app->getModule(Yii::$app->controller->module->id);

$pager = 'johnsnook\visitors\widgets\LinkPager';

echo $this->render('_visitsSearch', [
    'model' => $searchModel,
]);

echo GridView::widget([
    'dataProvider' => $dataProvider,
    'options' => ['class' => 'bg-white '],
    'tableOptions' => ['class' => 'bg-white table table-striped table-sm'],
    'filterModel' => $searchModel,
    'pager' => ['class' => $pager],
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


