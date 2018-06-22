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
use yii\base\ActionEvent;
use yii\base\Module as BaseModule;

/**
 * This is the main module class for the Yii2-user.
 *
 * @property array $modelMap
 *
 * @author John Snook <jsnook@gmail.com>
 */
class Module extends BaseModule {

    const VERSION = '0.0.1';

    /**
     *
     * @var string
     */
    public $ipAddress;
    public $proxyCheckUrlTemplate = 'http://proxycheck.io/v2/{ip_address}&key={key}&vpn=1&inf=0';
    public $ipInfoUrlTemplate = 'http://ipinfo.io/{ip_address}?token={key}';

    const TEMPLATE = ['{ip_address}', '{key}'];

    public $ipInfoKey = '';
    public $proxyCheckKey = '';
    public $blowOff;
    public $controller = 'johnsnook\ipFilter\controllers\VisitorController';
    private static $visitor;

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
        'visitor/view/<ipAddress>' => 'visitor/view',
        'visitor/update/<ipAddress>' => 'visitor/update',
        'visitor/delete/<ipAddress>' => 'visitor/delete',
    ];

    /**
     * Handles the BeforeAction event
     *
     * @param ActionEvent $event
     */
    public function metalDetector(ActionEvent $event) {
        $remoteAddress = new RemoteAddress();
        $this->ipAddress = $remoteAddress->getIpAddress();
        $visitor = Visitor::ringDoorbell($this->ipAddress);
        if ($visitor->isNewRecord) {
            $visitor->ip_info = $this->getIpInfo();
            $visitor->proxy_check = $this->getProxyInfo();
            //die($visitor->proxy_check);
            if ($visitor->proxy_check->proxy === 'yes') {
                $visitor->access_type = Visitor::ACCESS_LIST_BLACK;
            }
        }
        if (!$visitor->save()) {
            die(json_encode($visitor->errors));
        }
        $visitor->refresh();
        if ($visitor->access_type === Visitor::ACCESS_LIST_BLACK) {
            $url = Url::toRoute([$this->blowOff, 'visitor' => $visitor]);
            $event->result = \Yii::$app->controller->redirect($url);
            $event->isValid = FALSE;
        }
        self::$visitor = $visitor;
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
    private function getIpInfo() {
        $url = str_replace(self::TEMPLATE, [
            $this->ipAddress, $this->ipInfoKey], $this->ipInfoUrlTemplate);

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
    private function getProxyInfo() {
        $url = str_replace(self::TEMPLATE, [
            $this->ipAddress, $this->proxyCheckKey], $this->proxyCheckUrlTemplate);

        if (!empty($data = json_decode(file_get_contents($url), true))) {
            return (object) $data[$this->ipAddress];
        }
        return (object) [];
    }

    /**
     * Returns the IP without the masking part
     *
     * @return string The IP without the masking part
     */
    public function getPlainIp() {
        return split('/', $this->ipaddess)[0];
    }

    /**
     * @return string
     */
    public function getDb() {
        return \Yii::$app->get($this->dbConnection);
    }

}
