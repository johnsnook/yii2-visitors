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
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel frontend\models\VisitorSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

//$visitor = Yii::$app->getModule(Yii::$app->controller->module->id);
return [
    [
        'class' => '\kartik\grid\DataColumn',
        'attribute' => 'ip',
        'format' => 'html',
        'headerOptions' => [
//            'class' => 'col-md-1',
            'style' => 'min-width:120px; width:125px; max-width:130px;',
        ],
        'value' => function($model, $key, $index, $column) {
            $model = (object) $model;
            $style = '';
//            if ($model->banned || $model->hat_color === Visitor::HAT_COLOR_BLACK) {
//                $style = 'background-color: Black; color: White';
//            } elseif ($model->hat_color === Visitor::HAT_COLOR_WHITE) {
//                $style = 'background-color: #33FF00';
//            }
            return Html::a($model->ip, ['/visitor/' . $model->ip], ['style' => $style]);
        }
    ],
    [
        'class' => '\kartik\grid\DataColumn',
        'attribute' => 'created_at',
        'headerOptions' => [
//            'class' => 'col-md-1',
            'style' => 'min-width:150px;-width:160px;max-width:170px;',
        ],
        'value' => function($data) {
            return $data->createdAt;
        }
    ],
    [
        'class' => '\kartik\grid\DataColumn',
        'attribute' => 'request',
        'contentOptions' => [
//            'class' => 'col-md-2',
        ],
        'format' => 'html',
        'value' => function($data) {
            $requestSplit = explode('?', $data['request']);
            if (count($requestSplit) > 1) {
                $requestSplit[1] = '...';
            }
            return Html::a(implode('', $requestSplit), Url::toRoute($data['request']));
        }
    ],
    [
        'class' => '\kartik\grid\DataColumn',
        'attribute' => 'referer',
        'contentOptions' => [
//            'class' => 'col-md-3'
        ],
        'format' => 'html',
        'value' => function($data) {
            $referer = $data['referer'];
            $refererSplit = explode('?', $referer);
            if (count($refererSplit) > 1) {
                $refererSplit[1] = '...';
                $referer = implode('', $refererSplit);
            } elseif (strlen($referer) > 25) {
                $referer = substr($referer, 0, 25) . '...';
            }
            return Html::a($referer, Url::to($data['referer']));
        }
    ],
    [
        'class' => '\kartik\grid\DataColumn',
        'attribute' => 'user_agent',
        'contentOptions' => [
//            'class' => 'col-md-3'
        ],
        'value' => function($data) {
            $agent = VisitorAgent::findOne($data['user_agent']);

            if (!empty($agent->agentType)) {
                $type = ($agent->agentType == 'Browser' ? '' : "$agent->agentType: ");
                return "{$type}{$agent->agentName} {$agent->agentVersion}";
            } else {
                return substr($data['user_agent'], 0, 25) . '...';
            }
        }
    ],
];


