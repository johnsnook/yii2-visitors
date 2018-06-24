<?php

use johnsnook\ipFilter\models\Visitor;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $searchModel frontend\models\VisitorSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Visitors';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="visitor-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]);   ?>

    <?php Pjax::begin(); ?>
    <?=
    GridView::widget([
        'dataProvider' => $dataProvider,
        //'filterModel' => $searchModel,
        'columns' => [
            [
                'class' => '\yii\grid\DataColumn',
                'attribute' => 'ip',
                'format' => 'html',
                'value' => function($data) {
                    $data = (object) $data;
                    $style = '';
                    if ($data->access_type === Visitor::ACCESS_LIST_BLACK) {
                        $style = 'background-color: Black; color: White';
                    } elseif ($data->access_type === Visitor::ACCESS_LIST_WHITE) {
                        $style = 'background-color: GreenYellow';
                    }
                    return Html::a($data->ip, ['visitor/view', 'id' => $data->ip], ['style' => $style]);
                }
            ],
            [
                'class' => '\yii\grid\DataColumn',
                'attribute' => 'city',
                'format' => 'raw',
                'value' => function($data) {

                    $data = json_decode(json_decode($data['info']), true);

                    if (!empty($data['city'])) {
                        return $data['city'];
                    } else if (!empty($data['region'])) {
                        return $data['region'];
                    } else if (!empty($data['country'])) {
                        return $data['country'];
                    } else {
                        return '(not set)';
//                        return '<div class="bg-primary"><a href="'
//                                . Url::toRoute(['ipinfo/view', 'ip' => $data['ip']])
//                                . '"data-pjax="1" class="text-white" role="modal-remote">Set</a></div>';
                    }
                }
            ],
            [
                'class' => '\yii\grid\DataColumn',
                'attribute' => 'count',
            ],
            [
                'class' => '\yii\grid\DataColumn',
                'attribute' => 'recent',
                'label' => 'Last Accessed',
                'value' => function($data) {
                    $dt = new DateTime($data['recent']);
                    return $dt->format('Y-m-d g:i A');
                }
            ],
            //'user_id',
            // 'name',
            // 'message:ntext',
            // 'info',
            // 'access_log',
            // 'proxy_check',
            ['class' => 'yii\grid\ActionColumn'],
        ],
        'pager' => [
            'linkOptions' => [
                'class' => "page-link",
            ],
            'prevPageCssClass' => 'page-item',
            'nextPageCssClass' => 'page-item',
            'disabledListItemSubTagOptions' => [
                'class' => 'page-link',
                'tag' => 'a',
                'tabIndex' => -1
            ],
            'pageCssClass' => 'page-item',
            'options' => [
                'class' => 'pagination',
            //'style' => 'display:inline-block;float:left;margin:20px 10px 20px 0;width:auto;'
            ],
        ],
    ]);
    ?>
    <?php Pjax::end(); ?>
</div>
