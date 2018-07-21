<?php

/**
 * This file is part of the Yii2 extension module, yii2-ip-filter
 *
 * @author John Snook
 * @date 2018-06-28
 * @license https://github.com/johnsnook/yii2-ip-filter/LICENSE
 * @copyright 2018 John Snook Consulting
 */

namespace johnsnook\ipFilter\controllers;

use Yii;
use johnsnook\ipFilter\models\Visitor;
use johnsnook\ipFilter\models\VisitorLogSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\AccessControl;

/**
 * VisitorController implements the CRUD actions for the Visitor model.
 */
class IndividualController extends Controller {

    const REPLACEMENTS_TEMPLATE = ['{ip_address}', '{key}'];

    /**
     * @var string The template for the ip info API.
     */
    const TEMPLATE_IP_INFO_URL = 'http://ipinfo.io/{ip_address}?token={key}';

    /**
     * @inheritdoc
     */
    public function behaviors() {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['view'],
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['view'],
                        'roles' => ['?', '@', 'admin'],
                    ],
                ],
            ],
        ];
    }

    public function actionIndex() {
        return $this->render('quick');
    }

    public function actionLookup($ip) {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $ipFilter = Yii::$app->getModule('ipFilter');
        $url = str_replace(self::REPLACEMENTS_TEMPLATE, [$ip, $ipFilter->ipInfoKey], self::TEMPLATE_IP_INFO_URL);
        try {
            if (!empty($data = json_decode(file_get_contents($url)))) {
                return $data;
            }
        } catch (\yii\base\Exception $e) {
            return null;
        }
    }

}
