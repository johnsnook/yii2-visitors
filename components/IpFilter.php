<?php

/**
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace johnsnook\ipFilter\components;

use johnsnook\ipFilter\lib\RemoteAddress;
use johnsnook\ipFilter\models\Visitor;
use yii\base\Component;

class IpFilter extends Component {

    protected $ip;

    const PROXY_CHECK_URL = 'http://proxycheck.io/v2/{ip_address}&key={key}&vpn=1&inf=0';
    const IP_INFO_URL = 'http://ipinfo.io/{ip_address}?token={key}';
    const TEMPLATE = ['{ip_address}', '{key}'];

    public $ipInfoKey = '';
    public $proxyCheckKey = '';
    private static $visitor;

    /**
     * {@inheritDoc}
     * Gets the IP address, gets a visitor object, fills it in if new, saves it,
     * refreshes it and sets this components $visitor static property
     */
    public function init() {
        parent::init();
        $remoteAddress = new RemoteAddress();
        $this->ip = $remoteAddress->getIpAddress();
        $visitor = Visitor::ringDoorbell($this->ip);
        if ($visitor->isNewRecord) {
            $visitor->ip_info = $this->getIpInfo();
            $visitor->proxy_check = $this->getProxyInfo();
        }
        if (!$visitor->save()) {
            die(json_encode($visitor->errors));
        }
        $visitor->refresh();
        self::$visitor = $visitor;
    }

    /**
     * Request ip information from ipinfo.io which looks like
     *     "hostname": "c-24-99-237-149.hsd1.ga.comcast.net",
     *     "city": "Decatur",
     *     "region": "Georgia",
     *     "country": "US",
     *     "loc": "33.8110,-84.2869",
     *     "postal": 30033,
     *     "org": "AS7922 Comcast Cable Communications, LLC"
     *
     * @return array
     */
    private function getIpInfo() {
        $url = str_replace(self::TEMPLATE, [
            $this->ip, $this->ipInfoKey], self::IP_INFO_URL);

        if (!empty($data = json_decode(file_get_contents($url)))) {
            return $data;
        }
        return [];
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
            $this->ip, $this->proxyCheckKey], self::PROXY_CHECK_URL);

        if (!empty($data = json_decode(file_get_contents($url)))) {
            return $data[$this->ip];
        }
        return [];
    }

    /**
     * Returns the IP without the masking part
     *
     * @return string The IP without the masking part
     */
    public function getPlainIp() {
        return split('/', $this->ip)[0];
    }

}
