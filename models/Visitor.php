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
 * @property string $city
 * @property string $region
 * @property string $country
 * @property double $latitude
 * @property double $longitude
 * @property string $organization
 * @property string $proxy
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
            [['access_type', 'name', 'message', 'ip', 'city', 'region', 'organization', 'proxy'], 'string'],
            [['created_at', 'updated_at'], 'safe'],
            [['latitude', 'longitude'], 'double'],
            [['user_id'], 'integer'],
        ];
    }

    public function beforeSave($insert) {
//        if (gettype($this->info) !== 'string') {
//            $this->info = json_encode($this->info);
//        }

        return true;
    }

    public function afterFind() {
        parent::afterFind();
        $this->info = (object) $this->info;
        //$this->info = json_decode($this->info);
    }

    public static function incrementCount($ip) {
        $sql = "UPDATE visitor SET visits = visits + 1, updated_at = now() WHERE ip = '$ip'";
        $command = \Yii::$app->db->createCommand($sql);
        $command->execute();
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'ip' => 'Ip Address',
            'access_type' => 'Access List',
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
