<?php

/**
 * @author John Snook
 * @date Aug 22, 2018
 * @license https://snooky.biz/site/license
 * @copyright 2018 John Snook Consulting
 * Description of dashboard
 */
use yii\helpers\Html;
use johnsnook\visitors\assets\VisitorAsset;

$visitorAsset = VisitorAsset::register($this);
$linechartPath = $visitorAsset->baseUrl . '/images/linechart.png';
$mapPath = $visitorAsset->baseUrl . '/images/map.png';



echo Html::a('Visitors', ['/visitors/visitor/index'], ['class' => 'btn btn-success']) . '&nbsp;';
echo Html::a('Visitor Log', ['/visitors/visits/index'], ['class' => 'btn btn-success']) . '&nbsp;';
echo Html::a('Visitors Graph', ['/visitors/dashboard/visits-visitors'], ['class' => 'btn btn-warning']) . '&nbsp;';
echo Html::a('Visitors Map', ['/visitors/dashboard/visitors-map'], ['class' => 'btn btn-warning']) . '&nbsp;';
?>
<style>
    .preview{
        width:100%;
        height:100%;
    }

</style>
<div class="row">
    <div class="panel panel-default col-md-6">
        <div class="panel-heading">Vistor/Visits Chart</div>
        <div class="panel-body">
            <a href="<?= \yii\helpers\Url::toRoute(['/visitors/dashboard/visits-visitors']) ?>">
                <img id="linechart" src="<?= $linechartPath ?>" class="preview" />
            </a>
        </div>
    </div>
    <div class="panel panel-default col-md-6">
        <div class="panel-heading">Vistor ISP Visits Map</div>
        <div class="panel-body">
            <a href="<?= \yii\helpers\Url::toRoute(['/visitors/dashboard/visitors-map']) ?>">
                <img id="map" src="<?= $mapPath ?>" class="preview"/>
            </a>
        </div>

    </div>
</div>