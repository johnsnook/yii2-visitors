<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model johnsnook\visitor\models\VisitorLog */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="visitor-log-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'ip')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'created_at')->textInput() ?>

    <?= $form->field($model, 'request')->textInput() ?>

    <?= $form->field($model, 'referer')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'user_agent')->textarea(['rows' => 6]) ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
