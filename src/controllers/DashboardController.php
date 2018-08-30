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

use Yii;
use johnsnook\visitors\models\VisitsSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * VisitsController implements the CRUD actions for Visits model.
 */
class DashboardController extends Controller {

    /**
     * {@inheritdoc}
     */
    public function behaviors() {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
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
    public function actionLine() {
        #return $this->render('dashboard-visits-line');
        return $this->render('dashboard-highcharts');
    }

    public function actionD3() {
        return $this->render('dashboard-d3');
    }

    /**
     * Returns a graph view of the current user query.
     * @return mixed
     */
    public function actionGraph() {
        $searchModel = new VisitsSearch();
        $searchModel->search(Yii::$app->request->queryParams);
        return $this->render('visits-graph', [
                    'searchModel' => $searchModel,
        ]);
    }

    /**
     * Displays a single Visits model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id) {
        return $this->render('view', [
                    'model' => $this->findModel($id),
        ]);
    }

}
