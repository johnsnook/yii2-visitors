<?php

namespace johnsnook\ipFilter\widgets\Stacked;

/**
 * @author John Snook
 * @date 2018-07-2
 * @license https://github.com/johnsnook/yii2-ip-filter/LICENSE
 * @copyright 2018 John Snook Consulting
 */
use yii\bootstrap4\Html;
use StackedAsset;

/**
 * Stacked jquery plugin wrapper
 */
class CardlWidget extends \yii\bootstrap\Widget {

    /** properties * */
    public $containerOptions;

    /** methods * */
    public function init() {
        parent::init();
        if (empty($this->containerOptions)) {
            Html::addCssClass($this->containerOptions, 'card-default');
        }
        Html::addCssClass($this->containerOptions, 'card stacked');

        StackedAsset::register($this->getView());
        /** start capturing output buffer */
        ob_start();
    }

    public function run() {
        $out = Html::beginTag('div', $this->containerOptions);
        /** using the buffer, our widget can be used with begin/end */
        $out .= ob_get_clean();
        $out .= Html::endTag('div');
        return $out;
    }

}
