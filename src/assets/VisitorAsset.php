<?php

namespace johnsnook\visitor\assets;

use yii\web\AssetBundle;
use yii\web\View;

/**
 * Main frontend application asset bundle.
 */
class VisitorAsset extends AssetBundle {

    public $sourcePath = __DIR__ . '/js';
    public $css = [
    ];
    public $js = [
        'visitorSearch.js'
    ];
    public $publishOptions = ['forceCopy' => YII_ENV_DEV ? true : false];
    public $jsOptions = ['position' => View::POS_END];
    public $depends = [
        'yii\web\JqueryAsset',
    ];

}
