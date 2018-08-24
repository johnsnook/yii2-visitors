<?php

/**
 * This file is part of the Yii2 extension module, yii2-visitor
 *
 * @author John Snook
 * @date 2018-06-28
 * @license https://github.com/johnsnook/yii2-visitor/LICENSE
 * @copyright 2018 John Snook Consulting
 */
use kartik\dialog\Dialog;
use kartik\dialog\DialogAsset;
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Dropdown;
use yii\helpers\Inflector;

DialogAsset::register($this);

/* @var $this yii\web\View */
/* @var $searchModel frontend\models\VisitorSearch */
/* @var $form yii\widgets\ActiveForm */

//$lastErr = johnsnook\parsel\ParselQuery::$lastError;

$form = ActiveForm::begin([
            'action' => ['index'],
            'method' => 'get',
            'fieldConfig' => ['enableLabel' => false],
            'options' => [
//                'class' => "form-inline",
                'role' => "form",
                'style' => 'margin-bottom:30px'
            ]
        ]);
$className = \yii\helpers\StringHelper::basename(get_class($searchModel));

$sql = (!empty($searchModel->parselQuery->sql) ? $searchModel->parselQuery->sql : null);
?>

<div class="form-group ">
    <div class="input-group ">
        <input id="userquery" name="<?= $className ?>[userQuery]"
               type="text" class="form-control"
               value="<?= htmlentities($searchModel->userQuery) ?>"
               placeholder="Enter a search term"
               >
               <?php
               echo Html::beginTag('span', ['class' => 'input-group-btn']);
               echo Html::submitButton('<i class="glyphicon glyphicon-search"></i>', [
                   'class' => 'btn btn-primary',
                   'title' => "Execute the search!"
               ]);
               echo Html::a('<i class="glyphicon glyphicon-leaf"></i>', [Yii::$app->requestedAction->id], [
                   'class' => 'btn btn-success',
                   'title' => 'Reset search form'
               ]);
               echo Html::button('<i class="glyphicon glyphicon-ice-lolly-tasted"></i>&nbsp;<span class="caret"></span>', [
                   'class' => 'btn btn-default dropdown-toggle',
                   'data-toggle' => 'dropdown',
                   'title' => 'Insert a data field for searching',
                   'aria-haspopup' => true,
                   'aria-expanded' => false
               ]);
               $bassDrop = [];
               foreach ($searchModel->fields as $field) {
                   $bassDrop[] = [
                       'label' => Inflector::humanize($field),
                       'url' => "javascript:parselField('$field')"
                   ];
               }

               echo Dropdown::widget([
                   'items' => $bassDrop,
                   'options' => ['style' => 'z-index:1002']
               ]);

               echo Html::button('<i class="glyphicon glyphicon-question-sign"></i>', [
                   'class' => 'btn btn-info',
                   'id' => 'btn-help',
                   'title' => "Search help",
               ]);
               if (!empty($sql)) {
                   echo Html::button('<i class="glyphicon glyphicon-th-list"></i>', [
                       'class' => 'btn btn-warning',
                       'id' => 'btn-sql',
                       'title' => "Generated SQL",
                   ]);
               }
               echo Html::endTag('span');
               ?>
    </div>
</div>

<?php
if (!empty($searchModel->queryError)) {
    echo '<div class="alert alert-danger" role="alert">';
    echo $searchModel->queryError;
    echo '</div>';
}
ActiveForm::end();

echo Dialog::widget([
    'libName' => 'helpDialog',
    'overrideYiiConfirm' => false, // prevent this library from overriding yii data-confirm dialog
    'options' => [// customized BootstrapDialog options
        'size' => Dialog::SIZE_NORMAL, // large dialog text
        'type' => Dialog::TYPE_INFO, // bootstrap contextual color
        'title' => 'Search Help',
    ]
]);
if (!empty($sql)) {
    echo Dialog::widget([
        'libName' => 'sqlDialog',
        'overrideYiiConfirm' => false, // prevent this library from overriding yii data-confirm dialog
        'options' => [// customized BootstrapDialog options
            'size' => Dialog::SIZE_LARGE, // large dialog text
            'type' => Dialog::TYPE_WARNING, // bootstrap contextual color
            'title' => 'Generated SQL (for debugging)',
        ]
    ]);
}

$help = file_get_contents(__dir__ . '/searchHelp.txt');

$js = <<< JS
$('#btn-help').on('click', function(){
    helpDialog.alert("$help");
});

$('#btn-sql').on('click', function(){
    sqlDialog.alert($('#sql').html());
});

JS;
$this->registerJs($js);
?>
<div id="sql" class="collapse"><?= $searchModel->parselQuery->sql ?></div>
<script>
    function parselField(field) {
        $('input#userquery')
                .val($('input#userquery').val() + ' ' + field + ':')
                .focus();
    }
</script>