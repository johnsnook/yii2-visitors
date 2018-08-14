<?php

/**
 * This file is part of the Yii2 extension module, yii2-visitor
 *
 * @author John Snook
 * @date 2018-06-28
 * @license https://github.com/johnsnook/yii2-visitor/LICENSE
 * @copyright 2018 John Snook Consulting
 */
use johnsnook\visitor\models\Visitor;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel frontend\models\VisitorSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Visitors';
$this->params['breadcrumbs'][] = $this->title;
$route = Url::to([Yii::$app->controller->id . '/index']);
$visitor = Yii::$app->getModule(Yii::$app->controller->module->id);

$pager = 'johnsnook\visitor\widgets\LinkPager';
?>
<div class="my-5 container-fluid" >
    <div class="row">
        <div class="offset-1 col-10">
            <?php
            echo $this->render('_search', [
                'model' => $searchModel,
            ]);
            ?>
        </div>
    </div>
    <div class="offset-1 col-10 visitor-index my-5">
        <?php Pjax::begin(['formSelector' => 'search-form']); ?>
        <?=
        GridView::widget([
            'caption' => $dataProvider->totalCount . ' ' . Html::encode($this->title) . '!',
            'dataProvider' => $dataProvider,
            #'filterModel' => $searchModel,
            //'options' => ['class' => 'd-flex col-10 mx-auto '],
            'tableOptions' => ['class' => 'bg-white table table-striped table-sm'],
            //'summary' => false,
            'pager' => ['class' => $pager],
            'columns' => [
                [
                    'class' => 'yii\grid\ActionColumn',
                    'template' => '{view} {update}'
                ],
                [
                    'class' => '\yii\grid\DataColumn',
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
                'asn',
                'organization',
//                'hat_color',
//                'hat_rule',
                [
                    'class' => '\yii\grid\DataColumn',
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
</div>
