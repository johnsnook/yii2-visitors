<?php

namespace johnsnook\visitors\assets;

use yii\web\AssetBundle;
use yii\web\View;

/**
 * Main frontend application asset bundle.
 */
class C3jsAsset extends AssetBundle {

    public $sourcePath = '@bower/c3js-chart';
    public $css = ['c3.min.css'];
    public $js = ['c3.min.js'];
    public $jsOptions = ['position' => View::POS_END];
    public $depends = [
        'johnsnook\visitors\assets\D3Asset',
    ];

}
