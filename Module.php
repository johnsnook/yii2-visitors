<?php

/*
 * This file is part of the yii2-ip-filter project.
 *
 * (c) John Snook Consulting  <http://github.com/john snook/> 
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
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
use yii\helpers\Url;
use yii\web\Application;

/**
 * This is the main module class for the Yii2-user.
 *
 * @property string $mapquestKey
 *
 * @author John Snook <jsnook@gmail.com>
 */
class Module extends BaseModule implements BootstrapInterface {

    const VERSION = '0.0.1';

    public $mapquestKey;
    public static $proxyCheckUrlTemplate = 'http://proxycheck.io/v2/{ip_address}&key={key}&vpn=1&inf=0';
    public static $ipInfoUrlTemplate = 'http://ipinfo.io/{ip_address}?token={key}';
    public static $userAgentUrlTemplate = 'http://www.useragentstring.com/?uas={user_agent}&getJSON=all ';
    public $blacklistProxyType = ['VPN', 'TOR'];
    public $ignore;

    const TEMPLATE = ['{ip_address}', '{key}'];

    public $ipInfoKey = '';
    public $proxyCheckKey = '';
    public $blowOff = 'ipFilter/visitor/blowoff';
//    public $controller = 'johnsnook\ipFilter\controllers\VisitorController';
//    public $controllerNamespace = 'johnsnook\ipFilter\controllers';
    public $visitor;
    public $admins = [];
    private $ipBlock = 'iptables -A INPUT -s 123.45.67.89 -j DROP';
    private $ipUnBlock = 'iptables -D INPUT -s 123.45.67.89 -j DROP';

    /**
     * @var string The prefix for user module URL.
     *
     * @See [[GroupUrlRule::prefix]]
     */
    public $urlPrefix = 'ipFilter';

    /** @var array The rules to be used in URL management. */
    public $urlRules = [
        'visitor' => 'ipFilter/visitor/index',
        'visitor/index' => 'ipFilter/visitor/index',
        //'/visitor/blowoff' => 'ipFilter/visitor/blowoff',
        'visitor/<id>' => 'ipFilter/visitor/view',
        'visitor/update/<id>' => 'ipFilter/visitor/update',
        'visitor/delete/<id>' => 'visitor/delete',
    ];

    public function getIsAdmin() {
        return in_array($this->visitor->ip, $this->admins);
    }

    /**
     *
     * @param Application $app
     */
    public function bootstrap($app) {
        if ($app->hasModule('ipFilter') && ($module = $app->getModule('ipFilter')) instanceof Module) {
            //\Yii::$container->set('johnsnook\ipFilter\models\Visitor'); //, ['Visitor' => 'johnsnook\ipFilter\models\Visitor']
            //$app->controllerMap[] = ['Visitor' => 'johnsnook\\ipFilter\\controllers\\VisitorController'];
            $app->getUrlManager()->addRules($module->urlRules, false);

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
        if (array_key_exists($controllerId, $this->ignore) && in_array($event->action->id, $this->ignore[$controllerId])) {
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
            if ($visitor->info->proxy !== 'no') {
                $visitor->access_type = Visitor::ACCESS_LIST_BLACK;
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
        } elseif (!$alreadyFuckingOff && ($visitor->access_type === Visitor::ACCESS_LIST_BLACK)) {
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
        $url = str_replace(self::TEMPLATE, [$ip, $this->ipInfoKey], static::$ipInfoUrlTemplate);
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
        $url = str_replace(self::TEMPLATE, [$ip, $this->proxyCheckKey], self::$proxyCheckUrlTemplate);
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
            $url = str_replace('{user_agent}', $userAgent, self::$userAgentUrlTemplate);
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
