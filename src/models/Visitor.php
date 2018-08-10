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
use johnsnook\ipFilter\models\Country;
use johnsnook\ipFilter\helpers\IpHelper;

/**
 * This is the model class for table "visitor".
 *
 * @property string $ip
 * @property boolean $is_blacklisted
 * @property string $blacklist_reason;
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

    const BL_NULL = NULL;
    const NOT_BLACKLISTED = '';
    CONST BL_PROXY = 'Proxy';
    CONST BL_AUTOBAN = 'Autoban';
    CONST BL_MANUAL = 'Manual';

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
     * @var VisitorLog The log record of this visit
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
            [['name', 'message', 'ip', 'city', 'region', 'organization', 'proxy', 'blacklist_reason'], 'string'],
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
            'is_blacklisted' => 'Banned?',
            'blacklist_reason' => 'Ban Reason',
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
                    $this->organization = $info->org;
                }
            }
            if (is_null($this->proxy) || $this->proxy === 'ERROR') {
                $this->proxy = self::proxyCheck($this->ip);
                $this->is_blacklisted = $this->isBlacklisted(true);
            }
            return true;
        }
        return false;
    }

    /**
     * Compares properties against a indexed array of arrays of values.  String
     * values are compared using stripos, ip values should be in CIDR format.
     *
     * @param array $list
     * @return array
     */
    public function checkList($list) {
        if (isset($list['ip'])) {
            foreach ($list['ip'] as $ip) {
                if (IpHelper::inRange($this->ip, $ip)) {
                    return ['ip' => $ip];
                }
            }
        }
        if (isset($list['city'])) {
            foreach ($list['city'] as $city) {
                if (stripos($this->city, $city) !== false) {
                    return ['city' => $city];
                }
            }
        }
        if (isset($list['region'])) {
            foreach ($list['region'] as $region) {
                if (stripos($this->region, $region) !== false) {
                    return ['region' => $region];
                }
            }
        }
        if (isset($list['country'])) {
            foreach ($list['country'] as $country) {
                if (stripos($this->country, $country) !== false) {
                    return ['country' => $country];
                }
            }
        }
        if (isset($list['postal'])) {
            foreach ($list['postal'] as $postal) {
                if ($this->postal === $postal) {
                    return ['postal' => $postal];
                }
            }
        }
        if (isset($list['organization'])) {
            foreach ($list['organization'] as $organization) {
                if (stripos($this->organization, $organization) !== false) {
                    return ['organization' => $organization];
                }
            }
        }
        if (isset($list['referer'])) {
            foreach ($list['referer'] as $referer) {
                if (stripos($this->visit->referer, $referer) !== false) {
                    return ['referer' => $referer];
                }
            }
        }
        return null;
    }

    /**
     * Is this visitor blacklisted from a previous check or are they blacklisted by
     * applying the rules now.
     *
     * @param boolean $forceCheck
     * @return boolean
     */
    public function isBlacklisted($forceCheck = false) {
        if ($forceCheck === false) {
            return $this->is_blacklisted;
        }
        $ipFilter = \Yii::$app->getModule('ipFilter');
        $return = false;
        if ($this->proxy !== 'no' && in_array($this->proxy, $ipFilter->proxyBan, true)) {
            $this->blacklist_reason = self::BL_PROXY;
            return true;
        }

        foreach ($ipFilter->autoBan as $ban) {
            if (IpHelper::inRange($this->ip, $ban)) {
                $this->blacklist_reason = self::BL_AUTOBAN;
                $return = true;
                break;
            }
        }
        return $return;
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
        $ipFilter = \Yii::$app->getModule('ipFilter');

        $url = str_replace(self::REPLACEMENTS_TEMPLATE, [$this->ip, $ipFilter->ipInfoKey], self::TEMPLATE_IP_INFO_URL);
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
        $ipFilter = \Yii::$app->getModule('ipFilter');
        $proxy = null;
        $url = str_replace(self::REPLACEMENTS_TEMPLATE, [$ip, $ipFilter->proxyCheckKey], self::TEMPLATE_PROXY_CHECK_URL);

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
        $ipFilter = \Yii::$app->getModule('ipFilter');
        $url = str_replace(self::REPLACEMENTS_TEMPLATE, [$this->ip, $ipFilter->proxyCheckKey], self::TEMPLATE_PROXY_CHECK_URL);
        try {
            if (!empty($data = json_decode(file_get_contents($url), true))) {
                return (object) $data[$this->ip];
            }
        } catch (\yii\base\Exception $e) {
            return null;
        }
    }

    public function getLoggedVisits() {
        return $this->hasMany(VisitorLog::className(), ['ip' => 'ip']);
    }

}
