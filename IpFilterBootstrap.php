<?php

/**
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace johnsnook\ipFilter;

use yii\base\BootstrapInterface;
use yii\web\Application;

class IpFilterBootstrap extends BootstrapInterface {

    public function bootstrap(Application $app) {

        //$app->aliases[] = ['@visitor' => 'johnsnook\ipFilters'];
        $app->on(Application::EVENT_BEFORE_REQUEST, function ($event) {

        });
    }

}
