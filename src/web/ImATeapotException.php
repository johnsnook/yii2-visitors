<?php

namespace johnsnook\visitors\web;

/**
 * @author John Snook
 * @date Aug 21, 2018
 * @license https://snooky.biz/site/license
 * @copyright 2018 John Snook Consulting
 * Thrown as the default blowoff
 */
class ImATeapotException extends \yii\web\HttpException {

    /**
     * Constructor.
     * @param string $message error message
     * @param int $code error code
     * @param \Exception $previous The previous exception used for the exception chaining.
     */
    public function __construct($message = null, $code = 0, \Exception $previous = null) {
        if (empty($message)) {
            $message = "I'm short and stout.  You're like Jordan Peele; you must get out.";
        }
        parent::__construct(418, $message, $code, $previous);
    }

}
