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

    /**
     * @inheritdoc
     *
     * We override the AR to this module's db to allow the developer to have a
     * separate database for this module.
     *
     * @return \yii\db\Connection 
     */
    public static function getDb() {
        return self::getModule()->getDb();
    }

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
        return \johnsnook\visitors\Module::getInstance();
    }

}
