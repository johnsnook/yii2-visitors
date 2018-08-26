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

//echo "Picture a big old map here.";
//dump($locations);
//die();
$mapData = $searchModel->mapChartData;
echo GoogleChart::widget(array('visualization' => 'Map',
    'packages' => 'map', //default is corechart
    'loadVersion' => 1, //default is 1.  As for Calendar, you need change to 1.1
    'data' => $mapData, //$searchModel->mapChartData,
    'options' => ['title' => 'My Daily Activity',
        'showTip' => true,
    ]
));
//dump($locData);
