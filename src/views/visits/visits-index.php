<?php

/**
 * This file is part of the Yii2 extension module, yii2-visitor
 *
 * @author John Snook
 * @date 2018-06-28
 * @license https://github.com/johnsnook/yii2-visitor/LICENSE
 * @copyright 2018 John Snook Consulting
 */
/* @var $this yii\web\View */
/* @var $model frontend\models\Visitor */
/* @var $searchModel frontend\models\VisitsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

use kartik\grid\GridView;
use yii\bootstrap\Tabs;
use yii\helpers\Url;
use kop\y2sp\ScrollPager;
use yii\bootstrap\Nav;

$this->title = 'Visits';
$this->params['breadcrumbs'][] = $this->title;


echo $this->render('/search/searchForm', [
    'searchModel' => $searchModel,
]);

$grid = GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'filterRowOptions' => ['style' => 'visibility: collapse'],
            'pjax' => true,
            'bordered' => true,
            'striped' => false,
            'condensed' => true,
            'responsive' => false,
            'hover' => true,
            'floatHeader' => true,
            'floatHeaderOptions' => ['zIndex' => 999],
            'panel' => null, //['type' => GridView::TYPE_INFO],
            'containerOptions' => ['style' => 'overflow: auto'], // only set when $responsive = false
            'showPageSummary' => false,
            'pager' => [
                'class' => ScrollPager::className(),
                'container' => '.grid-view',
                'item' => '.kv-grid-table tbody tr',
                'enabledExtensions' => [
                    ScrollPager::EXTENSION_SPINNER,
                ],
            ],
            'columns' => require __DIR__ . '/visits-index-columns.php',
        ]);

$tabItems = [
    [
        'label' => '<i class="glyphicon glyphicon-list-alt"></i> Data',
        'active' => 'true',
        'content' => $grid,
    ],
    [
        'label' => '<i class="glyphicon glyphicon-globe"></i> Map',
        'content' => 'Loading (hopefully!) ...',
        'linkOptions' => ['data-url' => Url::to(['/visitors/visits/map'])]
    ],
    [
        'label' => '<i class="glyphicon glyphicon-stats"></i> Charts',
        'content' => 'Loading (hopefully!) ...',
        'linkOptions' => ['data-url' => Url::to(['/visitors/visits/graph'])]

    //'linkOptions' => [...],
    ],
];

echo Tabs::widget([
    'encodeLabels' => false,
    'items' => $tabItems,
//    'options' => ['class' => 'nav-pills'], // set this to nav-tab to get tab-styled navigation
]);
