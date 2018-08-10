<?php

/**
 * This file is part of the Yii2 extension module, yii2-ip-filter
 *
 * @author John Snook
 * @date 2018-06-28
 * @license https://github.com/johnsnook/yii2-ip-filter/LICENSE
 * @copyright 2018 John Snook Consulting
 */
use johnsnook\ipFilter\widgets\Stacked\PanelWidget;
use johnsnook\ipFilter\widgets\Stacked\CardlWidget;
use johnsnook\ipFilter\assets\VisitorAsset;
use yii\helpers\Html;
use yii\widgets\DetailView;

$ipFilter = Yii::$app->getModule(Yii::$app->controller->module->id);

//use yii\bootstrap\Tabs;

/* @var $this yii\web\View */
/* @var $model frontend\models\Visitor */
VisitorAsset::register($this);
$this->title = empty($model->name) ? $model->ip : $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Visitors', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
//$ipFilter = Yii::$app->getModule(Yii::$app->controller->module->id);
//$panel = $ipFilter->bootstrapCssVersion === 3 ? 'panel' : 'card';
//$this->params['bootstrapCssVersion'];
?>
<div class="visitor-view">
    <div class="row" style="font-size: 36px">
        <div class="col-md-6 col-6" ><?= Html::encode($this->title); ?></div>
        <div class="col-md-6 col-6" style="text-align: right"><?php
            if (!Yii::$app->user->isGuest) {
                if (!$model->banned) {
                    echo Html::a('Blacklist', ['blacklist', 'id' => $model->ip], ['class' => 'btn btn-danger']);
                }
                echo Html::a('Update', ['update', 'id' => $model->ip], ['class' => 'btn btn-default']);
            }
            ?></div>
    </div>
    <div class="row">
        <div class="col-5 col-md-5">
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
        <div class="col-lg-7 col-7">
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
    <div id="StackAttack" class="stack" style="display: block; position: relative;margin-bottom:200px">
        <?php
        $title = 'Map';
        $body = $this->render('_map', ['model' => $model]);
        if ($ipFilter->bootstrapCssVersion === 3) {
            echo PanelWidget::widget([
                'containerOptions' => ['class' => 'stackItem'],
                'headingOptions' => ['class' => 'bg-primary'],
                'title' => $title,
                'body' => $body,
            ]);
        } else {
            echo CardlWidget::widget([
                'containerOptions' => ['class' => 'stackItem border border-secondary'],
                'useHeader' => false,
                'headerOptions' => ['class' => 'bg-secondary'],
                'title' => 'Top',
                'body' => $body,
            ]);
        }
        $title = 'Visits';
        $body = $this->render('_visitsGrid', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
        if ($ipFilter->bootstrapCssVersion === 3) {
            echo PanelWidget::widget([
                'containerOptions' => ['class' => 'stackItem panel-success'],
                'title' => $title,
                'body' => $body,
            ]);
        } else {
            echo CardlWidget::widget([
                'containerOptions' => ['class' => 'stackItem  mb-3 border border-info'],
                'headerOptions' => ['class' => 'bg-info'],
                'title' => $title,
                'body' => $body,
            ]);
        }
        ?>
    </div>
</div>
