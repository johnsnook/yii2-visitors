<?php

namespace console\helpers;

use yii\helpers\Console;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ProgressBar
 *
 * @author John
 */
class ProgressBar extends \yii\base\Object {

    public $lineNumber;
    public $total;
    public $progress = 0;
    public $width = null;
    public $label = null;
    public $labelColor = null;
    public $progressColor = null;
    public $infoColor = null;
    private $_startTime;
    private $_progressEta;
    private $_progressEtaLastDone = 0;
    private $_progressEtaLastUpdate;

    public function __construct($config) {
        parent::__construct($config);
    }

    /**
     * Starts display of a progress bar on screen.
     *
     * This bar will be updated by [[update()]] and my be ended by [[endProgress()]].
     *
     * The following example shows a simple usage of a progress bar:
     *
     * ```php
     * Console::startProgress(0, 1000);
     * for ($n = 1; $n <= 1000; $n++) {
     *     usleep(1000);
     *     Console::update($n, 1000);
     * }
     * Console::endProgress();
     * ```
     *
     * Git clone like progress (showing only status information):
     * ```php
     * Console::startProgress(0, 1000, 'Counting objects: ', false);
     * for ($n = 1; $n <= 1000; $n++) {
     *     usleep(1000);
     *     Console::update($n, 1000);
     * }
     * Console::endProgress("progress." . PHP_EOL);
     * ```
     *
     * @param int $progress the number of items that are completed.
     * @param int $this->total the total value of items that are to be done.
     * @param string $prefix an optional string to display before the progress bar.
     * Default to empty string which results in no prefix to be displayed.
     * @param int|bool $width optional width of the progressbar. This can be an integer representing
     * the number of characters to display for the progress bar or a float between 0 and 1 representing the
     * percentage of screen with the progress bar may take. It can also be set to false to disable the
     * bar and only show progress information like percent, number of items and ETA.
     * If not set, the bar will be as wide as the screen. Screen size will be detected using [[getScreenSize()]].
     * @see startProgress
     * @see update
     * @see endProgress
     */
    public function start($total = null) {
        if (!is_null($total))
            $this->total = $total;

        $this->_startTime = time();
        $this->_progressEta = null;
        $this->_progressEtaLastDone = 0;
        $this->_progressEtaLastUpdate = time();

        $this->update($this->progress);
        $this->eta(0, 0);
    }

    private function eta($position, $total) {
        static $startTime; //TimeType 

        if ($position == 0) {
            $startTime = time();
            return; // to avoid a divide-by-zero error
        }

        $elapsedTime = time() - $startTime;
        //$estimatedRemaining = $elapsedTime * $total / $position;
        $estimatedRemaining = ($elapsedTime * ($total / $position)) - $elapsedTime;
        //$estimatedEndTime = time() + $estimatedRemaining;

        return $estimatedRemaining;


        // Print the results here
    }

    /**
     * Updates a progress bar that has been started by [[startProgress()]].
     *
     * @param int $progress the number of items that are completed.
     * @param int $this->total the total value of items that are to be done.
     * @param string $prefix an optional string to display before the progress bar.
     * Defaults to null meaning the prefix specified by [[startProgress()]] will be used.
     * If prefix is specified it will update the prefix that will be used by later calls.
     * @see startProgress
     * @see endProgress
     */
    public function update($progress) {
        $this->progress = $progress;
        $width = $this->width;
        if ($width === false) {
            $width = 0;
        } else {
            $screenSize = Console::getScreenSize(true);
            if ($screenSize === false && $width < 1) {
                $width = 0;
            } elseif ($width === null) {
                $width = $screenSize[0];
            } elseif ($width > 0 && $width < 1) {
                $width = floor($screenSize[0] * $width);
            }
        }
        if ($this->labelColor === null) {
            $prefix = $this->label;
        } else {
            $prefix = Console::ansiFormat($this->label, $this->labelColor);
        }
        $width -= Console::ansiStrlen($prefix);

        $percent = ($this->total == 0) ? 1 : $progress / $this->total;
        $info = sprintf('%d%% (%d/%d)', $percent * 100, $progress, $this->total);

        if ($progress > $this->total || $progress == 0) {
            $this->_progressEta = null;
            $this->_progressEtaLastUpdate = time();
        } elseif ($progress < $this->total) {
            // update ETA once per second to avoid flapping
            if (time() - $this->_progressEtaLastUpdate > 1 && $progress > $this->_progressEtaLastDone) {
                $this->_progressEta = $this->eta($progress, $this->total);
            }
        }
        if ($this->_progressEta === null) {
            $info .= ' ETA: n/a';
        } else {
            $info .= sprintf(' ETA: %s', gmdate("H:i:s", $this->_progressEta));
        }

        if ($this->infoColor !== null)
            $info = Console::ansiFormat($info, $this->infoColor);

        // Number extra characters outputted. These are opening [, closing ], and space before info
        // Since Windows uses \r\n\ for line endings, there's one more in the case
        $extraChars = Console::isRunningOnWindows() ? 4 : 3;
        $width -= $extraChars + Console::ansiStrlen($info);
        // skipping progress bar on very small display or if forced to skip
        Console::saveCursorPosition();
        Console::moveCursorTo(1, $this->lineNumber);

        if ($width < 5) {
            Console::stdout("\r$prefix$info   ");
        } else {
            if ($percent < 0) {
                $percent = 0;
            } elseif ($percent > 1) {
                $percent = 1;
            }
            $bar = floor($percent * $width);
            $status = str_repeat('░', $bar); //█
            if ($bar < $width) {
                $status .= '►';

                $status .= str_repeat(' ', $width - $bar - 1);
            }
            if ($this->progressColor !== null) {
                $status = Console::ansiFormat($status, $this->progressColor);
            }

            Console::stdout("\r$prefix" . "[$status] $info");
        }
        flush();
        Console::restoreCursorPosition();
    }

    /**
     * Ends a progress bar that has been started by [[startProgress()]].
     *
     * @param string|bool $remove This can be `false` to leave the progress bar on screen and just print a newline.
     * If set to `true`, the line of the progress bar will be cleared. This may also be a string to be displayed instead
     * of the progress bar.
     * @param bool $keepPrefix whether to keep the prefix that has been specified for the progressbar when progressbar
     * gets removed. Defaults to true.
     * @see startProgress
     * @see update
     */
    public function endProgress($remove = false, $keepPrefix = true) {
        Console::saveCursorPosition();
        Console::moveCursorTo(1, $this->lineNumber);
        if ($remove === false) {
            Console::stdout(PHP_EOL);
        } else {
            if (Console::streamSupportsAnsiColors(STDOUT)) {
                Console::clearLine();
            }
            Console::stdout("\r" . ($keepPrefix ? $this->label : '') . (is_string($remove) ? $remove : ''));
        }
        flush();
        Console::restoreCursorPosition();

        $this->_startTime = null;
        $this->width = null;
        $this->label = '';
        $this->_progressEta = null;
        $this->_progressEtaLastDone = 0;
        $this->_progressEtaLastUpdate = null;
    }

}
