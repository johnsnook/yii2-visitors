<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use \yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $searchModel frontend\models\VisitorSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Visitors';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="visitor-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]);   ?>

    <p>
        <?= Html::a('Create Visitor', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?php Pjax::begin(); ?>
    <?=
    GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            //['class' => 'yii\grid\SerialColumn'],

            [
                'class' => '\yii\grid\DataColumn',
                'attribute' => 'ip_address',
                'format' => 'raw',
                'value' => function($data) {
                    return Html::a($data->ip_address, ['visitor/view', 'id' => $data->ip_address]);
                }
            ],
            'access_type',
            //'created_at',
            [
                'class' => '\yii\grid\DataColumn',
                'attribute' => 'updated_at',
                //'width' => '20%',
                'label' => 'Last Accessed',
                'value' => function($data) {
                    return $data->updated_at->format('Y-m-d g:i A');
                }
            ],
            [
                'class' => '\yii\grid\DataColumn',
                'attribute' => 'access_log',
                'value' => function($data) {
                    $log = $data->access_log;
                    ArrayHelper::multisort($log, 'timestamp', SORT_DESC);
                    return $data->updated_at->format('Y-m-d g:i A');
                }
            ],
            //'user_id',
            // 'name',
            // 'message:ntext',
            // 'ip_info',
            // 'access_log',
            // 'proxy_check',
            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]);
    ?>
    <?php Pjax::end(); ?>
</div>
