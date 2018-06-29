<?php

namespace johnsnook\ipFilter\controllers;

use Yii;
use johnsnook\ipFilter\models\Visitor;
use johnsnook\ipFilter\models\VisitorLogSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\db\Query;

/**
 * VisitorController implements the CRUD actions for Visitor model.
 */
class VisitorController extends Controller {

    /**
     * @inheritdoc
     */
    public function behaviors() {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['index', 'view', 'update', 'blowoff'],
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['index', 'view', 'blowoff'],
                        'roles' => ['?'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['index', 'view', 'update'],
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    /**
     * Lists all Visitor models.
     * @return mixed
     */
    public function actionIndex($search = "", $field = "") {
        if (empty($search)) {
            $query = Visitor::find();
        } elseif (strpos($field, 'log') === 0) {
            $field = explode('-', $field)[1];
            $query = Visitor::find()
                    ->select(['t.ip'])
                    ->distinct()
                    ->addSelect(['city', 'region', 'country', 'visits', 'updated_at'])
                    ->from('visitor t')
                    ->leftJoin('visitor_log vl', 't.ip = vl.ip')
                    ->where(['ilike', $field, "$search"]);
        } else {
            $query = Visitor::find()->where(['ilike', $field, "$search"]);
        }

        //$subquery = (new Query)->from('visitor_log')->where(['active' => true])

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
     * Displays a single Visitor model.
     * @param string $id The IP address to be viewed
     * @return mixed
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
     * Displays a blowoff Visitor message.
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
        $visitor = $this->module->visitor;
        return $this->render('blowoff', [
                    'visitor' => $visitor,
        ]);
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
