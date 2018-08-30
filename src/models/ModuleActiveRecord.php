<?php

/**
 * @author John Snook
 * @date Aug 26, 2018
 * @license https://snooky.biz/site/license
 * @copyright 2018 John Snook Consulting
 * Description of ModuleActiveRecord
 */

namespace johnsnook\visitors\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;

/**
 * A superclass for Visitor et al
 *
 * @property-read \johnsnook\visitors\Module $module This extensions module
 */
class ModuleActiveRecord extends \yii\db\ActiveRecord {

    /** johnsnook\visitors\Module */
    private static $module;

    /**
     * Set up timestamp behavior here
     *
     * @return array
     */
    public function behaviors() {
        return [
            [
                'class' => TimestampBehavior::className(),
                'value' => new Expression('NOW()'),
            ],
        ];
    }

    /**
     * Converts db timestamp to date object and format into nicer version
     *
     * @return string a formatted big endian DateTime
     */
    public function getCreatedAt() {
        $dt = new \DateTime($this->created_at);
        return $dt->format('Y-m-d g:i A');
    }

    /**
     * This saves me having to remember how to get the damn thing
     *
     * @return \johnsnook\visitors\Module
     */
    public static function getModule() {
        if (empty(self::$module)) {
            return self::$module = Yii::$app->getModule(Yii::$app->controller->module->id);
        } else {
            return self::$module;
        }
    }

}
