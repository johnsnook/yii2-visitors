<?php

/**
 * This file is part of the Yii2 extension module, yii2-visitor
 *
 * @author John Snook
 * @date 2018-06-28
 * @license https://github.com/johnsnook/yii2-visitor/LICENSE
 * @copyright 2018 John Snook Consulting
 */
use johnsnook\visitors\assets\VisitorAsset;
use kop\y2sp\ScrollPager;
use yii\helpers\Html;
use yii\helpers\Url;
use kartik\grid\GridView;
use kartik\tabs\TabsX;

/* @var $this yii\web\View */
/* @var $searchModel frontend\models\VisitorSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
VisitorAsset::register($this);

$this->title = 'Visitors';
$bc = "$this->title";
;

$this->params['breadcrumbs'][] = $bc;
$route = Url::to([Yii::$app->controller->id . '/index']);
$visitor = Yii::$app->getModule(Yii::$app->controller->module->id);
?>
<style>
    table td[data-col-seq="5"]{
        white-space: nowrap;  /** added **/
    }

</style>
<h1><?php echo $dataProvider->totalCount . ' ' . Html::encode($bc) ?>!</h1>
<?php
echo $this->render('/search/searchForm', [
    'searchModel' => $searchModel,
]);

$grid = GridView::widget([
            //'caption' => $dataProvider->totalCount . ' ' . Html::encode($this->title) . '!',
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'columns' => require __DIR__ . '/index-columns.php',
            'filterRowOptions' => ['style' => 'visibility: collapse'],
            'pjax' => true,
            'bordered' => true,
            'striped' => false,
            'condensed' => true,
            'responsive' => true,
            'hover' => true,
            'floatHeader' => true,
//        'perfectScrollbar' => true,
//        'floatHeaderOptions' => ['scrollingTop' => $scrollingTop],
            'showPageSummary' => true,
            'panel' => [
                'type' => GridView::TYPE_INFO],
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
        'linkOptions' => ['data-url' => Url::to(['/visitors/visitor/map'])]
    ],
//    [
//        'label' => '<i class="glyphicon glyphicon-stats"></i> Charts',
//        'content' => 'Loading (hopefully!) ...',
//        'linkOptions' => ['data-url' => Url::to(['/visitors/visits/graph'])]
//
//    //'linkOptions' => [...],
//    ],
];
echo TabsX::widget([
    'encodeLabels' => false,
    'bordered' => true,
    'position' => TabsX::POS_LEFT,
    'items' => $tabItems,
//    'options' => ['class' => 'nav-pills'], // set this to nav-tab to get tab-styled navigation
]);
?>
