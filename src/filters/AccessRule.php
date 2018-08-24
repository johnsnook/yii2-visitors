<?php

/**
 * This file is part of the Yii2 extension module, yii2-visitor
 * @author John Snook
 * @date Aug 4, 2018
 * @license https://snooky.biz/site/license
 * @copyright 2018 John Snook Consulting
 * AccessRule
 */

namespace johnsnook\visitors\filters;

use yii\filters\AccessRule as BaseAccessRule;

class AccessRule extends BaseAccessRule {

    /**
     * @var array list of user ip that this rule applies to.
     * If not set or empty, it means this rule applies to all ips.
     * @see [[\johnsnook\visitors\models\Visitor::ip]]
     */
    public $ip;

    /**
     * @var array list of user cities that this rule applies to.
     * If not set or empty, it means this rule applies to all cities.
     * @see [[\johnsnook\visitors\models\Visitor::city]]
     */
    public $city;

    /**
     * @var array list of user regions that this rule applies to.
     * If not set or empty, it means this rule applies to all regions.
     * @see [[\johnsnook\visitors\models\Visitor::region]]
     */
    public $region;

    /**
     * @var array list of user countries that this rule applies to.
     * If not set or empty, it means this rule applies to all countries.
     * @see [[\johnsnook\visitors\models\Visitor::country]]
     */
    public $country;

    /**
     * @var array list of user proxies that this rule applies to.
     * If not set or empty, it means this rule applies to all proxies.
     * @see [[\johnsnook\visitors\models\Visitor::proxy]]
     */
    public $proxy;

    /**
     * @var array list of user hat_color that this rule applies to.
     * If not set or empty, it means this rule applies to all hat_colors.
     * @see [[\johnsnook\visitors\models\Visitor::proxy]]
     */
    public $hat_color;

    /**
     * Checks whether the Web user is allowed to perform the specified action.
     * @param Action $action the action to be performed
     * @param User|false $user the user object or `false` in case of detached User component
     * @param Request $request
     * @return bool|null `true` if the user is allowed, `false` if the user is denied, `null` if the rule does not apply to the user
     */
    public function allows($action, $user, $request) {
        $m1 = $this->matchIP($request->getUserIP()) && $this->matchCity($user->city);
        $m2 = $this->matchRegion($user->region) && $this->matchCountry($user->country);
        $m3 = $this->matchProxy($user->proxy) && $this->matchHatColor($user->hat_color);
        $m4 = $this->matchAction($action) && $this->matchRole($user);
        $m5 = $this->matchVerb($request->getMethod()) && $this->matchController($action->controller);
        $m6 = $this->matchCustom($action);

        if ($m1 && $m2 && $m3 && $m4 && $m5 && $m6) {

            return $this->allow ? true : false;
        }
        return null;
    }

    /**
     * @param string|null $ip the ip address
     * @return bool whether the rule applies to the Ip
     */
    protected function matchIp($ip) {
        return empty($this->ip) || in_array($ip, $this->ip, true);
    }

    /**
     * @param string|null $city the City
     * @return bool whether the rule applies to the City
     */
    protected function matchCity($city) {
        return empty($this->cities) || in_array($city, $this->cities, true);
    }

    /**
     * @param string|null $region the Region
     * @return bool whether the rule applies to the Region
     */
    protected function matchRegion($region) {
        return empty($this->region) || in_array($region, $this->region, true);
    }

    /**
     * @param string|null $country the Country
     * @return bool whether the rule applies to the Country
     */
    protected function matchCountry($country) {
        return empty($this->country) || in_array($country, $this->country, true);
    }

    /**
     * @param string|null $proxy the Proxy
     * @return bool whether the rule applies to the Proxy
     */
    protected function matchProxy($proxy) {
        return empty($this->proxy) || in_array($proxy, $this->proxy, true);
    }

    /**
     * @param string|null $hat_color the hat_color
     * @return bool whether the rule applies to the hat_color
     */
    protected function matchHatColor($hat_color) {
        return empty($this->hat_color) || in_array($hat_color, $this->hat_color, true);
    }

    /**
     * @param User $user the user object
     * @return bool whether the rule applies to the role
     * @throws InvalidConfigException if User component is detached
     */
    protected function matchRole($user) {
        if ($user)
            $items = empty($this->roles) ? [] : $this->roles;

        if (!empty($this->permissions)) {
            $items = array_merge($items, $this->permissions);
        }

        if (empty($items)) {
            return true;
        }

        if ($user === false) {
            throw new InvalidConfigException('The user application component must be available to specify roles in AccessRule.');
        }

        foreach ($items as $item) {
            if ($item === '?') {
                if ($user->getIsGuest()) {
                    return true;
                }
            } elseif ($item === '@') {
                if (!$user->getIsGuest()) {
                    return true;
                }
            } elseif ($item === '!') {
                if (!\Yii::$app->user->isGuest && \Yii::$app->user->identity->canGetProperty('isAdmin') && \Yii::$app->user->identity->isAdmin) {
                    return true;
                }
            } else {
                if (!isset($roleParams)) {
                    $roleParams = $this->roleParams instanceof Closure ? call_user_func($this->roleParams, $this) : $this->roleParams;
                }
                if ($user->can($item, $roleParams)) {
                    return true;
                }
            }
        }

        return false;
    }

}
