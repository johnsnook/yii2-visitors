<?php

/**
 * @author John Snook
 * @date 2018-06-28
 * @license https://github.com/johnsnook/yii2-ip-filter/LICENSE
 * @copyright 2018 John Snook Consulting
 */

namespace johnsnook\ipFilter\models;

use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;

/**
 * This is the model class for table "visitor".
 *
 * @property string $ip
 * @property boolean $is_blacklisted
 * @property string $created_at
 * @property string $updated_at
 * @property integer $user_id
 * @property string $name
 * @property string $message
 * @property string $city
 * @property string $region
 * @property string $country
 * @property double $latitude
 * @property double $longitude
 * @property string $organization
 * @property string $proxy
 *
 */
class Visitor extends ActiveRecord {

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
            //['ip', 'ip', 'ipv6' => false], // IPv4 address (IPv6 is disabled)
            [['name', 'message', 'ip', 'city', 'region', 'organization', 'proxy'], 'string'],
            [['is_blacklisted'], 'boolean'],
            [['created_at', 'updated_at'], 'safe'],
            [['latitude', 'longitude'], 'double'],
            [['user_id'], 'integer'],
        ];
    }

    /**
     * To reduce load on the database by performing a count(*) of visitor_log for
     * every visitor, we increment the count field whenever a new visit is logged
     *
     * @param string $ip
     */
    public static function incrementCount($ip) {
        $sql = "UPDATE visitor SET visits = visits + 1, updated_at = now() WHERE ip = '$ip'";
        $command = \Yii::$app->db->createCommand($sql);
        $command->execute();
    }

    /**
     * Format the postgresql time stamp into nicer version
     *
     * @return string a formatted big endian DateTime
     */
    public function getCreatedAt() {
        $dt = new \DateTime($this->created_at);
        return $dt->format('Y-m-d g:i A');
    }

    /**
     * Format the postgresql time stamp into nicer version
     *
     * @return string a formatted big endian DateTime
     */
    public function getUpdatedAt() {
        $dt = new \DateTime($this->updated_at);
        return $dt->format('Y-m-d g:i A');
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'ip' => 'Ip Address',
            'is_blacklisted' => 'Blacklisted?',
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

}
