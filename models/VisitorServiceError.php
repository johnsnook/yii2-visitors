<?php

namespace johnsnook\ipFilter\models;

use Yii;

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
