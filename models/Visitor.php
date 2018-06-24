<?php

namespace johnsnook\ipFilter\models;

use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;

/**
 * This is the model class for table "visitor".
 *
 * @property string $ip
 * @property string $access_type
 * @property string $created_at
 * @property string $updated_at
 * @property integer $user_id
 * @property string $name
 * @property string $message
 * @property object $info
 * @property float $latitude
 * @property float $longitude
 * @property array $info
 *
 * @property VisitorLog[] $visitorLogs
 */
class Visitor extends ActiveRecord {

    const ACCESS_LIST_NONE = 'None';
    const ACCESS_LIST_BLACK = 'Black';
    const ACCESS_LIST_WHITE = 'White';

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
     * @inheritdoc
     */
    public static function tableName() {
        return 'visitor';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['ip'], 'required'],
            [['access_type', 'name', 'message', 'ip'], 'string'],
            //[['info', 'access_log', 'proxy_check'], 'string'],
            [['created_at', 'updated_at'], 'safe'],
            [['user_id'], 'integer'],
        ];
    }

    public function beforeSave($insert) {
        $this->info = json_encode($this->info);
        $this->access_log = json_encode($this->access_log);
        return true;
    }

    public function afterFind() {
        parent::afterFind();
        $this->info = json_decode($this->info);
    }

    /**
     * Tries to find existing visitor record, and creates a new one if not found
     * Also logs this visit in the access_log
     *
     * @param type $ip
     * @return \johnsnook\ipFilter\models\Visitor
     */
    public static function ringDoorbell($ip) {
        $visitor = self::findOne($ip);
        if (is_null($visitor)) {
            $visitor = new Visitor(['ip' => $ip]);
        }
        return $visitor;
    }

    /**
     * Virtual property for $info property
     * @return string
     */
    public function getLatitude() {
        return isset($this->info->loc) ? split(',', $this->info->loc)[0] : null;
    }

    /**
     * Virtual property for $info property
     * @return string
     */
    public function getLongitude() {
        return isset($this->info->loc) ? split(',', $this->info->loc)[1] : null;
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'ip' => 'Ip Address',
            'access_type' => 'Access Type',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'user_id' => 'User ID',
            'name' => 'Name',
            'message' => 'Message',
            'info' => 'Ip Info',
            'hostname' => 'Host Name',
            'city' => 'City',
            'region' => 'Region',
            'country' => 'Country',
            'coordinates' => 'Coordinates',
            'latitude' => 'Latitude',
            'longitude' => 'Longitude',
            'postal' => 'Postal Code',
            'organization' => 'Organization',
            'access_log' => 'Access Log',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getVisitorLogs() {
        return $this->hasMany(VisitorLog::className(), ['ip' => 'ip']);
    }

}
