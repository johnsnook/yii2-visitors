<?php

/**
 * Uses mapquest/osm static map
 * @see https://developer.mapquest.com/documentation/static-map-api/v4/map/get/
 */
use johnsnook\ipFilter\models\VisitorAgent;
use johnsnook\ipFilter\models\Visitor;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;
//use yii\widgets\Pjax;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $model frontend\models\Visitor */

$this->title = empty($model->name) ? $model->ip : $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Visitors', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
$ipFilter = Yii::$app->getModule('ipFilter');
?>
<div class="visitor-view">

    <h1><?php
        echo Html::encode($this->title);
        if ($ipFilter->isAdmin) {
            if ($model->access_type !== Visitor::ACCESS_LIST_BLACK) {
                echo Html::a('Blacklist', ['blacklist', 'id' => $model->ip], ['class' => 'btn btn-sm btn-dark float-right']);
            }
            echo Html::a('Update', ['update', 'id' => $model->ip], ['class' => 'btn btn-sm btn-warning float-right']);
        }
        ?> </h1>
<!--    <p>
    <?php
//        echo Html::a('Delete', ['delete', 'id' => $model->ip], [
//            'class' => 'btn btn-danger',
//            'data' => [
//                'confirm' => 'Are you sure you want to delete this item?',
//                'method' => 'post',
//            ],
//        ]);
    ?>
    </p>-->
    <div class="row">
        <div class="col-5">
            <?php
            echo DetailView::widget([
                'model' => $model,
                'attributes' => [
                    'ip',
                    'access_type',
                    [
                        'attribute' => 'created_at',
                        'value' => function($data) {
                            return $data->createdAt;
                        }
                    ],
                    [
                        'attribute' => 'updated_at',
                        'value' => function($data) {
                            return $data->updatedBy;
                        }
                    ],
                    //'user_id',
                    'name',
                    'message:ntext',
                ],
            ])
            ?>
        </div>
        <div class="col-7">
            <table style="width:100%" class="table table-striped table-bordered detail-view">
                <?php
                $fields = (array) $model->info;
                foreach ($fields as $key => $field) {
                    if ($key === 'ip') {
                        continue;
                    }
                    echo "<tr><th>$key</th><td>$field</td></tr>";
                }
                ?>
            </table>
        </div>
    </div>
    <div class="row"><div class="col-12" style="text-align: center">
            <?php
            $info = (object) $model->info;
            $mapKey = Yii::$app->getModule('ipFilter')->mapquestKey;
//https://open.mapquestapi.com/staticmap/v4/getmap?key=KEY&size=600,400&zoom=13&center=47.6062,-122.3321

            $mapUrl = "https://open.mapquestapi.com/staticmap/v4/getmap?key=$mapKey";
            $mapUrl .= "&size=1000,500&zoom=16&center={$model->latitude},{$model->longitude}";
            $mapUrl .= "&mcenter={$model->latitude},{$model->longitude},0,0";
            $mapUrl .= "&type=map&imagetype=png&scalebar=false";
            $googUrl = "https://www.google.com/maps/place/{$model->latitude},{$model->longitude}";
            $location = "$info->city, $info->region, $info->country Map";
            $staticMap = Html::img($mapUrl, ['width' => '1000', 'alt' => $location]);
            echo Html::a($staticMap, $googUrl, ['target' => "_blank"]);
            ?>
        </div>
    </div>
    <div class="visitorlog-index" style="font-size: .96em">

        <?php //Pjax::begin();    ?>
        <?php // echo $this->render('_search', ['model' => $searchModel]);   ?>
        <?=
        GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'columns' => [
                [
                    'class' => '\yii\grid\DataColumn',
                    'attribute' => 'created_at',
                    'value' => function($data) {
                        return $data['created_at']->format('Y-m-d g:i A');
                    }
                ],
                [
                    'class' => '\yii\grid\DataColumn',
                    'attribute' => 'request',
                    'format' => 'html',
                    'value' => function($data) {
                        return Html::a($data['request'], Url::toRoute($data['request']));
                    }
                ],
                'referer',
                [
                    'class' => '\yii\grid\DataColumn',
                    'attribute' => 'user_agent',
                    'value' => function($data) {
                        static $agent = '666';
                        if ($agent === '666') {
                            $agent = VisitorAgent::findOne($data['user_agent']);
                        }
                        if (!empty($agent)) {
                            $type = ($agent->agentType == 'Browser' ? '' : "$agent->agentType: ");
                            return "{$type}{$agent->agentName} {$agent->agentVersion}";
                        } else {
                            return $data['user_agent'];
                        }
                    }
                ],
            ],
            'pager' => [
                'class' => \kop\y2sp\ScrollPager::className(),
                'container' => '.grid-view tbody',
                'item' => 'tr',
                'paginationSelector' => '.grid-view .pagination',
                'triggerOffset' => 100,
                'triggerTemplate' => '<tr class="ias-trigger"><td colspan="100%" style="text-align: center"><a style="cursor: pointer">{text}</a></td></tr>',
            ],
        ]);
        ?>
        <?php //Pjax::end();  ?>
    </div>
</div>
