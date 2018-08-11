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

use johnsnook\ipFilter\helpers\IpHelper;
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
    protected $defaultBlackProxies = ['VPN', 'Compromised Server', 'SOCKS', 'SOCKS4', 'HTTP', 'SOCKS5', 'HTTPS', 'TOR'];

    /**
     * @var array The list of CIDRs we automatically ban
     */
    public $autoBan = [];

    /**
     * @var array Rules that show the visitor the door
     */
    public $blackRules = [];

    /**
     * @var array Rules that welcomes a visitor in
     */
    public $whiteRules = [
        'ip' => [
            '52.0.0.0/15',
        ]
    ];

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
     * Why don't you pull yourself up by the bootstraps like I did by being born
     * middle class and having parents who could help pay for college?
     *
     * If we're running in console mode set the controller space to our commands
     * folder.  If not, attach our main event to the the [[ap beforeAction
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
        $this->visitor->visit = VisitorLog::log($ip);
        VisitorAgent::log($this->visitor->visit->user_agent);

        /** Allow the rejected visitor to reach the blowoff action */
        if ($event->action->controller->route === $this->blowOff) {
            return $event->handled = true;
        }

        /** the banned don't need to be checked, they can just go. * */
        if ($this->visitor->banned) {
            $event->handled = true;
            return \Yii::$app->getResponse()->redirect([$this->blowOff])->send();
        }

        /** White list */
        if (!is_null($checkEm = static::checkList($this->whiteRules, $this->visitor))) {
            $this->visitor->hat_color = Visitor::HAT_COLOR_WHITE;
            $this->visitor->hat_rule = implode(array_keys($checkEm)) . ' ' . implode($checkEm);
            $this->visitor->save();
            $event->handled = true;
            return true;
        }
//        echo str_repeat('*', 20) . PHP_EOL;
//        die();
        /** Black list (that's racist!) */
        if (!is_null($checkEm = static::checkList($this->blackRules, $this->visitor))) {
            $this->visitor->hat_color = Visitor::HAT_COLOR_BLACK;
            $this->visitor->hat_rule = implode(array_keys($checkEm)) . ' ' . implode($checkEm);
            $this->visitor->save();
            $event->handled = true;
            return \Yii::$app->getResponse()->redirect([$this->blowOff])->send();
        }

        return true;
    }

    /**
     * Compares properties against a indexed array of arrays of values.  String
     * values are compared using stripos, ip values should be in CIDR format.
     *
     * @param array $list
     * @param Visitor $visitor
     * @return array The list element that matched
     */
    protected static function checkList($list, $visitor) {
//        dump($list, $visitor);
        if (isset($list['ip'])) {
//            $c = count($list['ip']);
//            echo "list['ip'] is set [$c]<br>";
            foreach ($list['ip'] as $ip) {
//                echo "$visitor->ip in $ip = " . IpHelper::inRange($visitor->ip, $ip) . '<br>';
                if (IpHelper::inRange($visitor->ip, $ip)) {
//                    echo "found: $visitor->ip in $ip<br>";
//                    die();

                    return ['ip' => $ip];
                }
            }
        }
//        die();
        $stringAttributes = ['city', 'region', 'country', 'postal', 'asn', 'organization', 'proxy'];
        foreach ($stringAttributes as $sAttr) {
            if (isset($list[$sAttr])) {
                foreach ($list[$sAttr] as $val) {
                    if (stripos($visitor->$sAttr, $val) !== false) {
                        return [$sAttr => $val];
                    }
                }
            }
        }

        $stringAttributes = ['referer', 'request', 'user_agent'];
        foreach ($stringAttributes as $sAttr) {
            if (isset($list[$sAttr])) {
                foreach ($list[$sAttr] as $val) {
                    if (stripos($visitor->visit->$sAttr, $val) !== false) {
                        return [$sAttr => $val];
                    }
                }
            }
        }

        return null;
    }

    /**
     * @return Database connection
     */
    public function getDb() {
        return \Yii::$app->get($this->dbConnection);
    }

}
