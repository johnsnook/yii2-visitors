<?php

/* @var $model frontend\models\Visitor */

/**
 * Uses mapquest/osm static map
 * @see https://developer.mapquest.com/documentation/static-map-api/v4/map/get/
 * //https://open.mapquestapi.com/staticmap/v4/getmap?key=KEY&size=600,400&zoom=13&center=47.6062,-122.3321
 */
use yii\helpers\Html;

$mapKey = Yii::$app->getModule('ipFilter')->mapquestKey;

$mapUrl = "https://open.mapquestapi.com/staticmap/v4/getmap?key=$mapKey";
$mapUrl .= "&size=1000,500&zoom=16&center={$model->latitude},{$model->longitude}";
$mapUrl .= "&mcenter={$model->latitude},{$model->longitude},0,0";
$mapUrl .= "&type=map&imagetype=png&scalebar=false";
$googUrl = "https://www.google.com/maps/place/{$model->latitude},{$model->longitude}";
$location = "$model->city, $model->region, $model->country Map";
$staticMap = Html::img($mapUrl, ['width' => '1000', 'alt' => $location]);
echo Html::a($staticMap, $googUrl, ['target' => "_blank"]);
?>
    