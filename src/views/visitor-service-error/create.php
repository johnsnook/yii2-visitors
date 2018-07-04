<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\VisitorServiceError */

$this->title = 'Create Visitor Service Error';
$this->params['breadcrumbs'][] = ['label' => 'Visitor Service Errors', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="visitor-service-error-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
