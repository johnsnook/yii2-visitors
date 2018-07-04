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
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;

/**
 * VisitorController implements the CRUD actions for the Visitor model.
 */
class VisitorController extends Controller {

    /**
     * @inheritdoc
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
    public function actionIndex($search = "", $field = "") {
        if (empty($search)) {
            $query = Visitor::find();
        } elseif (strpos($field, 'log') === 0) {
            $field = explode('-', $field)[1];
            $query = Visitor::find()
                    ->select(['v.ip'])
                    ->distinct()
                    ->addSelect(['city', 'region', 'country', 'visits', 'updated_at'])
                    ->from('visitor v')
                    ->leftJoin('visitor_log vl', 'v.ip = vl.ip')
                    ->where(['ilike', $field, "$search"]);
        } else {
            $query = Visitor::find()->where(['ilike', $field, "$search"]);
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 20,
            ],
            'sort' => [
                'defaultOrder' => [
                    'updated_at' => SORT_DESC,
                ]
            ],
        ]);

        return $this->render('index', [
                    'dataProvider' => $dataProvider,
                    'search' => $search
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

        return $this->render('view', [
                    'model' => $this->findModel($id),
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Sets the [[is_blacklisted]] flag of this particular individual.
     * @return mixed
     */
    public function actionBlacklist($id) {
        $model = $this->findModel($id);
        $model->is_blacklisted = true;
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
