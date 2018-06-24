<?php

/*
 * This file is part of the Dektrium project.
 *
 * (c) Dektrium project <http://github.com/dektrium/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace johnsnook\ipFilter;

use johnsnook\ipFilter\lib\RemoteAddress;
use johnsnook\ipFilter\models\Visitor;
use johnsnook\ipFilter\models\VisitorLog;
use yii\base\ActionEvent;
use yii\base\BootstrapInterface;
use yii\base\Module as BaseModule;
use yii\web\Application;

/**
 * This is the main module class for the Yii2-user.
 *
 * @property array $modelMap
 *
 * @author John Snook <jsnook@gmail.com>
 */
class Module extends BaseModule implements BootstrapInterface {

    const VERSION = '0.0.1';

    public $mapquestKey;
    public static $proxyCheckUrlTemplate = 'http://proxycheck.io/v2/{ip_address}&key={key}&vpn=1&inf=0';
    public static $ipInfoUrlTemplate = 'http://ipinfo.io/{ip_address}?token={key}';

    const TEMPLATE = ['{ip_address}', '{key}'];

    public $ipInfoKey = '';
    public $proxyCheckKey = '';
    public $blowOff;
//    public $controller = 'johnsnook\ipFilter\controllers\VisitorController';
//    public $controllerNamespace = 'johnsnook\ipFilter\controllers';
    public $visitor;

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
        'visitor/<id>' => 'ipFilter/visitor/view',
        'visitor/update/<id>' => 'ipFilter/visitor/update',
        'visitor/delete/<id>' => 'visitor/delete',
    ];

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
        $remoteAddress = new RemoteAddress();
        $ipAddress = $remoteAddress->getIpAddress();
        $visitor = Visitor::ringDoorbell($ipAddress);
        if ($visitor->isNewRecord) {
            $visitor->info = $this->getIpInfo($ipAddress);
            $pcheck = $this->getProxyInfo($ipAddress);
            $visitor->info->proxy = ($pcheck->proxy === 'yes' ? $pcheck->type : 'no');
            if ($visitor->info->proxy !== 'no') {
                $visitor->access_type = Visitor::ACCESS_LIST_BLACK;
            }
            if (!$visitor->save()) {
                die(json_encode($visitor->errors));
            }
            $visitor->refresh();
        }
        VisitorLog::log($ipAddress);
        if ($visitor->access_type === Visitor::ACCESS_LIST_BLACK) {
            $url = Url::toRoute([$this->blowOff, 'visitor' => $visitor]);
            $event->result = \Yii::$app->controller->redirect($url);
            $event->isValid = FALSE;
        }
        $this->visitor = $visitor;
    }

    /**
     * Request ip information from ipinfo.io which looks like
     *     "hostname": "c-24-99-237-149.hsd1.ga.comcast.net",
     *     "city": "Decatur",
     *     "region": "Georgia",
     *     "country": "US",
     *     "loc": "33.8110,-84.2869",
     *     "visitoral": 30033,
     *     "org": "AS7922 Comcast Cable Communications, LLC"
     *
     * @return array
     */
    public function getIpInfo($ipAddress) {
        $ipAddress = self::getPlainIp($ipAddress);
        $url = str_replace(self::TEMPLATE, [$ipAddress, $this->ipInfoKey], static::$ipInfoUrlTemplate);
        if (!empty($data = json_decode(file_get_contents($url)))) {
            return $data;
        }
        return (object) [];
    }

    /**
     * Requests proxy information from proxycheck.io
     *     {
     *         "status": "ok",
     *         "185.220.101.34": {
     *             "proxy": "yes",
     *             "type": "TOR"
     *         }
     *     }
     *
     * @return array
     */
    public function getProxyInfo($ipAddress) {
        $ipAddress = self::getPlainIp($ipAddress);
        $url = str_replace(self::TEMPLATE, [$ipAddress, $this->proxyCheckKey], self::$proxyCheckUrlTemplate);
        if (!empty($data = json_decode(file_get_contents($url), true))) {
            return (object) $data[$ipAddress];
        }
        return (object) [];
    }

    /**
     * Returns the IP without the masking part
     *
     * @return string The IP without the masking part
     */
    public static function getPlainIp($ipAddress) {
        return split('/', $ipAddress)[0];
    }

    /**
     * @return string
     */
    public function getDb() {
        return \Yii::$app->get($this->dbConnection);
    }

}
