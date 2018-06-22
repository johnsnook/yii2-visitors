<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model frontend\models\Visitor */

$this->title = 'Update Visitor: ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Visitors', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->ip_address]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="visitor-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
