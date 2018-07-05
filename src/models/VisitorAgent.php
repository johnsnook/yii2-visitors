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
 * This is the model class for table "visitor_agent", holder of this json structure:
 * <code>
 * </code>
 *
 * @property string $user_agent
 * @property string $name
 * @property array $info
 *
 * @property string $agentType
 * @property string $agentName
 * @property string $agentVersion
 * @property string $osType
 * @property string $osName
 * @property string $osVersionName
 * @property string $osVersionNumber
 * @property string $osProducer
 * @property string $osProducerUrl
 * @property string $linuxDistribution
 * @property string $agentLanguage
 * @property string $agentLanguageTag
 *
 * @property VisitorLog[] $visitorLogs
 */
class VisitorAgent extends \yii\db\ActiveRecord {

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'visitor_agent';
    }

    public function beforeSave($insert) {
        if (gettype($this->info) !== 'string')
            $this->info = json_encode($this->info);
        return true;
    }

    public function afterFind() {
        parent::afterFind();
        $this->info = json_decode($this->info);
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['user_agent'], 'required'],
            [['user_agent', 'name'], 'string'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'user_agent' => 'User Agent',
            'name' => 'Name',
            'info' => 'Information',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getVisitorLogs() {
        return $this->hasMany(VisitorLog::className(), ['user_agent' => 'user_agent']);
    }

    /**
     * @return string
     */
    public function getAgentType() {
        if (isset($this->info->agent_type)) {
            return $this->info->agent_type;
        }
    }

    /**
     * @return string
     */
    public function getAgentName() {
        if (isset($this->info->agent_name)) {
            return $this->info->agent_name;
        }
    }

    /**
     * @return string
     */
    public function getAgentVersion() {
        if (isset($this->info->agent_version)) {
            return $this->info->agent_version;
        }
    }

    /**
     * @return string
     */
    public function getOsType() {
        if (isset($this->info->os_type)) {
            return $this->info->os_type;
        }
    }

    /**
     * @return string
     */
    public function getOsName() {
        if (isset($this->info->os_name)) {
            return $this->info->os_name;
        }
    }

    /**
     * @return string
     */
    public function getOsVersionName() {
        if (isset($this->info->os_versionName)) {
            return $this->info->os_versionName;
        }
    }

    /**
     * @return string
     */
    public function getOsVersionNumber() {
        if (isset($this->info->os_versionNumber)) {
            return $this->info->os_versionNumber;
        }
    }

    /**
     * @return string
     */
    public function getOsProducer() {
        if (isset($this->info->os_producer)) {
            return $this->info->os_producer;
        }
    }

    /**
     * @return string
     */
    public function getOsProducerUrl() {
        if (isset($this->info->os_producerURL)) {
            return $this->info->os_producerURL;
        }
    }

    /**
     * @return string
     */
    public function getLinuxDistribution() {
        if (isset($this->info->linux_distibution)) {
            return $this->info->linux_distibution;
        }
    }

    /**
     * @return string
     */
    public function getAgentLanguage() {
        if (isset($this->info->agent_language)) {
            return $this->info->agent_language;
        }
    }

    /**
     * @return string
     */
    public function getAgentLanguageTag() {
        if (isset($this->info->agent_languageTag)) {
            return $this->info->agent_languageTag;
        }
    }

    /**
     * Requests proxy information from http://www.useragentstring.com/
     * <code>
     * {
     *     "parse": {
     *         "user_agent": "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_9_5) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/64.0.3282.140 Safari/537.36",
     *         "software_name": "Chrome",
     *         "operating_system": "Mac OS X (Mavericks)",
     *         "software_version": 64,
     *         "operating_system_name": "Mac OS X",
     *         "operating_system_version_full": [
     *             10,
     *             9,
     *             5
     *         ],
     *         "software_name_code": "chrome",
     *         "simple_operating_platform_string": null,
     *         "operating_system_version": "Mavericks",
     *         "simple_sub_description_string": null,
     *         "is_abusive": false,
     *         "operating_system_flavour_code": null,
     *         "software_version_full": [
     *             64,
     *             0,
     *             3282,
     *             140
     *         ],
     *         "simple_software_string": "Chrome 64 on Mac OS X (Mavericks)",
     *         "operating_system_flavour": null,
     *         "operating_system_name_code": "mac-os-x",
     *         "software": "Chrome 64"
     *     },
     *     "result": {
     *         "message": "The user agent was parsed successfully.",
     *         "code": "success",
     *         "message_code": "user_agent_parsed"
     *     }
     * }
     * </code>
     *
     * @return array
     */
    public static function log($userAgent) {
        if (empty($userAgent)) {
            return;
        }
        if (is_null($vaModel = VisitorAgent::findOne($userAgent))) {

            $yii = self::getRealYiiPath();
            exec("php $yii ipFilter/service/user-agent '$userAgent' > /dev/null 2>&1 &");
        }
    }

    private static function getRealYiiPath() {
        $path = \Yii::$app->basePath;
        if (file_exists($path . '/yii')) {
            return $path . '/yii';
        } elseif (file_exists($path . '/../yii')) {
            return $path . '/../yii';
        } else {
            throw new \Exception("Couldn't find yii executable to run user agent script");
        }
    }

}