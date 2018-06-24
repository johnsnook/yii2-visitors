<?php

/**
 * Uses mapquest/osm static map
 * @see https://developer.mapquest.com/documentation/static-map-api/v4/map/get/
 */
use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\widgets\Pjax;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $model frontend\models\Visitor */

$this->title = empty($model->name) ? $model->ip : $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Visitors', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="visitor-view">

    <h1><?= Html::encode($this->title) ?></h1>
<!--    <p>
    <?php
//        echo Html::a('Update', ['update', 'id' => $model->ip], ['class' => 'btn btn-primary']);
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
        <div class="col-6">
            <?php
            echo DetailView::widget([
                'model' => $model,
                'attributes' => [
                    'ip',
                    'access_type',
                    [
                        'attribute' => 'created_at',
                        'value' => function($data) {
                            return $data->created_at->format('Y-m-d g:i A');
                        }
                    ],
                    [
                        'attribute' => 'updated_at',
                        'value' => function($data) {
                            return $data->updated_at->format('Y-m-d g:i A');
                        }
                    ],
                    //'user_id',
                    'name',
                    'message:ntext',
                ],
            ])
            ?>
        </div>
        <div class="col-6">
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
    <div class="visitorlog-index">

        <?php //Pjax::begin(); ?>
        <?php // echo $this->render('_search', ['model' => $searchModel]);  ?>
        <?=
        GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'columns' => [
                //['class' => 'yii\grid\SerialColumn'],
                //'ip',
                [
                    'class' => '\yii\grid\DataColumn',
                    'attribute' => 'created_at',
                    'value' => function($data) {
                        return $data['created_at']->format('Y-m-d g:i A');
//                    $dt = new DateTime($data['created_at']);
//                    return $dt->format('Y-m-d g:i A');
                    }
                ],
                //'created_at',
                'request',
                'referer',
                //'user_agent',
                ['class' => 'yii\grid\ActionColumn'],
            ],
        ]);
        ?>
        <?php //Pjax::end(); ?>
    </div>
</div>
