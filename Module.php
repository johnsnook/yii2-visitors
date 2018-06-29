<?php

/**
 * @author John Snook
 * @date 2018-06-28
 * @license https://github.com/johnsnook/yii2-ip-filter/LICENSE
 * @copyright 2018 John Snook Consulting
 */

namespace johnsnook\ipFilter;

use johnsnook\ipFilter\lib\RemoteAddress;
use johnsnook\ipFilter\models\Country;
use johnsnook\ipFilter\models\Visitor;
use johnsnook\ipFilter\models\VisitorAgent;
use johnsnook\ipFilter\models\VisitorLog;
use yii\base\ActionEvent;
use yii\base\BootstrapInterface;
use yii\base\Module as BaseModule;
use yii\web\Application;

/**
 * This is the main module class for the Yii2-user.
 *
 * @property string $ipInfoKey
 * @property string $mapquestKey
 * @property string $proxyCheckKey
 * @property string $proxyCheckKey
 * @property string $proxyCheckKey
 *
 * @author John Snook <jsnook@gmail.com>
 */
class Module extends BaseModule implements BootstrapInterface {

    /**
     * @var string The next release version string
     */
    const VERSION = 'v0.9.1';

    /**
     * @var array The replacements template
     */
    const TEMPLATE = ['{ip_address}', '{key}'];

    /**
     * @var string The template for the user agent API.
     */
    const TEMPLATE_USER_AGENT_URL = 'http://www.useragentstring.com/?uas={user_agent}&getJSON=all';

    /**
     * @var string The template for the proxy check API.
     */
    const TEMPLATE_PROXY_CHECK_URL = 'http://proxycheck.io/v2/{ip_address}&key={key}&vpn=1&inf=0';

    /**
     * @var string The template for the ip info API.
     */
    const TEMPLATE_IP_INFO_URL = 'http://ipinfo.io/{ip_address}?token={key}';

    /**
     * @var Visitor The Visitor record of the currently connected particular individual
     */
    public $visitor;

    /**
     * @var string The route to your blowoff page telling the user to pound sand
     */
    public $blowOff = 'visitor/blowoff';

    /**
     * @var string $ipInfoKey Go to https://ipinfo.io/signup for a free API key
     */
    public $ipInfoKey = '';

    /**
     * @var string $mapquestKey Go to https://developer.mapquest.com/plan_purchase/steps/business_edition/business_edition_free/register for a free API key
     */
    public $mapquestKey;

    /**
     * @var string $proxyCheckKey Go to https://proxycheck.io/ for a free API key
     */
    public $proxyCheckKey = '';

    /**
     * @var array These are the controller actions that will not be logged
     * <code>
     *     [
     *         'site'=> ['about', 'contact'],
     *     ]
     * </code>
     */
    public $ignorables = [];

    /** @var array The rules to be used in URL management. */
    public $urlRules = [
//        'visitor/<action:\w+>' => '/ipFilter/visitor/<action>',
        'visitor' => '/ipFilter/visitor/index',
        'visitor/index' => '/ipFilter/visitor/index',
        'visitor/blowoff' => '/ipFilter/visitor/blowoff',
        'visitor/<id>' => 'ipFilter/visitor/view',
        'visitor/update/<id>' => 'ipFilter/visitor/update',
    ];

    /**
     *
     * @param Application $app
     */
    public function bootstrap($app) {
        if ($app->hasModule('ipFilter') && ($module = $app->getModule('ipFilter')) instanceof Module) {
            $app->getUrlManager()->addRules($this->urlRules, false);

            /** this allows me to do some importing from my old security system */
            if (!($app instanceof \yii\console\Application)) {
                $app->on(Application::EVENT_BEFORE_ACTION, [$module, 'metalDetector']);
            } else {
                $this->controllerNamespace = 'johnsnook\ipFilter\commands';
            }
        }
    }

    /**
     * Handles the BeforeAction event
     *
     * @param ActionEvent $event
     */
    public function metalDetector(ActionEvent $event) {

        $controllerId = $event->action->controller->id;
        if (array_key_exists($controllerId, $this->ignorables) && in_array($event->action->id, $this->ignorables[$controllerId])) {
            return true;
        }

        $remoteAddress = new RemoteAddress();
        $ip = $remoteAddress->getIpAddress();

        /**
         * Try to find existing visitor record, and creates a new one if not found
         * Also logs this visit in the access_log
         */
        $visitor = Visitor::findOne($ip);
        if (is_null($visitor)) {
            $visitor = new Visitor(['ip' => $ip]);
            $info = $this->getIpInfo($ip);
            $visitor->city = $info->city;
            $visitor->region = $info->region;
            $country = Country::findOne(['code' => $info->country]);
            $visitor->country = $country->name;
            if ($info->loc) {
                $visitor->latitude = floatval(explode(',', $info->loc)[0]);
                $visitor->longitude = floatval(explode(',', $info->loc)[1]);
            }
            $visitor->organization = $info->org;
            $pcheck = $this->getProxyInfo($ip);
            $visitor->proxy = ($pcheck->proxy === 'yes' ? $pcheck->type : 'no');
            if ($visitor->proxy !== 'no') {
                $visitor->is_blacklisted = true;
            }
            if (!$visitor->save()) {
                die(json_encode($visitor->errors));
            }
            $visitor->refresh();
        }

        $log = VisitorLog::log($ip);
        $this->logUserAgentInfo($log->user_agent);
        $alreadyFuckingOff = ($event->action->controller->route === $this->blowOff);
        $this->visitor = $visitor;
        if ($alreadyFuckingOff) {
            //die($event->action->controller->route . '=====' . $this->blowOff);
            return true;
        } elseif (!$alreadyFuckingOff && $visitor->is_blacklisted) {
            die(json_encode([$event->action->controller->route, $this->blowOff]));

            $event->handled = true;
            return \Yii::$app->getResponse()->redirect([$this->blowOff])->send();
        }
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
     *        "visitoral": 30033,
     *        "org": "AS7922 Comcast Cable Communications, LLC"
     *    }
     * </code>
     *
     * @return array
     */
    public function getIpInfo($ip) {
        $url = str_replace(self::TEMPLATE, [$ip, $this->ipInfoKey], $this->ipInfoUrlTemplate);
        if (!empty($data = json_decode(file_get_contents($url)))) {
            return $data;
        }
        return (object) [];
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
     * @return array
     */
    public function getProxyInfo($ip) {
        $url = str_replace(self::TEMPLATE, [$ip, $this->proxyCheckKey], $this->proxyCheckUrlTemplate);
        if (!empty($data = json_decode(file_get_contents($url), true))) {
            return (object) $data[$ip];
        }
        return (object) [];
    }

    /**
     * Requests proxy information from http://www.useragentstring.com/
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
     * @return array
     */
    public function logUserAgentInfo($userAgent) {
        if (empty($userAgent)) {
            return;
        }
        if (is_null($vaModel = VisitorAgent::findOne($userAgent))) {
            $vaModel = new VisitorAgent(['user_agent' => $userAgent]);
            $userAgent = urlencode($userAgent);
            $url = str_replace('{user_agent}', $userAgent, $this->userAgentUrlTemplate);
            $vaModel->info = file_get_contents($url);
            $vaModel->save();
        }
        return $vaModel;
    }

    /**
     * @return string
     */
    public function getDb() {
        return \Yii::$app->get($this->dbConnection);
    }

}
