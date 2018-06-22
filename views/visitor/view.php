<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model frontend\models\Visitor */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Visitors', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="visitor-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->ip_address], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->ip_address], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'ip_address',
            'access_type',
            'created_at',
            'updated_at',
            'user_id',
            'name',
            'message:ntext',
            'ip_info',
            'access_log',
            'proxy_check',
        ],
    ]) ?>

</div>
