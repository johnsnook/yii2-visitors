<?php

/**
 * This file is part of the Yii2 extension module, yii2-visitor
 *
 * @author John Snook
 * @date 2018-06-28
 * @license https://github.com/johnsnook/yii2-visitor/LICENSE
 * @copyright 2018 John Snook Consulting
 */

namespace johnsnook\visitors\models;

/**
 * This is the model class for table "country".
 *
 * @property string $code
 * @property string $name
 */
class Country extends \yii\db\ActiveRecord {

    /**
     * The name of the db table
     *
     * @return string
     */
    public static function tableName() {
        return 'country';
    }

    /**
     * Validation rules
     *
     * @return array
     */
    public function rules() {
        return [
            [['code', 'name'], 'required'],
            [['name'], 'string'],
            [['code'], 'string', 'max' => 2],
            [['code'], 'unique'],
        ];
    }

}
