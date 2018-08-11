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
use yii\widgets\DetailView;
use yii\bootstrap\Tabs;

/* @var $this yii\web\View */
/* @var $model frontend\models\Visitor */

$this->title = empty($model->name) ? $model->ip : $model->name;
?>
<div class="visitor-view">
    <div class="row" style="font-size: 36px">
        <div class="col-sm-12" ><?= Html::encode($this->title); ?></div>
    </div>
    <div class="row">
        <div class="col-lg-5 col-md-5">
            <?php
            echo DetailView::widget([
                'model' => $model,
                'attributes' => [
                    'ip',
                    [
                        'attribute' => 'created_at',
                        'value' => function($data) {
                            $dt = new DateTime($data->created_at);
                            return $dt->format('Y-m-d g:i A');
                        }
                    ],
                    [
                        'attribute' => 'updated_at',
                        'value' => function($data) {
                            $dt = new DateTime($data->updated_at);
                            return $dt->format('Y-m-d g:i A');
                        }
                    ],
                    //'user_id',
                    'hat_color',
                    'hat_rule',
                    'visits',
                    'proxy'
                ],
            ]);
            ?>
        </div>
        <div class="col-lg-7 col-md-7">
            <?php
            echo DetailView::widget([
                'model' => $model,
                'attributes' => [
                    'city',
                    'region',
                    'country',
                    'asn',
                    'organization',
                    'latitude',
                    'longitude',
            ]]);
            ?>
        </div>
    </div>
    <?php
    echo Tabs::widget([
        'items' => [
            [
                'label' => 'Visits',
                'content' => $this->render('_visitsGrid', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
                ]),
                'active' => true
            ],
            [
                'label' => 'Map',
                'content' => $this->render('_map', ['model' => $model]),
            ],
        ],
    ]);
    ?>

</div>
