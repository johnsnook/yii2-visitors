<?php

/**
 * @author John Snook
 * @date Aug 22, 2018
 * @license https://snooky.biz/site/license
 * @copyright 2018 John Snook Consulting
 * Description of dashboard
 */
use yii\helpers\Html;

echo Html::a('Visitors', ['/visitors/visitor/index'], ['class' => 'btn btn-success']) . '&nbsp;';
echo Html::a('Visitor Log', ['/visitors/visits/index'], ['class' => 'btn btn-success']) . '&nbsp;';
echo Html::a('Visitor Log', ['/visitors/dashboard/line'], ['class' => 'btn btn-warning']) . '&nbsp;';
