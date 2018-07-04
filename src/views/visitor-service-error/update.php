<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\VisitorServiceError */

$this->title = 'Update Visitor Service Error: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Visitor Service Errors', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="visitor-service-error-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
