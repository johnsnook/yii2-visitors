<?php

/**
 * This file is part of the Yii2 extension module, yii2-visitor
 *
 * @author John Snook
 * @date 2018-06-28
 * @license https://github.com/johnsnook/yii2-visitor/LICENSE
 * @copyright 2018 John Snook Consulting
 */

namespace johnsnook\visitor\controllers;

use Yii;
use yii\filters\AccessControl;

/**
 * VisitorController implements the CRUD actions for the Visitor model.
 */
class IndividualController extends \yii\web\Controller {

    const REPLACEMENTS_TEMPLATE = ['{ip_address}', '{key}'];

    /**
     * @var string The template for the ip info API.
     */
    const TEMPLATE_IP_INFO_URL = 'http://ipinfo.io/{ip_address}?token={key}';

    /**
     * @inheritDoc
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

    /**
     * Displays the index page
     *
     * @return string
     */
    public function actionIndex() {
        return $this->render('quick');
    }

    /**
     * Performs ip info query
     *
     * @return string|null
     */
    public function actionLookup($ip) {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $visitor = Yii::$app->getModule('visitor');
        $url = str_replace(self::REPLACEMENTS_TEMPLATE, [$ip, $visitor->ipInfoKey], self::TEMPLATE_IP_INFO_URL);
        try {
            if (!empty($data = json_decode(file_get_contents($url)))) {
                return $data;
            }
        } catch (\yii\base\Exception $e) {
            return null;
        }
    }

}
