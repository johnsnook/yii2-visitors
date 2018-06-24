<?php

namespace johnsnook\ipFilter\models;

use yii\behaviors\TimestampBehavior;
use yii\db\Expression;

/**
 * This is the model class for table "access".
 *
 * @property integer $id
 * @property string $ip
 * @property string $created_at
 * @property string $request
 * @property string $referer
 * @property string $user_agent
 */
class VisitorLog extends \yii\db\ActiveRecord {

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'visitor_log';
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
                'updatedAtAttribute' => null,
                'value' => new Expression('NOW()'),
            ],
        ];
    }

    /**
     * Logs the visitor's request, browser type and referrer
     *
     * @param string $ip The ip address to associate this record with
     */
    public static function log($ip) {
        $log = new VisitorLog([
            'ip' => $ip,
            'request' => filter_input(INPUT_SERVER, 'REQUEST_URI'),
            'referer' => filter_input(INPUT_SERVER, 'HTTP_REFERER'),
            'user_agent' => filter_input(INPUT_SERVER, 'HTTP_USER_AGENT'),
        ]);
        $log->save(false);
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['ip', 'request', 'referer', 'user_agent'], 'string'],
            [['created_at'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'ip' => 'Ip Address',
            'created_at' => 'Created At',
            'request' => 'Request',
            'referer' => 'Referer',
            'user_agent' => 'User Agent',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getVisitor() {
        return $this->hasOne(Visitor::className(), ['ip' => 'ip']);
    }

}
