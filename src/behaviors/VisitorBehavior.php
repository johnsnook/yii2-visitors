<?php

/**
 * @author John Snook
 * @date Aug 4, 2018
 * @license https://snooky.biz/site/license
 * @copyright 2018 John Snook Consulting
 * Description of VisitorBehavior
 */

namespace johnsnook\ipFilter\behaviors;

use johnsnook\ipFilter\models\Visitor;

class VisitorBehavior extends \yii\base\Behavior {

    /**
     * @var \johnsnook\ipFilter\models\Visitor
     */
    public $visitor;

    public function getIp() {
        return $this->visitor->ip;
    }

    public function getCity() {
        return $this->visitor->city;
    }

    public function getRegion() {
        return $this->visitor->region;
    }

    public function getCountry() {
        return $this->visitor->country;
    }

    public function getPostal() {
        return $this->visitor->postal;
    }

    public function getProxy() {
        return $this->visitor->proxy;
    }

    public function getBanned() {
        return $this->visitor->banned;
    }

    public function getHat_color() {
        return $this->visitor->hat_color;
    }

    public function getHat_rule() {
        return $this->visitor->hat_rule;
    }

}
