<?php

/**
 * This file is part of the Yii2 extension module, yii2-visitor
 *
 * @author John Snook
 * @date 2018-06-28
 * @license https://github.com/johnsnook/yii2-visitor/LICENSE
 * @copyright 2018 John Snook Consulting
 */

namespace johnsnook\visitor\controllers;

use Yii;
use johnsnook\visitor\models\Visitor;
use johnsnook\visitor\models\VisitorSearch;
use johnsnook\visitor\models\VisitorLogSearch;
use yii\web\NotFoundHttpException;
use yii\filters\AccessControl;

/**
 * VisitorController implements the CRUD actions for the Visitor model.
 */
class VisitorController extends \yii\web\Controller {

    /**
     * @inheritDoc
     */
    public function behaviors() {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['index', 'view', 'update', 'blowoff', 'blacklist'],
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['index', 'view', 'blowoff'],
                        'roles' => ['?'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['index', 'view', 'update', 'blacklist'],
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    /**
     * Lists all Visitor models.
     * @return string A rendered view of the list of visitors
     */
    public function actionIndex() {
        $searchModel = new VisitorSearch();

        return $this->render('index', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $searchModel->search(Yii::$app->request->queryParams),
        ]);
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
        //$dataProvider->setPagination(['pageSize' => 10]);
        return $this->render('view', [
                    'model' => $this->findModel($id),
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Sets the [[Visitor::banned]] flag of this particular individual.
     * @return mixed
     */
    public function actionBlacklist($id) {
        $model = $this->findModel($id);
        $this->blacklist_reason = Visitor::BL_MANUAL;
        $model->banned = true;
        $model->save();
        return $this->actionView($id);
    }

    /**
     * Displays a blowoff Visitor message.
     * @return mixed
     */
    public function actionBlowoff() {
        $this->layout = 'blowy';
        return $this->render('blowoff');
    }

    /**
     * Updates an existing Visitor model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     */
    public function actionUpdate($id) {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $id]);
        } else {
            return $this->render('update', [
                        'model' => $model,
            ]);
        }
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
