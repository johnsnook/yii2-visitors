<?php

/**
 * This file is part of the Yii2 extension module, yii2-visitor
 *
 * @author John Snook
 * @date 2018-06-28
 * @license https://github.com/johnsnook/yii2-visitor/LICENSE
 * @copyright 2018 John Snook Consulting
 */
/* @var $model frontend\models\Visitor */

/**
 * Uses mapquest/osm static map
 * @see https://developer.mapquest.com/documentation/static-map-api/v4/map/get/
 * @example https://open.mapquestapi.com/staticmap/v4/getmap?key=KEY&size=600,400&zoom=13&center=47.6062,-122.3321
 */
use yii\helpers\Html;

$visitor = Yii::$app->getModule('visitor');
$mapKey = $visitor->googleMapsApiKey;



if (empty($mapKey)) {
    echo "<p>For this map to work, you must hava Google Maps API key defined in your configuration.</p>";
    echo "<p>Go to <a href=\"https://developers.google.com/maps/documentation/javascript/get-api-key\">their site</a> for a free API key</p>";
} else {
    $mapUrl = "https://maps.googleapis.com/maps/api/staticmap?center={$model->latitude},{$model->longitude}&zoom=13&size=520x340&maptype=roadmap"
            . "&markers=color:green%7C{$model->latitude},{$model->longitude}&key=$mapKey";
    $googUrl = "https://www.google.com/maps/place/{$model->latitude},{$model->longitude}";
    $location = "$model->city, $model->region, $model->country Map";
    $staticMap = Html::img($mapUrl, ['alt' => $location, 'class' => 'card-image ',]);
    echo Html::a($staticMap, $googUrl, ['target' => "_blank"]);
}

