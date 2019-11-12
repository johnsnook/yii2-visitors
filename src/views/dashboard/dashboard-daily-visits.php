<?php

/**
 * @author John Snook
 * @date Aug 23, 2018
 * @license https://snooky.biz/site/license
 * @copyright 2018 John Snook Consulting
 * Description of visits_map
 */
/* @var $this yii\web\View */
/* @var $chartData array */

use johnsnook\visitors\assets\C3jsAsset;

C3jsAsset::register($this);

$this->registerJs(file_get_contents(__dir__ . '/dashboard-daily-visits.js'));
?>
<style>
    #hourlyChart{
        margin-top:20px;
    }
    #dailyChart{
        margin-bottom:20px;
    }
</style>
<div id="dailyChart"></div>
<h5 style="width:100%; text-align: center">Click a date on the graph to see the details for that day.</h5>
<div id="hourlyChart"></div>