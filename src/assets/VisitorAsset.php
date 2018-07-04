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
 * Main application asset bundle.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class VisitorAsset extends AssetBundle {

    public $sourcePath = __DIR__ . '/js';
    //public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'css/site.css',
    ];
    public $js = [
        'visitorSearch.js',
        'stax.js',
    ];
    public $publishOptions = ['forceCopy' => YII_ENV_DEV ? true : false];
    public $jsOptions = ['position' => View::POS_END];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\web\JqueryAsset',
    ];

    public function init() {
        parent::init();
        $ipFilter = Yii::$app->getModule(Yii::$app->controller->module->id);
        if ($ipFilter->bootstrapCssVersion === 3) {
            $this->depends[] = 'yii\bootstrap\BootstrapAsset';
        } else {
            $this->depends[] = 'yii\bootstrap4\BootstrapAsset';
        }
    }

}
