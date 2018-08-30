<?php

/**
 * This file is part of the Yii2 extension module, yii2-visitor
 *
 * @author John Snook
 * @date 2018-06-28
 * @license https://github.com/johnsnook/yii2-visitor/LICENSE
 * @copyright 2018 John Snook Consulting
 */
use yii\helpers\Html;
use yii\widgets\DetailView;

//use yii\bootstrap\Tabs;

/* @var $this yii\web\View */
/* @var $model johnsnook\visitors\models\Visitor */

$this->title = empty($model->name) ? $model->ip : $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Visitors', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$visitorModule = $model->module;
$panel = $visitorModule->bootstrapCssVersion === 3 ? 'panel' : 'card';
?>
<div class="visitor-view">
    <div class="row" style="font-size: 36px">
        <div class="col-md-6" ><?= Html::encode($this->title); ?></div>
        <div class="col-md-6" style="text-align: right"><?php
            if (!Yii::$app->user->isGuest) {
                if (!$model->banned) {
                    echo Html::a('Blacklist', ['blacklist', 'id' => $model->ip], ['class' => 'btn btn-danger']);
                }
                echo Html::a('Update', ['update', 'id' => $model->ip], ['class' => 'btn btn-default']);
            }
            ?></div>
    </div>
    <div class="row">
        <div class="col-lg-5 col-md-5">
            <?php
            echo DetailView::widget([
                'model' => $model,
                'attributes' => [
                    'ip',
                    'banned:boolean',
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
                    'name',
                    'message:ntext',
                    'visits',
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
                    'latitude',
                    'longitude',
                    'organization',
                    'proxy'
            ]]);
            ?>
        </div>
    </div>
    <div id="StackAttack" class="stack" style="position: relative">
        <div class="stackable">
            <div class="<?= $panel ?> bg-primary">
                <div class="<?= $panel ?>-body">
                    <h5 class="<?= $panel ?>-title">Map</h5>
                    <?= $this->render('_map', ['model' => $model]) ?>
                </div>
            </div>
        </div>

        <div class="stackable">
            <div class="<?= $panel ?> bg-primary">
                <div class="<?= $panel ?>-body">
                    <h5 class="<?= $panel ?>-title">Visits</h5>
                    <?=
                    $this->render('_visitsGrid', [
                        'searchModel' => $searchModel,
                        'dataProvider' => $dataProvider,
                    ])
                    ?>
                </div>
            </div>
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
                //'headerOptions' => [...],
                'options' => ['id' => 'myveryownID'],
            ],
        ],
    ]);
    ?>

</div>
