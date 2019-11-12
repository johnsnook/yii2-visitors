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

$mapData = $searchModel->mapChartData;

echo GoogleChart::widget(array('visualization' => 'Map',
    'packages' => 'map', //default is corechart
    'loadVersion' => 'current', //default is 1.  As for Calendar, you need change to 1.1
    'data' => $mapData, //$searchModel->mapChartData,
    'htmlOptions' => ['data-boob' => 'DD', 'style' => 'min-width: 100%'],
    'options' => ['title' => 'My Daily Activity',
        'enableScrollWheel' => true,
        'showTip' => true,
        'useMapTypeControl' => true,
    ]
));
