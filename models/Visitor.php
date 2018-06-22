<?php

namespace johnsnook\ipFilter\models;

use johnsnook\ipFilter\lib\RemoteAddress;
use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;

/**
 * This is the model class for table "visitor".
 *
 * @property string $ip_address
 * @property string $access_type
 * @property string $created_at
 * @property string $updated_at
 * @property integer $user_id
 * @property string $name
 * @property string $message
 * @property object $ip_info
 * @property object $proxy_check
 * @property float $latitude
 * @property float $longitude
 * @property array $access_log
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
            [['ip_address'], 'required'],
            [['access_type', 'name', 'message', 'ip_address'], 'string'],
            //[['ip_info', 'access_log', 'proxy_check'], 'string'],
            [['created_at', 'updated_at'], 'safe'],
            [['user_id'], 'integer'],
        ];
    }

    public function beforeSave($insert) {
//        if (parent::beforeSave($insert)) {
        $this->ip_info = json_encode($this->ip_info);
        $this->proxy_check = json_encode($this->proxy_check);
        $this->access_log = json_encode($this->access_log);
        return true;
//        }
//        return false;
    }

    public function afterFind() {
        parent::afterFind();
        $this->ip_info = json_decode($this->ip_info);
        $this->proxy_check = json_decode($this->proxy_check);
        $this->access_log = json_decode($this->access_log, true);
    }

    /**
     * Tries to find existing visitor record, and creates a new one if not found
     * Also logs this visit in the access_log
     *
     * @param type $ip_address
     * @return \johnsnook\ipFilter\models\Visitor
     */
    public static function ringDoorbell($ip_address) {
        $guest = self::findOne($ip_address);
        if (is_null($guest)) {
            $guest = new Visitor(['ip_address' => $ip_address]);
            $guest->access_log = [];
        }
        $dt = new \DateTime();
        $access = $guest->access_log;
        $access[] = [
            'timestamp' => $dt->format('Y-m-d H:i:s'),
            'request' => filter_input(INPUT_SERVER, 'REQUEST_URI'),
            'referer' => filter_input(INPUT_SERVER, 'HTTP_REFERER'),
            'user_agent' => filter_input(INPUT_SERVER, 'HTTP_USER_AGENT'),
        ];
        $guest->access_log = $access;
        return $guest;
    }

    /**
     * Virtual property for $info property
     * @return string
     */
    public function getLatitude() {
        return isset($this->info['loc']) ? split(',', $this->info['loc'])[0] : null;
    }

    /**
     * Virtual property for $info property
     * @return string
     */
    public function getLongitude() {
        return isset($this->info['loc']) ? split(',', $this->info['loc'])[1] : null;
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'ip_address' => 'Ip Address',
            'access_type' => 'Access Type',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'user_id' => 'User ID',
            'name' => 'Name',
            'message' => 'Message',
            'ip_info' => 'Ip Info',
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

}
