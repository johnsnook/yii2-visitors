<?php

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
    <?= $form->field($model, 'access_type')->dropDownList(['None' => 'None', 'Black' => 'Black', 'White' => 'White',]) ?>
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
