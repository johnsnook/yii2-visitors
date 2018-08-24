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
use johnsnook\visitors\models\Visits;
use johnsnook\visitors\models\VisitsSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * VisitsController implements the CRUD actions for Visits model.
 */
class VisitsController extends Controller {

    /**
     * {@inheritdoc}
     */
    public function beforeAction($action) {
        if (parent::beforeAction($action)) {
            $this->view->params['breadcrumbs'][] = ['label' => 'Dashboard', 'url' => ['/visitors']];
            return true;
        }
        return false;
    }

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
        $searchModel = new VisitsSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        return $this->render('visits-index', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
        ]);
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
     * Returns a map view of the current user query.
     * @return mixed
     */
    public function actionMap() {
        $searchModel = new VisitsSearch();
        $searchModel->search(Yii::$app->request->queryParams);
        return $this->render('visits-map', [
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

    /**
     * Finds the Visits model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Visits the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id) {
        if (($model = Visits::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

}
