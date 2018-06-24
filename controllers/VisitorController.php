<?php

namespace johnsnook\ipFilter\controllers;

use Yii;
use johnsnook\ipFilter\models\Visitor;
use johnsnook\ipFilter\models\VisitorLogSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\data\SqlDataProvider;
use yii\filters\AccessControl;

/**
 * VisitorController implements the CRUD actions for Visitor model.
 */
class VisitorController extends Controller {

    /**
     * @inheritdoc
     */
    public function behaviors() {
        return [
//            'verbs' => [
//                'class' => VerbFilter::className(),
//                'actions' => [
//                    'delete' => ['POST'],
//                ],
//            ],
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['index', 'view', 'update'],
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['index', 'view'],
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
    public function actionIndex() {
        $sql = "select distinct v.ip, count(vl.*) as count, access_type, "
                . "max(vl.created_at) as recent, info::text "
                . "from visitor v left join visitor_log vl using(ip) group by v.ip, access_type ";
        $count = Yii::$app->db->createCommand("SELECT COUNT(*) as cnt FROM ($sql) as foo")->queryScalar();

        $dataProvider = new SqlDataProvider([
            'key' => 'ip',
            'sql' => $sql,
            'totalCount' => $count,
            'pagination' => [
                'pageSize' => 20,
            ],
            'sort' => [
                'defaultOrder' => ['recent' => SORT_DESC], //, 'count' => SORT_DESC
                'attributes' => [
                    'recent' => ['default' => SORT_DESC],
                    'count',
                    'ip',
                    'city',
                    'access_type'
                ],
            ],
        ]);
        return $this->render('index', ['dataProvider' => $dataProvider,]);
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
     * Deletes an existing Visitor model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id
     * @return mixed
     */
    public function actionDelete($id) {
        $this->findModel($id)->delete();
        return $this->redirect(['index']);
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
