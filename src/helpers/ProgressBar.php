<?php

/**
 * This file is part of the Yii2 extension module, yii2-visitor
 *
 * @author John Snook
 * @date 2018-06-28
 * @license https://github.com/johnsnook/yii2-visitor/LICENSE
 * @copyright 2018 John Snook Consulting
 */

namespace johnsnook\visitors\helpers;

use yii\helpers\Console;

/**
 * ProgressBar improves upon the Yii2 Console progress bar functions with better
 * ETA calculation
 *
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
    private $startTime;
    private $progressEta;
    private $progressEtaLastDone = 0;
    private $progressEtaLastUpdate;

    /**
     * Starts display of a progress bar on screen.
     *
     * This bar will be updated by [[update()]] and my be ended by [[end()]].
     *
     * The following example shows a simple usage of a progress bar:
     *
     * ```php
     * ProgressBar::start(0, 1000);
     * for ($n = 1; $n <= 1000; $n++) {
     *     usleep(1000);
     *     ProgressBar::update($n, 1000);
     * }
     * ProgressBar::end();
     * ```
     *
     * Git clone like progress (showing only status information):
     * ```php
     * ProgressBar::start(1000, false);
     * for ($n = 1; $n <= 1000; $n++) {
     *     usleep(1000);
     *     ProgressBar::update($n, 1000);
     * }
     * ProgressBar::end("progress." . PHP_EOL);
     * ```
     *
     * @param int $total the total value of items that are to be done.
     * @see update
     * @see end
     */
    public function start($total = null) {
        if (!is_null($total)) {
            $this->total = $total;
        }

        $this->startTime = time();
        $this->progressEta = null;
        $this->progressEtaLastDone = 0;
        $this->progressEtaLastUpdate = time();

        $this->update($this->progress);
        $this->eta(0, 0);
    }

    /**
     *
     * @staticvar int $startTime
     * @param int $position
     * @param int $total
     * @return int
     */
    private function eta($position, $total) {
        static $startTime = 0; //TimeType

        if ($position == 0) {
            $startTime = time();
            return; // to avoid a divide-by-zero error
        }

        $elapsedTime = time() - $startTime;
        $estimatedRemaining = ($elapsedTime * ($total / $position)) - $elapsedTime;
        return $estimatedRemaining;
    }

    /**
     * Updates a progress bar that has been started by [[startProgress()]].
     *
     * @param int $progress the number of items that are completed.
     * @see start
     * @see end
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
            $this->progressEta = null;
            $this->progressEtaLastUpdate = time();
        } elseif ($progress < $this->total) {
            // update ETA once per second to avoid flapping
            if (time() - $this->progressEtaLastUpdate > 1 && $progress > $this->progressEtaLastDone) {
                $this->progressEta = $this->eta($progress, $this->total);
            }
        }
        if ($this->progressEta === null) {
            $info .= ' ETA: n/a';
        } else {
            $info .= sprintf(' ETA: %s', gmdate("H:i:s", $this->progressEta));
        }

        if ($this->infoColor !== null) {
            $info = Console::ansiFormat($info, $this->infoColor);
        }

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
    public function end($remove = false, $keepPrefix = true) {
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

        $this->startTime = null;
        $this->width = null;
        $this->label = '';
        $this->progressEta = null;
        $this->progressEtaLastDone = 0;
        $this->progressEtaLastUpdate = null;
    }

}
