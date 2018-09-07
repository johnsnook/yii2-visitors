<?php

/**
 * This file is part of the Yii2 extension module, yii2-visitor
 *
 * @author John Snook
 * @date 2018-06-28
 * @license https://github.com/johnsnook/yii2-visitor/LICENSE
 * @copyright 2018 John Snook Consulting
 */

namespace johnsnook\visitors\controllers;

use johnsnook\visitors\models\Visits;
use johnsnook\visitors\models\VisitsSearch;
use johnsnook\visitors\models\Visitor;
use johnsnook\visitors\models\VisitorSearch;
use yii\db\Expression;
use yii\web\Controller;
use yii\web\Response;
use yii\helpers\ArrayHelper;
use yii\db\Query;

/**
 * VisitsController implements the CRUD actions for Visits model.
 */
class ExplorationController extends Controller {

    /**
     * Make the default response format JSON instead of XML
     */
    public function init() {
        parent::init();
        \Yii::$app->response->format = Response::FORMAT_JSON;
    }

    /**
     * Returns data for a line chart
     *
     * @param string $day
     * @param string $userQuery
     * @return array
     */
    public function actionVisits($userQuery = null, $day = null) {
        $visitor = new VisitorSearch(['userQuery' => $userQuery]);
        $visits = new VisitsSearch(['userQuery' => $userQuery]);
        if (empty($day)) {
            $a = $visitor->getNewVistorsByDay();
            $b = $visits->getNewVisitsByDay();
        } else {
            $a = $visitor->getNewVistorsByHour($day);
            $b = $visits->getNewVisitsByHour($day);
        }
        $c = ArrayHelper::index($a, 'x');
        $d = ArrayHelper::index($b, 'x');
        $es = ArrayHelper::merge($c, $d);
        ArrayHelper::multisort($es, ['x'], [SORT_ASC]);
        foreach ($es as &$e) {
            if (!isset($e['visitors'])) {
                $e['visitors'] = null;
            }
        }
        $chartData = [
            'x' => static::prepend('x', ArrayHelper::getColumn($es, 'x')),
            'visitors' => static::prepend('New Visitors', ArrayHelper::getColumn($es, 'visitors')),
            'visits' => static::prepend('Visits', ArrayHelper::getColumn($es, 'visits')),
        ];
        return $chartData;
    }

    public static function prepend($value, $array) {
        array_unshift($array, $value);
        return array_values($array);
    }

    public function actionVisitorMap() {
        $q = Visitor::find()
                ->select('organization')
                ->distinct()
                ->addSelect(['latitude', 'longitude', 'organization', 'visits', 'city', 'region', 'country'])
                ->where(['>', 'visits', 1])
                ->groupBy(['latitude', 'longitude', 'organization', 'visits', 'city', 'region', 'country'])
                ->orderBy('visits desc');
        //->groupBy(['ip''latitude', 'longitude', ]);
        $rs = $q->asArray()->all();
        $out = [];
        foreach ($rs as $r) {
            $r = (object) $r;
            $location = $this->getLocation($r->city, $r->region, $r->country);
            $key = "{$r->organization}{$r->latitude}{$r->longitude}";
            if (isset($out[$key])) {
                $out[$key]['visits'] += intval($r->visits);
            } else {
                $out[$key] = [
                    'latitude' => floatval($r->latitude),
                    'longitude' => floatval($r->longitude),
                    'visits' => intval($r->visits),
                    'who-where' => "{$r->organization} @ {$location}"
                ];
            }
        }
        return array_values($out);
    }

    private function getLocation($city, $regioin, $country) {
        $loc = [];
        if (!empty($city)) {
            $loc[] = $city;
        }
        if (!empty($regioin)) {
            $loc[] = $regioin;
        }
        if (!empty($country)) {
            $loc[] = $country;
        }
        if (empty($loc)) {
            return '(not set)';
        }
        return implode(', ', $loc);
    }

    public function actionGeojson() {

        $q = Visitor::find()
                ->select('organization')
                ->distinct()
                ->addSelect(['latitude', 'longitude', 'organization', 'visits', 'city', 'region', 'country'])
                ->where(['>', 'visits', 1])
                ->groupBy(['latitude', 'longitude', 'organization', 'visits', 'city', 'region', 'country'])
                ->orderBy('visits desc');

        $features = [];
        foreach ($q->asArray()->all() as $r) {
            $r = (object) $r;
            $location = $this->getLocation($r->city, $r->region, $r->country);
            $key = "{$r->organization}{$r->latitude}{$r->longitude}";
            if (isset($features[$key])) {
                $features[$key]['properties']['visits'] += intval($r->visits);
            } else {
                $features[$key] = [
                    'type' => 'Feature',
                    'geometry' => [
                        'type' => 'Point',
                        "coordinates" => [floatval($r->longitude), floatval($r->latitude)]
                    ],
                    'properties' => [
                        'visits' => intval($r->visits),
                        'whoAndWhere' => "{$r->organization} @ {$location}"
                    ],
                ];
            }
        }

        return [
            'type' => 'FeatureCollection',
            'features' => array_values($features),
        ];
    }

}
