<?php

use johnsnook\ipFilter\models\Visitor;
use johnsnook\ipFilter\models\VisitorLog;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\helpers\ArrayHelper;
use johnsnook\ipFilter\assets\VisitorAsset;

/* @var $this yii\web\View */
/* @var $searchModel frontend\models\VisitorSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
VisitorAsset::register($this);
$this->title = 'Visitors';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="visitor-index" style="text-align: center">
    <form id="search-form" action="/visitor/index" method="get" role="form">
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
                        <a href="/visitor/index" type="button" class="btn btn-default btn-outline-dark" role="button">Reset</a>
                    </div>
                </div><!-- /input-group -->
            </div>
            <div class="col-lg-1"></div>
        </div>
        <div class="row"><?= $dataProvider->query->createCommand()->getRawSql() ?></div>
    </form>
    <h1><?php echo $dataProvider->totalCount . ' ' . Html::encode($this->title) ?>!</h1>

    <?php Pjax::begin(['formSelector' => 'search-form']); ?>
    <?=
    GridView::widget([
        'dataProvider' => $dataProvider,
        #'filterModel' => $searchModel,
        'summary' => false,
        'columns' => [
            ['class' => 'yii\grid\ActionColumn'],
            [
                'class' => '\yii\grid\DataColumn',
                'attribute' => 'ip',
                'format' => 'html',
                'value' => function($data) {
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
                    //$dt = new DateTime();
                    return $data->updated_at->format('Y-m-d g:i A');
                }
            ],
        //'user_id',
        // 'name',
        // 'message:ntext',
        // 'info',
        // 'access_log',
        // 'proxy_check',
        ],
//        'pager' => [
//            'class' => \kop\y2sp\ScrollPager::className(),
//            'container' => '.grid-view tbody',
//            'item' => 'tr',
//            'paginationSelector' => '.grid-view .pagination',
//            'triggerOffset' => 100,
//            'triggerTemplate' => '<tr class="ias-trigger"><td colspan="100%" style="text-align: center"><a style="cursor: pointer">{text}</a></td></tr>',
//        ],
    ]);
    ?>
    <?php Pjax::end(); ?>
</div>
