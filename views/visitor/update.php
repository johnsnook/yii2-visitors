<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model \johnsnook\ipFilter\models\Visitor */
/* @var $form yii\widgets\ActiveForm */

$this->title = 'Update Visitor: ' . empty($model->name) ? $model->ip : "$model->name - $model->ip ";
$this->params['breadcrumbs'][] = ['label' => 'Visitors', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->ip, 'url' => ['view', 'id' => $model->ip]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="visitor-update">
    <h1><?= Html::encode($this->title) ?></h1>
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

        <div class="form-group">
            <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>

</div>
