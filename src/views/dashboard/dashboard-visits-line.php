<?php

/**
 * @author John Snook
 * @date Aug 23, 2018
 * @license https://snooky.biz/site/license
 * @copyright 2018 John Snook Consulting
 * Description of visits_map
 */
/* @var $this yii\web\View */
/* @var $searchModel \johnsnook\visitors\models\VisitorSearch */

use johnsnook\googlechart\GoogleChart;
use johnsnook\visitors\models\Visits;
use yii\db\Expression;

$this->registerJsFile('https://www.gstatic.com/charts/loader.js', ['position' => \yii\web\View::POS_END]);

$query = Visits::find()
        ->select([
            new Expression('count(*)'),
            'visitDate' => new Expression('created_at::date'),
            'ip'
        ])
        ->groupBy('visitDate, ip')
        ->orderBy('visitDate');

$sumQuery = new yii\db\Query([
    'select' => ['visitDate', 'count' => new Expression('SUM(count)')],
    'from' => ['visitdates' => $query],
    'groupBy' => ['visitDate'],
    'orderBy' => ['visitDate' => SORT_ASC]
        ]
);

#$data = $query->asArray()->all();
$data = $sumQuery->all();

/** Add column labels */
$googleData = [['Date', 'Visits']];

$i = 1;
foreach ($data as $datum) {
    //$datum = (object) $datum;
    $googleData[] = array_values($datum);
    $googleData[$i++][1] = intval($datum['count']);
}
//dump($googleData);
//die();

echo GoogleChart::widget([
    'visualization' => 'LineChart',
    'packages' => 'corechart', //default is corechart
    'loadVersion' => 'current', //default is 1.  As for Calendar, you need change to 1.1
    'data' => $googleData, //$searchModel->mapChartData,
    'htmlOptions' => ['style' => 'min-width: 100%'],
    'options' => [
        'title' => 'Unique IP visits per day',
        'animation' => ['duration' => 2000, 'easing' => "out", 'startup' => true],
        'enableScrollWheel' => true,
        'height' => 400,
        'explorer' => [
            'axis' => 'horizontal',
            'actions' => ["dragToZoom", "rightClickToReset"],
            'keepInBounds' => 'true',
//            'actions' => ['dragToPan', 'rightClickToReset']
        ],
    //'showTip' => true,
//'useMapTypeControl' => true,
    ]]
);

function fillIn($in) {
    $out = []; //Our new array
    reset($in); //Sets array position to start
    $key = key($in); //Grabs the key
    $begin = new DateTime($in[0][0]); //Sets the begin date for period to $begin
    $end = new DateTime($in[count($in) - 1][0]); //Sets the begin date for period to $begin

    $interval = new DateInterval('P1D'); //Increases by one day (interval)
    $daterange = new DatePeriod($begin, $interval, $end); //Gets the date range

    $i = 0;
    foreach ($daterange as $date) {
        $date = $date->format("Y-m-d");
        if (isset($in[$date]))
            $out[$date] = $in[$date];
        else
            $out[$date] = 0;
    }
}
