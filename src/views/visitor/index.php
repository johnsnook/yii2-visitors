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
<div class="visitor-index" >
    <h1><?php echo $dataProvider->totalCount . ' ' . Html::encode($bc) ?>!</h1>
    <?php
    echo $this->render('/search/searchForm', [
        'searchModel' => $searchModel,
    ]);

    echo GridView::widget([
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
    ?>
</div>
