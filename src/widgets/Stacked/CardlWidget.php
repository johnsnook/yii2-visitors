<?php

namespace johnsnook\ipFilter\widgets\Stacked;

/**
 * @author John Snook
 * @date 2018-07-2
 * @license https://github.com/johnsnook/yii2-ip-filter/LICENSE
 * @copyright 2018 John Snook Consulting
 */
use yii\bootstrap4\Html;

/**
 * For use with bootstrap 4.  Renders a card. Widget can be used either way
 * @example
 * ```
 * <?php
 *     echo CardlWidget::widget([
 *        'headerOptions' => ['class' => 'bg-secondary'],
 *        'title' => 'Top card',
 *        'body' => $aBunchOfHtml,
 *     ]);
 *
 *     echo CardlWidget::begin([
 *        'containerOptions' => ['class' => 'border border-secondary'],
 *        'headerOptions' => ['class' => 'bg-dark text-white'],
 *        'title' => 'Top card',
 *     ]);
 * ?>
 * <p class="card-text">
 * <?= echo CardlWidget::end();
 */
class CardlWidget extends \yii\bootstrap\Widget {

    /** properties * */
    public $containerOptions;
    public $title;
    public $titleOptions;
    public $useHeader = true;
    public $headerOptions;
    public $body = '';
    public $bodyOptions;
    public $footer;
    public $footerOptions;

    /** methods * */
    public function init() {
        parent::init();
        if (empty($this->containerOptions)) {
            Html::addCssClass($this->containerOptions, 'card-default');
        }
        Html::addCssClass($this->containerOptions, 'card');
        Html::addCssClass($this->headerOptions, 'card-header');
        Html::addCssClass($this->titleOptions, 'card-title');
        Html::addCssClass($this->bodyOptions, 'card-body');
        Html::addCssClass($this->footerOptions, 'card-footer');
        /** start capturing output buffer */
        ob_start();
    }

    public function run() {
        $out = Html::beginTag('div', $this->containerOptions);
        /** Heading & title, if useHeader set */
        if (!empty($this->title)) {
            if ($this->useHeader) {
                $out .= Html::beginTag('div', $this->headerOptions);
                $out .= Html::beginTag('h5', $this->titleOptions);
                $out .= $this->title;
                $out .= Html::endTag('h5');
                $out .= Html::endTag('div');
            }
        }

        /** Body with optional title */
        $out .= Html::beginTag('div', $this->bodyOptions);
        if (!$this->useHeader) {
            $out .= Html::beginTag('h5', $this->titleOptions);
            $out .= $this->title;
            $out .= Html::endTag('h5');
        }
        /** using both body and the buffer, our widget can be used both ways  */
        $out .= $this->body;
        $out .= ob_get_clean();
        $out .= Html::endTag('div');

        /** footer */
        if (isset($this->footer)) {
            $out .= Html::beginTag('div', $this->footerOptions);
            $out .= $this->footer;
            $out .= Html::endTag('div');
        }
        $out .= Html::endTag('div');
        return $out;
    }

}
