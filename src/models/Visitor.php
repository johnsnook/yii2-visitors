<?php

/**
 * This file is part of the Yii2 extension module, yii2-visitor
 *
 * @author John Snook
 * @date 2018-06-28
 * @license https://github.com/johnsnook/yii2-visitor/LICENSE
 * @copyright 2018 John Snook Consulting
 */

namespace johnsnook\visitor\models;

use yii\behaviors\TimestampBehavior;
use yii\db\Expression;
use johnsnook\visitor\models\Country;

/**
 * This is the model class for table "visitor".
 *
 * @property string $ip
 * @property boolean $banned
 * @property string $created_at
 * @property string $updated_at
 * @property integer $user_id
 * @property string $city
 * @property string $region
 * @property string $country
 * @property string $postal
 * @property double $latitude
 * @property double $longitude
 * @property string $asn
 * @property string $organization
 * @property string $proxy
 * @property string $hat_color
 * @property string $hat_rule
 *
 */
class Visitor extends \yii\db\ActiveRecord {

    const HAT_COLOR_WHITE = 'White';
    const HAT_COLOR_NONE = 'None';
    const HAT_COLOR_BLACK = 'Black';

    /**
     * @var array The replacements template
     */
    const REPLACEMENTS_TEMPLATE = ['{ip_address}', '{key}'];

    /**
     * @var string The template for the proxy check API.
     */
    const TEMPLATE_PROXY_CHECK_URL = 'http://proxycheck.io/v2/{ip_address}&key={key}&vpn=1&inf=0';

    /**
     * @var string The template for the ip info API.
     */
    const TEMPLATE_IP_INFO_URL = 'http://ipinfo.io/{ip_address}?token={key}';

    /**
     * @var string $ipInfoKey Go to https://ipinfo.io/signup for a free API key
     */
    public $ipInfoKey = '';

    /**
     * @var string $proxyCheckKey Go to https://proxycheck.io/ for a free API key
     */
    public $proxyCheckKey = '';

    /**
     * @var Visits The log record of this visit
     */
    public $visit;

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
     * The table name
     */
    public static function tableName() {
        return 'visitor';
    }

    /**
     * Validation rules
     *
     * @return array
     */
    public function rules() {
        return [
            [['ip'], 'required'],
            //['ip', 'ip', 'ipv6' => false], // IPv4 address (IPv6 is disabled)
            [['ip', 'city', 'region', 'asn', 'organization', 'proxy', 'hat_color', 'hat_rule'], 'string'],
            [['banned'], 'boolean'],
            [['created_at', 'updated_at'], 'safe'],
            [['latitude', 'longitude'], 'double'],
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
     * Labels of the attributes
     */
    public function attributeLabels() {
        return [
            'ip' => 'Ip Address',
            'banned' => 'Banned?',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'user_id' => 'User ID',
            'hat_color' => 'Hat Color',
            'hat_rule' => 'Hat Color Reason',
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
     * {@inheritdoc}
     *
     * Retrieves available information about the IP address if the record is
     * being inserted.
     *
     * @param type $insert
     * @return boolean
     */
    public function beforeSave($insert) {
        if (parent::beforeSave($insert)) {
            if ($insert) {
                if (($info = $this->getIpInfo($this->ip)) !== null) {
                    $this->city = $info->city;
                    $this->region = $info->region;
                    $country = Country::findOne(['code' => $info->country]);
                    $this->country = $country->name;

                    if ($info->loc) {
                        $this->latitude = floatval(explode(',', $info->loc)[0]);
                        $this->longitude = floatval(explode(',', $info->loc)[1]);
                    }
                    $organs = explode(' ', $info->org);
                    $this->asn = $organs[0];
                    $this->organization = implode(' ', array_slice($organs, 1));
                }
            }
            if (is_null($this->proxy) || $this->proxy === 'ERROR') {
                $this->proxy = self::proxyCheck($this->ip);
//                $this->banned = $this->isBanned(true);
            }
            return true;
        }
        return false;
    }

    /**
     * Is this visitor blacklisted from a previous check or are they blacklisted by
     * applying the rules now.
     *
     * @param boolean $forceCheck
     * @return boolean
     */
    public function isBanned($forceCheck = false) {
        if ($forceCheck === false) {
            return $this->banned;
        }
        return ($this->hat_color === static::HAT_COLOR_BLACK);
    }

    /**
     * Request ip information from ipinfo.io which looks like
     * <code>
     *    {
     *        "hostname": "c-24-99-237-149.hsd1.ga.comcast.net",
     *        "city": "Decatur",
     *        "region": "Georgia",
     *        "country": "US",
     *        "loc": "33.8110,-84.2869",
     *        "postal": 30033,
     *        "org": "AS7922 Comcast Cable Communications, LLC"
     *    }
     * </code>
     *
     * @return object|null
     */
    public function getIpInfo() {
        $visitor = \Yii::$app->getModule('visitor');

        $url = str_replace(self::REPLACEMENTS_TEMPLATE, [$this->ip, $visitor->ipInfoKey], self::TEMPLATE_IP_INFO_URL);
        try {
            if (!empty($data = json_decode(file_get_contents($url)))) {
                return $data;
            }
        } catch (\yii\base\Exception $e) {
            return null;
        }
    }

    /**
     * Requests proxy information from proxycheck.io
     * <code>
     *     {
     *         "status": "ok",
     *         "185.220.101.34": {
     *             "proxy": "yes",
     *             "type": "TOR"
     *         }
     *     }
     * </code>
     *
     * @param string $ip The IP address of the current visitor
     * @return object|null
     */
    public static function proxyCheck($ip) {
        $visitor = \Yii::$app->getModule('visitor');
        $proxy = null;
        $url = str_replace(self::REPLACEMENTS_TEMPLATE, [$ip, $visitor->proxyCheckKey], self::TEMPLATE_PROXY_CHECK_URL);

        try {
            if (!empty($data = json_decode(file_get_contents($url), true))) {
                $pcheck = (object) $data[$ip];
                $proxy = ($pcheck->proxy === 'yes' ? $pcheck->type : 'no');
            }
        } catch (\yii\base\Exception $e) {
            VisitorServiceError::log("Proxy Check", $url, $e->getMessage());
            $proxy = 'ERROR';
        }

        return $proxy;
    }

    private function getProxyInfo() {
        $visitor = \Yii::$app->getModule('visitor');
        $url = str_replace(self::REPLACEMENTS_TEMPLATE, [$this->ip, $visitor->proxyCheckKey], self::TEMPLATE_PROXY_CHECK_URL);
        try {
            if (!empty($data = json_decode(file_get_contents($url), true))) {
                return (object) $data[$this->ip];
            }
        } catch (\yii\base\Exception $e) {
            return null;
        }
    }

    public function getLoggedVisits() {
        return $this->hasMany(Visits::className(), ['ip' => 'ip']);
    }

}
