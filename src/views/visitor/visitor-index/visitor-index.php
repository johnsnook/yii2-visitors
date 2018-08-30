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
//use kartik\tabs\TabsX;
use yii\bootstrap\Tabs;

/* @var $this yii\web\View */
/* @var $searchModel \johnsnook\visitors\models\VisitorSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
VisitorAsset::register($this);

$this->title = 'Visitors';
$bc = "$this->title";

$this->params['breadcrumbs'][] = $bc;
$route = Url::to([Yii::$app->controller->id . '/index']);

$this->registerJsFile('https://www.gstatic.com/charts/loader.js', ['position' => \yii\web\View::POS_END]);
?>
<style>
    table td[data-col-seq="5"]{
        white-space: nowrap;  /** added **/
    }
    div.kv-thead-float{
        overflow: hidden
    }
</style>
<h1><?= $dataProvider->totalCount . ' ' . Html::encode($bc) ?>!</h1>
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
            'bordered' => false,
            'striped' => false,
            'condensed' => true,
            'responsive' => true,
            'hover' => true,
            'floatHeader' => true,
            'showPageSummary' => true,
            'panel' => false,
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
        //'url' => Url::to(['/visitors/visitor/map']),
        'linkOptions' => ['data-url' => Url::to(['/visitors/visitor/map'])],
        'content' => 'Loading (hopefully!) ...',
//        'content' => $this->render('visitor-index-map', ['searchModel' => $searchModel]),
//        'linkOptions' => ['data-url' => Url::to(['/visitors/visitor/map'])]
    ],
//    [
//        'label' => '<i class="glyphicon glyphicon-stats"></i> Charts',
//        'content' => 'Loading (hopefully!) ...',
//        'linkOptions' => ['data-url' => Url::to(['/visitors/visits/graph'])]
//
//    //'linkOptions' => [...],
//    ],
];
$ajaxTabEvent = <<< JS
    function (e) {
        e.preventDefault();

        if (typeof e.target.dataset.url !== 'undefined') {
            console.log(e);

            let params = decodeURI(window.location.href).split('?')[1];
            let url = e.target.dataset.url;
            if (typeof params !== 'undefined') {
        console.log(params);
                url += '?' + params;
            }
            if (typeof e.target.dataset.loaded === 'undefined') {
                $.get(url, function (data) {
                    $(e.target.hash).html(data);
                });
                e.target.dataset.loaded = true;
            }
        }

        $(this).tab('show');
    }
JS;

echo Tabs::widget([
    'clientEvents' => [
        'click' => $ajaxTabEvent
    ],
    'encodeLabels' => false,
//    'bordered' => true,
//    'position' => TabsX::POS_LEFT,
    'items' => $tabItems,
//    'options' => ['class' => 'nav-pills'], // set this to nav-tab to get tab-styled navigation
]);
?>
<script>
</script>