<?php

/**
 * @author John Snook
 * @date Aug 23, 2018
 * @license https://snooky.biz/site/license
 * @copyright 2018 John Snook Consulting
 * Description of visits_map
 */
/* @var $this yii\web\View */
/* @var $locations yii\db\ActiveQuery */

use scotthuangzl\googlechart\GoogleChart;

//echo "Picture a big old map here.";
//dump($locations);
//die();
$locData = $locations->asArray()->all();
array_unshift($locData, ['Latitude', 'Longitude', 'Organization']);
$out = array_map(function (array $arr) {
    return array_values($arr);
}, $locData);
//dump($locData);

echo GoogleChart::widget(array('visualization' => 'Map',
    'packages' => 'map', //default is corechart
    'loadVersion' => 1, //default is 1.  As for Calendar, you need change to 1.1
    'data' => $locData,
    'options' => ['title' => 'My Daily Activity',
        'showTip' => true,
    ]
));
//dump($locData);
