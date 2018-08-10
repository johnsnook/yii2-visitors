<?php

/**
 * @author John Snook
 * @date Aug 4, 2018
 * @license https://snooky.biz/site/license
 * @copyright 2018 John Snook Consulting
 * AccessRule
 */

namespace johnsnook\ipFilter\filters;

use yii\filters\AccessRule as BaseAccessRule;

class AccessRule extends BaseAccessRule {

    /**
     * @var array list of user cities that this rule applies to.
     * If not set or empty, it means this rule applies to all cities.
     * @see Visitor::city
     */
    public $cities;

    /**
     * @var array list of user regions that this rule applies to.
     * If not set or empty, it means this rule applies to all regions.
     * @see Visitor::region
     */
    public $regions;

    /**
     * @var array list of user countries that this rule applies to.
     * If not set or empty, it means this rule applies to all countries.
     * @see Visitor::country
     */
    public $countries;

    /**
     * Checks whether the Web user is allowed to perform the specified action.
     * @param Action $action the action to be performed
     * @param User|false $user the user object or `false` in case of detached User component
     * @param Request $request
     * @return bool|null `true` if the user is allowed, `false` if the user is denied, `null` if the rule does not apply to the user
     */
    public function allows($action, $user, $request) {
        $visitor = \Yii::$app->getModule('ipFilter')->visitor;
        if (!is_null($visitor)) {
            if ($this->matchCity($visitor->city) && $this->matchRegion($visitor->region) && $this->matchCountry($visitor->country) && $this->matchAction($action) && $this->matchRole($user) && $this->matchIP($request->getUserIP()) && $this->matchVerb($request->getMethod()) && $this->matchController($action->controller) && $this->matchCustom($action)) {

                return $this->allow ? true : false;
            }
        }
//        else {
//            if ($this->matchAction($action) && $this->matchRole($user) && $this->matchIP($request->getUserIP()) && $this->matchVerb($request->getMethod()) && $this->matchController($action->controller) && $this->matchCustom($action)) {
//                return $this->allow ? true : false;
//            }
//        }
        return null;
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
        return empty($this->regions) || in_array($region, $this->regions, true);
    }

    /**
     * @param string|null $country the Country
     * @return bool whether the rule applies to the Country
     */
    protected function matchCountry($country) {
        return empty($this->countries) || in_array($country, $this->countries, true);
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
