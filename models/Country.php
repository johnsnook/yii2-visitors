<?php

namespace johnsnook\ipFilter\models;

use Yii;

/**
 * This is the model class for table "country".
 *
 * @property string $code
 * @property string $name
 */
class Country extends \yii\db\ActiveRecord {

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'country';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['code', 'name'], 'required'],
            [['name'], 'string'],
            [['code'], 'string', 'max' => 2],
            [['code'], 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'code' => 'Code',
            'name' => 'Name',
        ];
    }

}
