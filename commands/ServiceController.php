<?php

/**
 * This file is part of the Yii2 extension module, yii2-ip-filter
 *
 * @author John Snook
 * @date 2018-06-28
 * @license https://github.com/johnsnook/yii2-ip-filter/LICENSE
 * @copyright 2018 John Snook Consulting
 */

namespace johnsnook\ipFilter\commands;

use yii;
use yii\helpers\Console;
use yii\console\Controller;
use johnsnook\ipFilter\models\Visitor;
use johnsnook\ipFilter\models\VisitorAgent;
use johnsnook\ipFilter\models\VisitorServiceError;

/**
 * ServiceController provides 3 commands to request various information for websites
 * They are her as commands so that they can be executed in the background
 * suffixed by "> /dev/null 2>&1 &", thus free the web app from waiting for a
 * response.
 */
class ServiceController extends Controller {

    /**
     * @var array The replacements template
     */
    const REPLACEMENTS_TEMPLATE = ['{ip_address}', '{key}'];

    /**
     * @var string The template for the ip info API.
     */
    const TEMPLATE_IP_INFO_URL = 'http://ipinfo.io/{ip_address}?token={key}';

    /**
     * @var string The template for the proxy check API.
     */
    const TEMPLATE_PROXY_CHECK_URL = 'http://proxycheck.io/v2/{ip_address}&key={key}&vpn=1&inf=0';

    /**
     * @var string The template for the user agent API.
     */
    const USER_AGENT_URL = 'https://api.whatismybrowser.com/api/v2/user_agent_parse';

    /**
     * Retrieves basic information about the visitor, including location and network
     *
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
     * @param string $ip The IP address of the current visitor
     * @param string $apiKey The API key
     */
    public function actionIpinfo($ip) {
        $ipFilter = \Yii::$app->controller->module;

        if (!is_null($visitor = Visitor::findOne($ip))) {
            $url = str_replace(self::REPLACEMENTS_TEMPLATE, [$ip, $ipFilter->ipInfoKey], self::TEMPLATE_IP_INFO_URL);
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            if (($response = curl_exec($ch) ) === false) {
                $vse = new VisitorServiceError;
                $vse->service = "Ip Info";
                $vse->url = "url";
                $vse->message = curl_error($ch);
                $vse->save();
            } else {
                $info = json_decode($response);
                $visitor->city = $info->city;
                $visitor->region = $info->region;
                $country = Country::findOne(['code' => $info->country]);
                $visitor->country = $country->name;
                if ($info->loc) {
                    $visitor->latitude = floatval(explode(',', $info->loc)[0]);
                    $visitor->longitude = floatval(explode(',', $info->loc)[1]);
                }
                $visitor->organization = $info->org;
                $visitor->save();
            }
            curl_close($ch);
        }
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
     * @param string $ip The IP address of the current visitor
     * @param string $apiKey The API key
     */
    public function actionProxyCheck($ip) {
        $ipFilter = \Yii::$app->controller->module;
        if (!is_null($visitor = Visitor::findOne($ip))) {
            $url = str_replace(self::REPLACEMENTS_TEMPLATE, [$ip, $ipFilter->proxyCheckKey], self::TEMPLATE_PROXY_CHECK_URL);
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            if (($response = curl_exec($ch) ) === false) {
                $vse = new VisitorServiceError;
                $vse->service = "Proxy Check";
                $vse->url = "url";
                $vse->message = curl_error($ch);
                $vse->save();
            } else {
                $pcheck = json_decode($response);
                $visitor->proxy = ($pcheck->proxy === 'yes' ? $pcheck->type : 'no');
                if ($visitor->proxy !== 'no') {
                    $visitor->is_blacklisted = true;
                }
                $visitor->save();
            }
            curl_close($ch);
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
    public function actionUserAgent($userAgent) {
        $ipFilter = \Yii::$app->controller->module;

        if (is_null($agent = VisitorAgent::findOne($userAgent))) {
            $data = ["user_agent" => $userAgent];

            $agent = new VisitorAgent($data);
            echo "New agent\n";

            $ch = curl_init(self::USER_AGENT_URL);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['X-API-KEY: ' . $ipFilter->whatsmybrowswerKey]);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            if (($response = curl_exec($ch) ) === false) {
                $vse = new VisitorServiceError;
                $vse->service = "User Agent";
                $vse->url = "url";
                $vse->params = json_encode($data);
                $vse->message = curl_error($ch);
                $vse->save();
                echo "Error" . curl_error($ch) . PHP_EOL;
            } else {
                $agent->info = json_decode($response);
                echo "Response received\n" . json_encode($agent->info, 224);
                if ($agent->save()) {
                    echo "Agent Saved\n";
                } else {
                    echo "Agent NOT Saved\n";
                    echo json_encode($agent->errors, 224) . PHP_EOL;
                }
            }
            curl_close($ch);
            return;
        }
        echo "Agent already exists.\n";
        return Controller::EXIT_CODE_NORMAL;
    }

}
