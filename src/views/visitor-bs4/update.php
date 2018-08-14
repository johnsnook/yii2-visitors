<?php

/**
 * This file is part of the Yii2 extension module, yii2-visitor
 *
 * @author John Snook
 * @date 2018-06-28
 * @license https://github.com/johnsnook/yii2-visitor/LICENSE
 * @copyright 2018 John Snook Consulting
 */
#use frontend\assets\AppAsset;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model \johnsnook\visitor\models\Visitor */
/* @var $form yii\widgets\ActiveForm */

$this->title = 'Update Visitor: ' . empty($model->name) ? $model->ip : "$model->name - $model->ip ";
$this->params['breadcrumbs'][] = ['label' => 'Visitors', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->ip, 'url' => ['view', 'id' => $model->ip]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="visitor-update offset-2 col-8">
    <h1><?= Html::encode($this->title) ?></h1>
    <div class="visitor-form">
        <?php $form = ActiveForm::begin(); ?>
        <?= $form->field($model, 'name')->textInput() ?>
        <?= $form->field($model, 'message')->textarea(['rows' => 6]) ?>
        <?= $form->field($model, 'banned')->checkbox() ?>
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
