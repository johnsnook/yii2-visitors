<?php

/**
 * This file is part of the Yii2 extension module, yii2-visitor
 *
 * @author John Snook
 * @date 2018-06-28
 * @license https://github.com/johnsnook/yii2-visitor/LICENSE
 * @copyright 2018 John Snook Consulting
 */
use johnsnook\visitor\assets\VisitorAsset;
use johnsnook\visitor\models\Visitor;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel frontend\models\VisitorSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
VisitorAsset::register($this);

$this->title = 'Visitors';
$bc = "$this->title";
$this->params['breadcrumbs'][] = $bc;
$route = Url::to([Yii::$app->controller->id . '/index']);
$visitor = Yii::$app->getModule(Yii::$app->controller->module->id);
if (empty($model->userQuery)) {
    $this->title .= " query: [{$searchModel->userQuery}]";
}

//if ($visitor->bootstrapCssVersion === 4) {
//    $pager = 'johnsnook\visitor\widgets\LinkPager';
//} else {
$pager = 'yii\widgets\LinkPager';
//}
?>
<div class="visitor-index" >
    <h1><?php echo $dataProvider->totalCount . ' ' . Html::encode($bc) ?>!</h1>
    <?php
    echo $this->render('_search', [
        'model' => $searchModel,
    ]);
    ?>
    <?php Pjax::begin(['formSelector' => 'search-form']); ?>
    <?=
    GridView::widget([
        'caption' => $dataProvider->totalCount . ' ' . Html::encode($this->title) . '!',
        'dataProvider' => $dataProvider,
        'filterRowOptions' => ['style' => 'visibility: collapse'],
        'tableOptions' => ['class' => 'table table-striped table-sm'],
//        'pager' => ['class' => $pager],
        'columns' => [
            [
                'class' => '\yii\grid\DataColumn',
                'attribute' => 'ip',
                'format' => 'html',
                'value' => function($data) {
                    $style = '';
                    if ($data->banned || $data->hat_color === Visitor::HAT_COLOR_BLACK) {
                        $style = 'background-color: Black; color: White';
                    } elseif ($data->hat_color === Visitor::HAT_COLOR_WHITE) {
                        $style = 'background-color: #33FF00';
                    }
                    return Html::a($data->ip, ['view', 'id' => $data->ip], ['style' => $style]);
                }
            ],
            [
                'class' => '\yii\grid\DataColumn',
                'attribute' => 'asn',
                'format' => 'html',
                'value' => function($data) {
                    return Html::a($data->asn, ['visitor/index', 'VisitorSearch[userQuery]' => '=asn:' . $data->asn]);
                }
            ],
//            'asn',
            [
                'class' => '\yii\grid\DataColumn',
                'attribute' => 'organization',
                'format' => 'html',
                'value' => function($data) {
                    return Html::a($data->organization, ['visitor/index', 'VisitorSearch[userQuery]' => '=organization:"' . $data->organization . '"']);
                }
            ],
//            'organization',
            [
                'class' => '\yii\grid\DataColumn',
                'attribute' => 'city',
                'format' => 'raw',
                'value' => function($data) {
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
                'class' => '\yii\grid\DataColumn',
                'attribute' => 'visits',
            ],
            [
                'class' => '\yii\grid\DataColumn',
                'attribute' => 'updated_at',
                'value' => function($data) {
                    $dt = new DateTime($data->updated_at);
                    return $dt->format('Y-m-d g:i A');
                }
            ],
        ],
    ]);
    ?>
    <?php Pjax::end(); ?>
</div>
