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
use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;

/**
 * VisitsController implements the CRUD actions for Visits model.
 */
class DashboardController extends Controller {

    /**
     * {@inheritdoc}
     */
    public function beforeAction($action) {
        if (parent::beforeAction($action)) {
            $this->view->params['breadcrumbs'][] = ['label' => 'Dashboard', 'url' => ['/dashboard']];
            return true;
        }
        return false;
    }

    /**
     * Lists all Visits models.
     * @return mixed
     */
    public function actionIndex() {
        return $this->render('index');
    }

    /**
     * Lists all Visits models.
     * @return mixed
     */
    public function actionVisitsVisitors() {
        return $this->render('dashboard-daily-visits');
    }

    /**
     * Returns a graph view of the current user query.
     * @return mixed
     */
    public function actionVisitorsMap() {
        return $this->render('dashboard-visitors-map');
    }

}
