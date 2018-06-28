<?php

namespace johnsnook\ipFilter\models;

use Yii;

/**
 * This is the model class for table "visitor_agent", holder of this json structure:
 * <code>
 *    {
 *        "agent_type": "Browser",
 *        "agent_name": "Opera",
 *        "agent_version": "9.70",
 *        "os_type": "Linux",
 *        "os_name": "Linux",
 *        "os_versionName": "",
 *        "os_versionNumber": "",
 *        "os_producer": "",
 *        "os_producerURL": "",
 *        "linux_distibution": "Null",
 *        "agent_language": "English - United States",
 *        "agent_languageTag": "en-us"
 *    }
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
            [['user_agent', 'name', 'info'], 'string'],
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

}
