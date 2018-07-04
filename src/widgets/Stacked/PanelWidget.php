<?php

namespace johnsnook\ipFilter\widgets\Stacked;

/**
 * @author John Snook
 * @date 2018-07-2
 * @license https://github.com/johnsnook/yii2-ip-filter/LICENSE
 * @copyright 2018 John Snook Consulting
 */
use yii\bootstrap\Html;

class PanelWidget extends \yii\bootstrap\Widget {

    /** properties * */
    public $containerOptions;
    public $title;
    public $titleOptions;
    public $useHeading = true;
    public $headingOptions;
    public $body;
    public $bodyOptions;
    public $footer;
    public $footerOptions;

    /** methods * */
    public function init() {
        parent::init();
        if (empty($this->containerOptions)) {
            Html::addCssClass($this->containerOptions, 'panel-default');
        }
        Html::addCssClass($this->containerOptions, 'panel');
        Html::addCssClass($this->headingOptions, 'panel-heading');
        Html::addCssClass($this->titleOptions, 'panel-title');
        Html::addCssClass($this->bodyOptions, 'panel-body');
        Html::addCssClass($this->footerOptions, 'panel-footer');
        /** start capturing output buffer */
        ob_start();
    }

    public function run() {
        $out = Html::beginTag('div', $this->containerOptions);
        if (isset($this->title)) {
            if ($this->useHeading) {
                $out .= Html::beginTag('div', $this->headingOptions);
            }
            $out .= Html::beginTag('div', $this->titleOptions);
            $out .= $this->title;
            $out .= Html::endTag('div');
            if ($this->useHeading) {
                $out .= Html::endTag('div');
            }
        }
        /** using both body and the buffer, our widget can be used both ways  */
        $out .= $this->body;
        $out .= ob_get_clean();

        if (isset($this->footer)) {
            $out .= Html::beginTag('div', $this->footerOptions);
            $out .= $this->footer;
            $out .= Html::endTag('div');
        }
        $out .= Html::endTag('div');
        return $out;
    }

}
