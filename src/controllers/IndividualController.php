<?php

/**
 * This file is part of the Yii2 extension module, yii2-ip-filter
 *
 * @author John Snook
 * @date 2018-06-28
 * @license https://github.com/johnsnook/yii2-ip-filter/LICENSE
 * @copyright 2018 John Snook Consulting
 */

namespace johnsnook\ipFilter\controllers;

use Yii;
use johnsnook\ipFilter\models\Visitor;
use johnsnook\ipFilter\models\VisitorLogSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\AccessControl;

/**
 * VisitorController implements the CRUD actions for the Visitor model.
 */
class IndividualController extends Controller {
    //public $viewPath = __dir__ . '/../views/individual';

    /**
     * @inheritdoc
     */
    public function behaviors() {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['view'],
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['view'],
                        'roles' => ['?', '@', 'admin'],
                    ],
                ],
            ],
        ];
    }

    /**
     * Displays a single Visitor model and a GridView from VisitorLog of this
     * Visitor's activity on your site
     *
     * @param string $id The IP address to be viewed
     * @return string  A rendered view of the Vistor's details and a list of
     * activity
     */
    public function actionView($id) {
        $searchModel = new VisitorLogSearch();
        $searchModel->ip = $id;
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $this->setViewPath(__dir__ . '/../views/individual');
        //dump($this->viewPath);
        //die();
        return $this->render('view', [
                    'model' => $this->findModel($id),
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Finds the Visitor model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return Visitor the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id) {
        if (($model = Visitor::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

}
