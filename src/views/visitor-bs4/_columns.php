<?php

use yii\helpers\Url;

return [
    [
        'class' => 'kartik\grid\ActionColumn',
        'dropdown' => false,
        'vAlign' => 'middle',
        'urlCreator' => function($action, $model, $key, $index) {
            return Url::to([$action, 'id' => $key]);
        },
        'viewOptions' => ['role' => 'modal-remote', 'title' => 'View', 'data-toggle' => 'tooltip'],
        'updateOptions' => ['role' => 'modal-remote', 'title' => 'Update', 'data-toggle' => 'tooltip'],
        'deleteOptions' => ['role' => 'modal-remote', 'title' => 'Delete',
            'data-confirm' => false, 'data-method' => false, // for overide yii data api
            'data-request-method' => 'post',
            'data-toggle' => 'tooltip',
            'data-confirm-title' => 'Are you sure?',
            'data-confirm-message' => 'Are you sure want to delete this item'],
    ],
    [
        'class' => '\kartik\grid\DataColumn',
        'attribute' => 'ip',
        'format' => 'html',
        'value' => function($data) {
            $style = '';
            if ($data->banned || $data->hat_color === Visitor::HAT_COLOR_BLACK) {
                $style = 'background-color: Black; color: White';
            }
            return Html::a($data->ip, ['view', 'id' => $data->ip], ['style' => $style]);
        }
    ],
    [
        'class' => '\kartik\grid\DataColumn',
        'attribute' => 'asn',
    ],
    [
        'class' => '\kartik\grid\DataColumn',
        'attribute' => 'organization',
    ],
    [
        'class' => '\kartik\grid\DataColumn',
        'attribute' => 'city',
        'format' => 'raw',
        'value' => function($data) {
            #$data = json_decode(json_decode($data['info']), true);
            $return = '';
            if (!empty($data->city)) {
                $return .= $data->city . (!empty($data->region) ? ', ' : '');
            }
            if (!empty($data->region)) {
                $return .= $data->region . (!empty($data->country) ? ', ' : '');
            }
            if (!empty($data->country)) {
                $return .= $data->country;
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
        'value' => function($data) {
            $dt = new DateTime($data->updated_at);
            return $dt->format('Y-m-d g:i A');
        }
    ],
];
