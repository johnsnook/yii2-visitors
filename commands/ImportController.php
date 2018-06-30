<?php

namespace johnsnook\ipFilter\commands;

use yii;
use yii\helpers\Console;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * ImportController - it imports things
 *
 *
 * @author John
 */
class ImportController extends \yii\console\Controller {

    /**
     * @inheritdoc
     */
    public function init() {
        parent::init();
        /** set the user to admin identiy */
//        \Yii::$app->user->setIdentity(\common\models\Person::findOne(1));
        //Console::clearScreen();
    }

    /**
     * @inheritdoc
     */
    public function actionTest() {
        list($w, $h) = Console::getScreenSize();
        Console::moveCursorTo(1, 1);
        for ($i = 1; $i < $h * 100 + 1; $i++) {
            echo str_repeat('╥', $w) . PHP_EOL;
            if (fgetc(STDIN) == 'q') {
                return;
            }
        }
        Console::moveCursorTo(1, (int) $h / 2);
        Console::clearScreenBeforeCursor();
    }

    /**
     * @inheritdoc
     */
    private function showMsg($msg) {
        Console::clearLine();
        echo Console::renderColoredString($msg);
    }

    /**
     * @inheritdoc
     */
    private function showProgress($prog, $tot = null, $label = null) {
        static $Total;

        if (!is_null($tot))
            $Total = $tot;
        Console::saveCursorPosition();
        list($w, $h) = Console::getScreenSize();
        Console::moveCursorTo(1, $h);
        if (!is_null($label) || $prog == 0) {
            Console::startProgress($prog, $Total, $label, $w);
        } else {
            Console::updateProgress($prog, $Total);
        }
        Console::restoreCursorPosition();
    }

    private function exception2arr($e) {
        $out = [];
        $out['message'] = $e->getMessage();
        $out['file'] = $e->getFile();
        $out['line'] = $e->getLine();
        $out['trace'] = $e->getTraceAsString();
        return json_encode($out, 224);
    }

    private function nameAndExt($fileName) {
        return [substr($fileName, 0, strrpos($fileName, '.')), substr($fileName, strrpos($fileName, '.') + 1)];
    }

    private function heads(&$projProgress, &$docsProgress, &$docsRelProgress, $config) {
        Console::saveCursorPosition();
        $screen = Console::getScreenSize();
        Console::moveCursorTo(1, $screen[1] - 5);
        Console::clearLine();
        echo Console::renderColoredString("%W{$config['title']} %n");
        Console::moveCursorTo(1, $screen[1] - 4);
        Console::clearLine();
        echo Console::renderColoredString("%R" . str_repeat('═', $screen[0]) . "%n");
        $projProgress->update($config['projProgress']);
        $docsProgress->update($config['docsProgress']);
        $docsRelProgress->update($config['docsRelProgress']);
        Console::restoreCursorPosition();
    }

}
