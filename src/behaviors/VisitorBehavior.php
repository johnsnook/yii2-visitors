<?php

/**
 * @author John Snook
 * @date Aug 4, 2018
 * @license https://snooky.biz/site/license
 * @copyright 2018 John Snook Consulting
 * Description of VisitorBehavior
 */

namespace johnsnook\visitor\behaviors;

use johnsnook\visitor\models\Visitor;

/**
 * Adds visitor attributes to application user component
 */
class VisitorBehavior extends \yii\base\Behavior {

    /**
     * @var \johnsnook\visitor\models\Visitor
     */
    public $visitor;

    /**
     * The ip address of the current visitor
     * @return string
     */
    public function getIp() {
        return $this->visitor->ip;
    }

    /**
     * The city of the current visitor
     * @return string|null
     */
    public function getCity() {
        return $this->visitor->city;
    }

    /**
     * The region of the current visitor
     * @return string|null
     */
    public function getRegion() {
        return $this->visitor->region;
    }

    /**
     * The country of the current visitor
     * @return string
     */
    public function getCountry() {
        return $this->visitor->country;
    }

    /**
     * The postal code of the current visitor
     * @return string|null
     */
    public function getPostal() {
        return $this->visitor->postal;
    }

    /**
     * The proxy type of the current visitor, or 'no'
     * @return string
     */
    public function getProxy() {
        return $this->visitor->proxy;
    }

    /**
     * Whether the current visitor has been banned by an admin
     * @return boolean
     */
    public function getBanned() {
        return $this->visitor->banned;
    }

    /**
     * The hat color of the current visitor.  'Black', 'None', 'White'.
     * @return string
     */
    public function getHat_color() {
        return $this->visitor->hat_color;
    }

    /**
     * The rule that makes the hat black or white.
     * @return string|null
     */
    public function getHat_rule() {
        return $this->visitor->hat_rule;
    }

}
