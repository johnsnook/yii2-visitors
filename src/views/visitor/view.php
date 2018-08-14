<?php

/**
 * This file is part of the Yii2 extension module, yii2-visitor
 *
 * @author John Snook
 * @date 2018-06-28
 * @license https://github.com/johnsnook/yii2-visitor/LICENSE
 * @copyright 2018 John Snook Consulting
 */
/* @var $this yii\web\View */
/* @var $model frontend\models\Visitor */

use johnsnook\visitor\widgets\Panel;
use yii\helpers\Html;
use yii\widgets\DetailView;

$visitor = Yii::$app->getModule(Yii::$app->controller->module->id);

$this->title = $model->ip;
$this->params['breadcrumbs'][] = ['label' => 'Visitors', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$visitor = Yii::$app->getModule('visitor');
?>
<div class="container-fluid ">
    <!--    <div class="col-md-10 col-md-offset-2">-->
    <?php
    $lie = '<li class="nav-item">';
    if (!Yii::$app->user->isGuest) {
        if (!$model->banned) {
            echo $lie . Html::a('Blacklist', ['blacklist', 'id' => $model->ip], ['class' => 'mr-sm-2 btn btn-outline-danger text-danger']) . '</li>';
        }
        echo $lie . Html::a('Update', ['update', 'id' => $model->ip], ['class' => 'btn btn-outline-warning text-warning my-2 my-sm-0']) . '</li>';
    }
    ?>
</div>
<div class="d-flex mx-auto card-group " >
    <?php
    /** Visitor Info Panel */
    $template = '<tr><th{captionOptions} style="text-align: right">{label}</th><td{contentOptions}>{value}</td></tr>';

    Panel::begin([
        'containerOptions' => [
            'class' => 'col-lg-4',
        ],
        'useHeader' => false,
    ]);
    echo DetailView::widget([
        'options' => ['class' => 'table table-striped table-sm'],
        'model' => $model,
        'template' => $template,
        'attributes' => [
            'ip',
            //'banned:boolean',
            [
                'attribute' => 'banned',
                'format' => 'text',
                'value' => function($data) {
                    return ($data->banned ? 'Yes' : 'No');
                }
            ],
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
            'hat_color',
            'hat_rule',
            'visits',
        ],
    ]);
    Panel::end();


    /** Map Panel */
    Panel::begin([
        'containerOptions' => [
            'class' => 'col-lg-4',
            'style' => 'overflow:hidden;display: flex;justify-content: center; align-items: center;  ',
        ],
            //'body' => ,
            //'title' => "Map"
    ]);
    echo $this->render('_map', ['model' => $model]);
    Panel::end();


    Panel::begin([
        'containerOptions' => [
            'class' => 'col-lg-4',
        ],
        'useHeader' => false,
    ]);
    echo DetailView::widget([
        'options' => ['class' => 'table table-striped table-sm'],
        'model' => $model,
        'template' => $template,
        'attributes' => [
            'city',
            'region',
            'country',
            'postal',
            'latitude',
            'longitude',
            'asn',
            'organization',
            'proxy'
        ]
    ]);
    Panel::end();
    ?>

</div>
<?php
/** Visitor Log Panel */
echo Panel::widget([
    'containerOptions' => ['class' => 'bg-secondary border-light mx-auto'],
    'titleOptions' => ['class' => 'text-white'],
    'bodyOptions' => ['class' => 'bg-white text-dark'],
    //'title' => "Log",
    'body' => $this->render('_visitsGrid', [
        'searchModel' => $searchModel,
        'dataProvider' => $dataProvider,
    ])
]);
?>
<!--</div>-->
<!--<div class="d-flex mx-auto ">-->


