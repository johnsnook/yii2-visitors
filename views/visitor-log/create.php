<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\VisitorLog */

$this->title = 'Create Visitor Log';
$this->params['breadcrumbs'][] = ['label' => 'Visitor Logs', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="visitor-log-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
