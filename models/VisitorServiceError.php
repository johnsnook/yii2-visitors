<?php

/**
 * This file is part of the Yii2 extension module, yii2-ip-filter
 *
 * @author John Snook
 * @date 2018-06-28
 * @license https://github.com/johnsnook/yii2-ip-filter/LICENSE
 * @copyright 2018 John Snook Consulting
 */

namespace johnsnook\ipFilter\models;

/**
 * This is the model class for table "visitor_service_error".
 *
 * @property integer $id
 * @property string $service
 * @property string $url
 * @property array $params
 * @property string $message
 * @property boolean $is_resolved
 */
class VisitorServiceError extends \yii\db\ActiveRecord {

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'visitor_service_error';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['service', 'url', 'message'], 'required'],
            [['service', 'url', 'params', 'message'], 'string'],
            [['is_resolved'], 'boolean'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'service' => 'Service',
            'url' => 'Url',
            'params' => 'Params',
            'message' => 'Message',
            'is_resolved' => 'Is Resolved',
        ];
    }

}
