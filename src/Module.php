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
 * @property string $googleMapsApiKey Api key
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
     * @var string $googleMapsApiKey Go to https://developers.google.com/maps/documentation/javascript/get-api-key for a free API key
     */
    public $googleMapsApiKey;

    /**
     * @var string $proxyCheckKey Go to https://proxycheck.io/ for a free API key
     */
    public $proxyCheckKey = '';

    /**
     * @var string $whatsmybrowswerKey Go to https://proxycheck.io/ for a free API key
     */
    public $whatsmybrowswerKey = '';

    /**
     * @var array The list of proxy types we autoban
     */
    public $proxyBan = ['VPN', 'Compromised Server', 'SOCKS', 'SOCKS4', 'HTTP', 'SOCKS5', 'HTTPS', 'TOR'];

    /**
     *
     * @var array The list of CIDRs we automatically ban
     */
    public $autoBan = [];

    /**
     * @var string Controllers will use this value if set to allow the user to
     * define their own custom views.
     */
    //public $viewPath;
    /**
     * @var boolean Whether to rely on blacklist flag or calculate it every time
     */
    public $forceCheck = false;

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
        '/visitor' => '/ipFilter/visitor/index',
        '/visitor/index' => '/ipFilter/visitor/index',
        '/visitor/blowoff' => '/ipFilter/visitor/blowoff',
        '/visitor/<id>' => '/ipFilter/visitor/view',
        '/visitor/update/<id>' => '/ipFilter/visitor/update',
        '/individual/<id>' => '/ipFilter/individual/view',
    ];
    public $bootstrapCssVersion = 3;

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
     * Why don't you pull yourself up by the bootstraps?
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
                $app->on(Application::EVENT_BEFORE_ACTION, [$module, 'leGauntlet']);
            }
        }
    }

    /**
     * Called on the BeforeAction event, it logs the current visitor, and if it's
     * a new visitor, gather their ipInfo, proxy information and browser info.
     * Then, check their blacklist status or compares their info against the
     * blacklist criteria.
     *
     * If the visitor IS blacklisted, redirect them to the blowoff action.  If
     * they're going to the blowoff action, let them and don't redirect them so
     * much the browser throws a fucking error.
     *
     * @param ActionEvent $event
     * @return boolean whether the current action should continue
     */
    public function leGauntlet(ActionEvent $event) {

        /** get user ip, if null, send them to the blowoff */
        if (is_null($ip = Yii::$app->request->getUserIP())) {
            $event->handled = true;
            if ($event->action->controller->route !== $this->blowOff) {
                return \Yii::$app->getResponse()->redirect([$this->blowOff])->send();
            } else {
                return true;
            }
        }

        /**
         * Check to see if this action is listed in the ignorables array or if
         * the visitors ip is in the whitelist
         */
        $controllerId = $event->action->controller->id;
        if (array_key_exists($controllerId, $this->ignorables) && in_array($event->action->id, $this->ignorables[$controllerId])) {
            return true;
        } elseif (array_key_exists('whitelist', $this->ignorables) && in_array($ip, $this->ignorables['whitelist'])) {
            return true;
        }

        /**
         * Try to find existing visitor record, and creates a new one if not found
         * Also logs this visit in the access_log
         */
        $this->visitor = Visitor::findOne($ip);
        if (is_null($this->visitor)) {
            $this->visitor = new Visitor([
                'ip' => $ip,
                'ipInfoKey' => $this->ipInfoKey,
                'proxyCheckKey' => $this->proxyCheckKey,
            ]);
            if (!$this->visitor->save()) {
                die(json_encode($this->visitor->errors));
            }
            $this->visitor->refresh();
        }

        /** Attach the visitor behavior to the user */
        $visitorBehavior = new behaviors\VisitorBehavior(['visitor' => $this->visitor]);
        \Yii::$app->user->attachBehavior('visitor', $visitorBehavior);

        /** Log the visit */
        $log = VisitorLog::log($ip);
        VisitorAgent::log($log->user_agent);
        /** Allow the blacklisted visitor to reach the blowoff action */
        if ($event->action->controller->route === $this->blowOff) {
            $event->handled = true;
            return true;
        }

        if ($this->isBlacklisted()) {
            $event->handled = true;
            return \Yii::$app->getResponse()->redirect([$this->blowOff])->send();
        }

        return true;
    }

    public function isBlacklisted() {
        return $this->visitor->isBlacklisted($this->forceCheck);
    }

    /**
     * @return Database connection
     */
    public function getDb() {
        return \Yii::$app->get($this->dbConnection);
    }

}
