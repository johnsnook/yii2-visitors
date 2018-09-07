<?php

namespace johnsnook\visitors\assets;

use yii\web\AssetBundle;
use yii\web\View;

/**
 * Main frontend application asset bundle.
 */
class D3Asset extends AssetBundle {

    public $sourcePath = '@bower/d3';
    public $js = ['d3.min.js'];
    //public $publishOptions = ['forceCopy' => YII_ENV_DEV ? true : false];
    public $jsOptions = ['position' => View::POS_END];

}
