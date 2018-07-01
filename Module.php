<?php

/**
 * This file is part of the Yii2 extension module, yii2-ip-filter
 *
 * @author John Snook
 * @date 2018-06-28
 * @license https://github.com/johnsnook/yii2-ip-filter/LICENSE
 * @copyright 2018 John Snook Consulting
 */

namespace johnsnook\ipFilter;

use johnsnook\ipFilter\lib\RemoteAddress;
use johnsnook\ipFilter\models\Visitor;
use johnsnook\ipFilter\models\VisitorAgent;
use johnsnook\ipFilter\models\VisitorLog;
use Yii;
use yii\base\ActionEvent;
use yii\base\BootstrapInterface;
use yii\base\Module as BaseModule;
use yii\web\Application;

/**
 * This is the main module class for the Yii2-ip-filter extension.
 *
 * @property string $ipInfoKey Api key
 * @property string $mapquestKey Api key
 * @property string $proxyCheckKey Api key
 * @property string $whatsmybrowswerKey Api key
 * @property Visitor $visitor The current visitor
 * @property array $ignorables Array of controller actions and IPs to ignore
 * @property array $urlRules Array of rules for a UrlManger configured to pretty Url
 *
 * @author John Snook <jsnook@gmail.com>
 */
class Module extends BaseModule implements BootstrapInterface {

    /**
     * @var string The next release version string
     */
    const VERSION = 'v0.9.3';

    /**
     * @var array The replacements template
     */
    const REPLACEMENTS_TEMPLATE = ['{ip_address}', '{key}'];

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
    public $blowOff = 'ipFilter/visitor/blowoff';

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
     * @var string $whatsmybrowswerKey Go to https://proxycheck.io/ for a free API key
     */
    public $whatsmybrowswerKey = '';

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
        'visitor' => 'ipFilter/visitor/index',
        '/visitor/index' => 'ipFilter/visitor/index',
        '/visitor/blowoff' => 'ipFilter/visitor/blowoff',
        '/visitor/<id>' => '/ipFilter/visitor/view',
        '/visitor/update/<id>' => '/ipFilter/visitor/update',
    ];

    /**
     * {@inheritdoc}
     *
     * If we're running from the console, change the controller namespace
     */
    public function init() {
        parent::init();
        if (Yii::$app instanceof \yii\console\Application) {
            $this->controllerNamespace = 'johnsnook\ipFilter\commands';
        }
    }

    /**
     *
     * @param Application $app
     */
    public function bootstrap($app) {


        if ($app->hasModule('ipFilter') && ($module = $app->getModule('ipFilter')) instanceof Module) {
            $app->getUrlManager()->addRules($this->urlRules, false);
            //die(json_encode($app->getUrlManager()->rules));
            /** this allows me to do some importing from my old security system */
            if ($app instanceof \yii\console\Application) {
                $this->controllerNamespace = 'johnsnook\ipFilter\commands';
            } else {
                $app->on(Application::EVENT_BEFORE_ACTION, [$module, 'metalDetector']);
            }
        }
    }

    /**
     * Handles the BeforeAction event
     *
     * @param ActionEvent $event
     */
    public function metalDetector(ActionEvent $event) {
        $remoteAddress = new RemoteAddress();
        $ip = $remoteAddress->getIpAddress();

        $controllerId = $event->action->controller->id;
        if (array_key_exists($controllerId, $this->ignorables) && in_array($event->action->id, $this->ignorables[$controllerId])) {
            return true;
        }

        /**
         * Try to find existing visitor record, and creates a new one if not found
         * Also logs this visit in the access_log
         */
        $visitor = Visitor::findOne($ip);
        if (is_null($visitor)) {
            $visitor = new Visitor([
                'ip' => $ip,
                'ipInfoKey' => $this->ipInfoKey,
                'proxyCheckKey' => $this->proxyCheckKey,
            ]);
            if (!$visitor->save()) {
                die(json_encode($visitor->errors));
            }
            $visitor->refresh();
        }
        $this->visitor = $visitor;
        if (array_key_exists('whitelist', $this->ignorables) && in_array($ip, $this->ignorables['whitelist'])) {
            return true;
        }

        $log = VisitorLog::log($ip);
        VisitorAgent::log($log->user_agent);
        $alreadyFuckingOff = ($event->action->controller->route === $this->blowOff);
        $this->visitor = $visitor;
        if ($alreadyFuckingOff) {
            return true;
        } elseif (!$alreadyFuckingOff && $visitor->is_blacklisted) {
            $event->handled = true;
            return \Yii::$app->getResponse()->redirect([$this->blowOff])->send();
        }
    }

    /**
     * @return Database connection
     */
    public function getDb() {
        return \Yii::$app->get($this->dbConnection);
    }

}
