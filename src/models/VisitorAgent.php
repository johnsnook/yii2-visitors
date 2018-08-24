<?php

/**
 * This file is part of the Yii2 extension module, yii2-visitor
 *
 * @author John Snook
 * @date 2018-06-28
 * @license https://github.com/johnsnook/yii2-visitor/LICENSE
 * @copyright 2018 John Snook Consulting
 */

namespace johnsnook\visitors\models;

/**
 * This is the model class for table "visitor_agent", holder of this json structure:
 * <code>
 * </code>
 *
 * @property string $user_agent
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
 * @property Visits[] $visitss
 */
class VisitorAgent extends \yii\db\ActiveRecord {

    /**
     * @var string The template for the user agent API.
     */
    const USER_AGENT_URL = 'https://api.whatismybrowser.com/api/v2/user_agent_parse';

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'visitor_agent';
    }

    /**
     *
     * @param boolean $insert
     * @return boolean
     */
    public function beforeSave($insert) {
        if (gettype($this->info) !== 'string')
            $this->info = json_encode($this->info);
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function afterFind() {
        parent::afterFind();
        $this->info = json_decode($this->info);
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['user_agent'], 'required'],
            [['user_agent'], 'string'],
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
    public function getVisitss() {
        return $this->hasMany(Visits::className(), ['user_agent' => 'user_agent']);
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
    public static function log1($userAgent) {
        if (empty($userAgent)) {
            return;
        }
        if (is_null($vaModel = VisitorAgent::findOne($userAgent))) {

            $yii = self::getRealYiiPath();
            exec("php $yii visitor/service/user-agent '$userAgent' > /dev/null 2>&1 &");
        }
    }

    /**
     *
     * @return string
     * @throws \Exception
     */
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

    /**
     * Requests user agent info from https://whatismybrowser.com
     * <code>
     * Response received
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
     * @param string $userAgent The browser reported string returned by $_[USER_AGENT]
     * @param string $apiKey
     */
    public static function log($userAgent) {
        //$visitor = \Yii::$app->controller->module;
        $visitor = \Yii::$app->getModule('visitor');

        if (is_null($agent = VisitorAgent::findOne($userAgent))) {
            $data = ["user_agent" => $userAgent];

            $agent = new VisitorAgent($data);
            echo "New agent\n";

            $ch = curl_init(self::USER_AGENT_URL);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['X-API-KEY: ' . $visitor->whatsmybrowswerKey]);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            if (($response = curl_exec($ch) ) === false) {
                die("Error" . curl_error($ch) . PHP_EOL);
            } else {
                $agent->info = json_decode($response);
                //echo "Response received\n" . json_encode($agent->info, 224);
                if ($agent->save()) {
                    //  echo "Agent Saved\n";
                } else {
                    //echo "Agent NOT Saved\n";
                    die(json_encode($agent->errors, 224) . PHP_EOL);
                }
            }
            curl_close($ch);
            return;
        }
        //echo "Agent already exists.\n";
    }

}
