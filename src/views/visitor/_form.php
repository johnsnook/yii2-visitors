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
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model \johnsnook\ipFilter\models\Visitor */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="visitor-form">

    <?php $form = ActiveForm::begin(); ?>
    <?= $form->field($model, 'name')->textInput() ?>
    <?= $form->field($model, 'message')->textarea(['rows' => 6]) ?>
    <?= $form->field($model, 'is_blacklisted')->checkbox() ?>
    <div class="form-group field-visitor-created_at">
        <label class="control-label" for="visitor-created_at">Creation</label>
        <div type="text" id="visitor-created_at" class="form-control" name="Visitor[created_at]">
            <?= $model->createdAt ?>
        </div>
    </div>
    <div class="form-group field-visitor-updated_at">
        <label class="control-label" for="visitor-updated_at">Modified</label>
        <div type="text" id="visitor-updated_at" class="form-control" name="Visitor[updated_at]">
            <?= $model->updatedAt ?>
        </div>
    </div>
    <?php //echo  $form->field($model, 'created_at')->textInput(); ?>
    <?php //echo  $form->field($model, 'updated_at')->textInput(); ?>
    <?php //echo $form->field($model, 'user_id')->textInput(); ?>


    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
