<?php

use yii\web\AssetBundle;

/**
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
class StackedAsset extends AssetBundle {

    public $sourcePath = __DIR__ . "/assets";
    public $js = [
        'js/jquery.stacked.js'
    ];
    public $depends = [
        'yii\web\JqueryAsset'
    ];

    public function init() {
        // Tell AssetBundle where the assets files are
        $this->sourcePath = __DIR__ . "/assets";
        parent::init();
    }

}
