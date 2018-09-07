<?php

namespace johnsnook\visitors\assets;

use yii\web\AssetBundle;
use yii\web\View;

/**
 * Main frontend application asset bundle.
 */
class LeafletAsset extends AssetBundle {

    public $sourcePath = '@bower/leaflet/dist';
    public $css = ['leaflet.css'];
    public $js = ['leaflet.js'];
    //public $publishOptions = ['forceCopy' => YII_ENV_DEV ? true : false];
    public $jsOptions = ['position' => View::POS_END];
    public $depends = [
        'yii\web\JqueryAsset',
    ];

}
