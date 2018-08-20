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
//                'class' => "form-inline",
                'role' => "form",
                'style' => 'margin-bottom:30px'
            ]
        ]);
?>

<div class="form-group ">
    <!--<label class="control-label" for="visitorsearch-userquery">Filter visitors by</label>-->
    <div class="input-group ">
        <input id="visitorsearch-userquery" name="VisitorSearch[userQuery]"
               type="text" class="form-control"
               value="<?= htmlentities($model->userQuery) ?>"
               placeholder="Enter a search term"
               >
               <?php
               echo Html::beginTag('span', ['class' => 'input-group-btn']);
               echo Html::submitButton('<i class="glyphicon glyphicon-search"></i>', [
                   'class' => 'btn btn-primary',
                   'data-toggle' => "tooltip",
                   'data-title' => "Search"
               ]);
               echo Html::a('<i class="glyphicon glyphicon-leaf"></i>', ['index'], [
                   'class' => 'btn btn-success',
                   'data-toggle' => "tooltip",
                   'data-title' => "Reset search"
               ]);
               echo Html::button('<i class="glyphicon glyphicon-question-sign"></i>', [
                   'class' => 'btn btn-info',
                   'data-toggle' => 'collapse',
                   'data-title' => "Toggle search help",
                   'data-target' => '#help',
                   'autocomplete' => 'off',
                   'aria-expanded' => "false",
                   "aria-controls" => "help"
               ]);
               echo Html::button('<i class="glyphicon glyphicon-th-list"></i>', [
                   'class' => 'btn btn-warning',
                   'data-toggle' => 'collapse',
                   'data-title' => "Toggle Generated SQL",
                   'data-target' => '#sql',
                   'autocomplete' => 'off',
                   'aria-expanded' => "false",
                   "aria-controls" => "sql"
               ]);
               echo Html::endTag('span');
               ?>
    </div>
</div>
<div id="help"  class="panel outline-info collapse" >
    <div class="panel-body">
        <table class="table table-striped table-sm">
            <thead>
                <tr>
                    <th>Operator</th>
                    <th>Type</th>
                    <th>Description</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>-</td>
                    <td>Negation</td>
                    <td>The user query "smart pretty -judgmental" parses to "smart AND pretty AND NOT judgmental"</td>
                </tr>
                <tr>
                    <td>()</td>
                    <td>Sub-query</td>
                    <td>Allows grouping of terms .  The user query "-crazy (smart AND pretty)" parses to "NOT crazy AND (smart AND pretty)"</td>
                </tr>
                <tr>
                    <td>*</td>
                    <td>Wildcard</td>
                    <td>Fuzzy matches. "butt*" matches butt, buttery, buttered etc.</td>
                </tr>
                <tr>
                    <td>_</td>
                    <td>Character wildcard</td>
                    <td>Matches one character.  "boo_" matches boot, book, bool, boon, etc.</td>
                </tr>
                <tr>
                    <td>=</td>
                    <td>Full match</td>
                    <td>Entire fields must be equal to the term.  "=georgia" only matches where one or more fields is exactly equal to the search term.  The search term will NOT be bracketed with %, but wildcards can still be used.</td>
                </tr>
                <tr>
                    <td>""</td>
                    <td>Double quotes</td>
                    <td>Phrase. '"Super fun"' searches for the full phrase, space include.  Wild cards, negation and exact match operators all work within the phrase.</td>
                </tr>
                <tr>
                    <td>''</td>
                    <td>Single quotes</td>
                    <td>Phrase, no wildcards.  The term will not be evaluated for * or _, but will be wrapped in wildcards.  If a % or _ is in the term, it will be escaped.  'P%on*' becomes '%P%on*%'.</td>
                </tr>
                <tr>
                    <td>:</td>
                    <td>Field</td>
                    <td>Specify the field to search.  'name:jo*' will search the name field for 'jo*.' If no field name matches, all fields will be searched for 'name:jo*'</td>
                </tr>
            </tbody>
        </table>

    </div>
</div>
<?php
if (!empty($model->queryError)) {
    echo '<div class="alert alert-danger" role="alert">';
    echo $model->queryError;
    echo '</div>';
} elseif (!empty($model->parselQuery->sql)) {
    echo '<div id="sql"  class="panel outline-info collapse" >';
    echo '<div class="panel-body">';
    dump($model->parselQuery->tokens);
    dump($model->parselQuery->queryParts);
    echo $model->parselQuery->sql;

    echo '</div>';
    echo '</div>';
}
ActiveForm::end();
?>
