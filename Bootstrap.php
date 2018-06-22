<?php

/**
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace johnsnook\ipFilter;

use yii\base\BaseObject;
use yii\base\BootstrapInterface;
use yii\web\Application;
use yii\helpers\Url;

class Bootstrap implements BootstrapInterface {

    /**
     *
     * @param Application $app
     */
    public function bootstrap($app) {
        if ($app->hasModule('ipFilter') && ($module = $app->getModule('ipFilter')) instanceof Module) {
            \Yii::$container->set('johnsnook\ipFilter\models\Visitor'); //, ['Visitor' => 'johnsnook\ipFilter\models\Visitor']
            $app->controllerMap[] = ['Visitor' => 'johnsnook\\ipFilter\\controllers\\VisitorController'];
            $app->urlManager->addRules($module->urlRules);
            //die(json_encode($app->urlManager->rules));
            $app->on(Application::EVENT_BEFORE_ACTION, [$module, 'metalDetector']);
        }
    }

}
