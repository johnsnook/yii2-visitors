<?php

/**
 * @author John Snook
 * @date Aug 21, 2018
 * @license https://snooky.biz/site/license
 * @copyright 2018 John Snook Consulting
 * Description of index_columns
 */
use johnsnook\visitors\models\Visitor;
use yii\helpers\Html;

return [
    [
        'class' => '\kartik\grid\DataColumn',
        'attribute' => 'ip',
        'format' => 'html',
        'width' => '50px',
        'value' => function($model, $key, $index, $column) {
            $model = (object) $model;
            $style = '';
            if ($model->banned || $model->hat_color === Visitor::HAT_COLOR_BLACK) {
                $style = 'background-color: Black; color: White';
            } elseif ($model->hat_color === Visitor::HAT_COLOR_WHITE) {
                $style = 'background-color: #33FF00';
            }
            return Html::a($model->ip, ['view', 'id' => $model->ip], ['style' => $style]);
        }
    ],
    [
        'class' => '\kartik\grid\DataColumn',
        'attribute' => 'asn',
        'format' => 'html',
        'width' => '50px',
        'value' => function($model, $key, $index, $column) {
            $model = (object) $model;

            return Html::a($model->asn, ['visitor/index', 'VisitorSearch[userQuery]' => '=asn:' . $model->asn]);
        }
    ],
//            'asn',
    [
        'class' => '\kartik\grid\DataColumn',
        'attribute' => 'organization',
        'format' => 'html',
        'value' => function($model, $key, $index, $column) {
            return Html::a($model->organization, ['visitor/index', 'VisitorSearch[userQuery]' => '=organization:"' . $model->organization . '"']);
        }
    ],
//            'organization',
    [
        'class' => '\kartik\grid\DataColumn',
        'attribute' => 'city',
        'format' => 'raw',
        'value' => function($model, $key, $index, $column) {
            $return = '';
            if (!empty($model->city)) {
                $return .= $model->city . (!empty($model->region) ? ', ' : '');
            }
            if (!empty($model->region)) {
                $return .= $model->region . (!empty($model->country) ? ', ' : '');
            }
            if (!empty($model->country)) {
                $return .= $model->country;
            }
            if ($return === '') {
                return '(not set)';
            }
            return $return;
        }
    ],
    [
        'class' => '\kartik\grid\DataColumn',
        'attribute' => 'visits',
    ],
    [
        'class' => '\kartik\grid\DataColumn',
        'attribute' => 'updated_at',
        'width' => '100px',
        'value' => function($model, $key, $index, $column) {
            $dt = new DateTime($model->updated_at);
            return $dt->format('Y-m-d g:i A');
        }
    ]
];
