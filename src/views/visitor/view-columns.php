<?php

/**
 * This file is part of the Yii2 extension module, yii2-visitor
 *
 * @author John Snook
 * @date 2018-06-28
 * @license https://github.com/johnsnook/yii2-visitor/LICENSE
 * @copyright 2018 John Snook Consulting
 */
use johnsnook\visitor\models\VisitorAgent;
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel frontend\models\VisitorSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

//$visitor = Yii::$app->getModule(Yii::$app->controller->module->id);
return [
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
            $requestSplit = explode('?', $data['request']);
            if (count($requestSplit) > 1) {
                $requestSplit[1] = '...';
            }
            return Html::a(implode('', $requestSplit), Url::toRoute($data['request']));
        }
    ],
    [
        'class' => '\yii\grid\DataColumn',
        'attribute' => 'referer',
        'format' => 'html',
        'value' => function($data) {
            $refererSplit = explode('?', $data['referer']);
            if (count($refererSplit) > 1) {
                $refererSplit[1] = '...';
            }
            return Html::a(implode('', $refererSplit), Url::to($data['referer']));
        }
    ],
    [
        'class' => '\yii\grid\DataColumn',
        'attribute' => 'user_agent',
        'value' => function($data) {
            $agent = VisitorAgent::findOne($data['user_agent']);

            if (!empty($agent)) {
                $type = ($agent->agentType == 'Browser' ? '' : "$agent->agentType: ");
                return "{$type}{$agent->agentName} {$agent->agentVersion}";
            } else {
                return $data['user_agent'];
            }
        }
    ],
];


