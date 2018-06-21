<?php

namespace johnsnook\ipFilter\models;

use johnsnook\ipFilter\lib\RemoteAddress;
use Yii;
use yii\db\ActiveRecord;

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
 * @property array $ip_info
 * @property object $ipInfo
 * @property object $proxyCheck
 * @property array $proxy_check
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
            [['ip_address', 'access_type', 'name', 'message'], 'string'],
            [['ip_info', 'access_log', 'proxy_check'], 'array'],
            [['created_at', 'updated_at'], 'safe'],
            [['user_id'], 'integer'],
        ];
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
        }
        $access = $guest->access_log;
        $access[] = [
            'request' => filter_input(INPUT_SERVER, 'REQUEST_URI'),
            'referer' => filter_input(INPUT_SERVER, 'HTTP_REFERER'),
            'user_agent' => filter_input(INPUT_SERVER, 'HTTP_USER_AGENT'),
        ];
        $guest->access_log = $access;
        return $guest;
    }

    /**
     * Virtual property for $ip_info property
     * @return string
     */
    public function getIpInfo() {
        return (object) $this->ip_info;
    }

    /**
     * Virtual property for $ip_info property
     * @return string
     */
    public function getProxyInfo() {
        return (object) $this->proxy_check;
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
