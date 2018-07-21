<?php

/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace johnsnook\ipFilter\assets;

use yii\web\AssetBundle;
use yii\web\View;
use Yii;

/**
 *         $ipFilter = Yii::$app->getModule(Yii::$app->controller->module->id);

 */
class VisitorAsset4 extends AssetBundle {

    public $sourcePath = __DIR__;
    public $css = [
            // 'css/site.css',
    ];
    public $js = [
        'js/visitorSearch.js',
        'js/stax.js',
    ];
    public $publishOptions = ['forceCopy' => YII_ENV_DEV ? true : false];
    public $jsOptions = ['position' => View::POS_END];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\web\JqueryAsset',
        'yii\bootstrap4\BootstrapAsset'
    ];

    public function init() {
        parent::init();
    }

}
