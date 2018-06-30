<?php

/**
 * This file is part of the Yii2 extension module, yii2-ip-filter
 *
 * @author John Snook
 * @date 2018-06-28
 * @license https://github.com/johnsnook/yii2-ip-filter/LICENSE
 * @copyright 2018 John Snook Consulting
 */
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
use yii\widgets\Pjax;
use johnsnook\ipFilter\assets\VisitorAsset;

/* @var $this yii\web\View */
/* @var $searchModel frontend\models\VisitorSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
VisitorAsset::register($this);
$this->title = 'Visitors';
$this->params['breadcrumbs'][] = $this->title;
$route = Url::to([Yii::$app->controller->id . '/index']);
?>
<div class="visitor-index" >
    <form id="search-form" action="<?= $route ?>" method="get" role="form">
        <div class="row">
            <div class="col-lg-1"></div>
            <div class="col-lg-10">
                <div class="input-group">
                    <input type="hidden" id="field" name="field" value="" class="form-control">
                    <input type="hidden" id="field" name="page" value="" class="form-control" value="1">
                    <input type="text" name="search" class="form-control" aria-label="Visitor Search" autofocus="true" value="<?= $search ?>">
                    <div class="input-group-btn">
                        <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Search <span class="caret"></span></button>
                        <ul class="dropdown-menu dropdown-menu-right">
                            <li><a href="javascript:setField('ip')" class="dropdown-item">IP Address</a></li>
                            <li><a href="javascript:setField('city')" class="dropdown-item">City</a></li>
                            <li><a href="javascript:setField('region')" class="dropdown-item">Region</a></li>
                            <li><a href="javascript:setField('country')" class="dropdown-item">Country</a></li>
                            <li><a href="javascript:setField('proxy')" class="dropdown-item">Proxy Type</a></li>
                            <li><a href="javascript:setField('organization')" class="dropdown-item">Organization</a></li>
                            <li><a href="javascript:setField('updated_at')" class="dropdown-item">Date</a></li>
                            <li role="separator" class="divider dropdown-divider"></li>
                            <li><a href="javascript:setField('log-request')" class="dropdown-item">Request</a></li>
                            <li><a href="javascript:setField('log-request')" class="dropdown-item">Referer</a></li>
                            <li><a href="javascript:setField('log-user_agent')" class="dropdown-item">User Agent</a></li>
                        </ul>
                    </div><!-- /btn-group -->
                    <div class="input-group-btn">
                        <a href="<?= $route ?>" type="button" class="btn btn-default btn-outline-dark" role="button">Reset</a>
                    </div>
                </div><!-- /input-group -->
            </div>
            <div class="col-lg-1"></div>
        </div>
        <div class="row"><?php #echo $dataProvider->query->createCommand()->getRawSql()         ?></div>
    </form>
    <h1><?php echo $dataProvider->totalCount . ' ' . Html::encode($this->title) ?>!</h1>

    <?php Pjax::begin(['formSelector' => 'search-form']); ?>
    <?=
    GridView::widget([
        'dataProvider' => $dataProvider,
        #'filterModel' => $searchModel,
        'summary' => false,
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
                    if ($data->is_blacklisted) {
                        $style = 'background-color: Black; color: White';
                    }
                    return Html::a($data->ip, ['view', 'id' => $data->ip], ['style' => $style]);
                }
            ],
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
