<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\VisitorLog */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="visitor-log-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'ip')->textInput() ?>

    <div class="form-group field-visitor-created_at">
        <label class="control-label" for="visitor-created_at">Creation</label>
        <div type="text" id="visitor-created_at" class="form-control" name="Visitor[created_at]">
            <?= $model->created_at->format('Y-m-d g:i A') ?>
        </div>
    </div>
    <?= $form->field($model, 'request')->textInput() ?>

    <?= $form->field($model, 'referer')->textInput() ?>

    <?= $form->field($model, 'user_agent')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
