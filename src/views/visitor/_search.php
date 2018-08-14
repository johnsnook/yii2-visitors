<?php

/**
 * This file is part of the Yii2 extension module, yii2-visitor
 *
 * @author John Snook
 * @date 2018-06-28
 * @license https://github.com/johnsnook/yii2-visitor/LICENSE
 * @copyright 2018 John Snook Consulting
 */
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $model frontend\models\VisitorSearch */
/* @var $form yii\widgets\ActiveForm */

//$lastErr = johnsnook\parsel\ParselQuery::$lastError;
?>
<?php
$form = ActiveForm::begin([
            'action' => ['index'],
            'method' => 'get',
            'fieldConfig' => ['enableLabel' => false],
            'options' => [
                'class' => "form-inline",
                'role' => "form",
                'style' => 'margin-bottom:30px'
            ]
        ]);
?>
<div class="row">
    <div class="col-sm-offset-1 col-sm-8 text-right" style="padding-right:0px">
        <input id="visitorsearch-userquery" name="VisitorSearch[userQuery]"
               type="text" name="search" class="form-control"
               value="<?= $model->userQuery ?>" style="width:-webkit-fill-available"
               placeholder="Enter a search term"
               >
    </div>
    <div class="col-sm-2" style="padding-left:0px">
        <?php
        echo Html::submitButton('Search', [
            'class' => 'btn btn-default',
            'type' => 'submit'
        ]);
        echo Html::button('<i class="glyphicon glyphicon-question-sign"></i>', [
            'class' => 'btn btn-info',
            'data-toggle' => 'collapse',
            'data-target' => '#help',
            'autocomplete' => 'off',
            'aria-expanded' => "false",
            "aria-controls" => "help"
        ]);
        echo Html::button('<i class="glyphicon glyphicon-th-list"></i>', [
            'class' => 'btn btn-info',
            'data-toggle' => 'collapse',
            'data-target' => '#sql',
            'autocomplete' => 'off',
            'aria-expanded' => "false",
            "aria-controls" => "sql"
        ]);
        ?>
    </div>
    <div class="col-sm-1" >
    </div>

</div>
<div id="help"  class="panel outline-info collapse" >
    <div class="panel-body">
        <b>Conjunctives:</b><br>
        <ul class="list-group">
            <li class="list-group-item">'AND' is the default behavior. "smart pretty" is the same as "smart AND pretty."</li>
            <li class="list-group-item">'OR' allows more results in your query: "smart OR pretty."</li>
        </ul>
        <b>Operators:</b><br>
        <ul class="list-group">
            <li class="list-group-item">Negation: '-'. The user query "smart pretty -judgmental" parses to "smart AND pretty AND NOT judgmental"</li>
            <li class="list-group-item">Sub-query : '()', Allows grouping of terms . The user query "-crazy (smart AND pretty)" parses to "NOT crazy AND (smart AND pretty)".</li>
            <li class="list-group-item">Wildpanel: '*', fuzzy matches. "butt*" matches butt, buttery, buttered etc.</li>
            <li class="list-group-item">Character wildpanel: '_', matches one character. "boo_" matches boot, book, bool, boon, etc.</li>
            <li class="list-group-item">Full match: '=', field match. Entire fields must be equal to the term. "=georgia" only matches where one or more fields is exactly equal to the search term. The search term will NOT be bracketed with %, but wildpanels can still be used.</li>
            <li class="list-group-item">Phrase: double quotes. '"Super fun"' searches for the full phrase, space include. Wild panels, negation and exact match operators all work within the phrase.</li>
            <li class="list-group-item">Phrase, no wildpanels: single quotes. The term will not be evaluated for * or _, but will be wrapped in wildpanels. If a % or _ is in the term, it will be escaped. 'P%on*' becomes '%P%on*%'.</li>
        </ul>

    </div>
</div>
<?php
if (!empty($model->queryError)) {
    echo '<div class="alert alert-danger" role="alert">';
    echo $model->queryError;
    echo '</div>';
} elseif (!empty($model->sql)) {
    echo '<div id="sql"  class="panel outline-info collapse" >';
    echo '<div class="panel-body">';
    echo $model->sql;
    echo '</div>';
    echo '</div>';
}
ActiveForm::end();
?>
