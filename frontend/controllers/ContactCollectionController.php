<?php

namespace frontend\controllers;

use common\entities\mongo\Phone;
use common\entities\mongo\PhoneSearch;
use Yii;
use common\entities\ContactCollection;
use common\entities\ContactCollectionSearch;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use common\entities\Phone as PgPhone;
use common\entities\PhoneSearch as PgPhoneSearch;

/**
 * ContactCollectionController implements the CRUD actions for ContactCollection model.
 */
class ContactCollectionController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
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
     * Lists all ContactCollection models.
     *
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new ContactCollectionSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', compact('searchModel', 'dataProvider'));
    }

    /**
     * Displays a single ContactCollection model.
     *
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {

        $searchModel = new PhoneSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, $id);

        return $this->render('view', [
            'modelCollections' => $this->findModel($id),
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);
    }

    /**
     * Creates a new ContactCollection model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     *
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new ContactCollection();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['index']);
        }
        $phoneSearchModel = new PgPhoneSearch();
        $phoneDataProvider = $phoneSearchModel->search(Yii::$app->request->queryParams);

        return $this->render('create', compact('model', 'phoneSearchModel', 'phoneDataProvider'));
    }

    /**
     * Updates an existing ContactCollection model.
     * If update is successful, the browser will be redirected to the 'view' page.
     *
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['index']);
        }
        $phoneSearchModel =new PgPhoneSearch();
        $phoneSearchModel->contact_collection_id=$id;
        $phoneDataProvider = $phoneSearchModel->search(Yii::$app->request->queryParams);
        return $this->render('update', compact('model', 'phoneSearchModel', 'phoneDataProvider'));
    }

    /**
     * Deletes an existing ContactCollection model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     *
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the ContactCollection model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     *
     * @param integer $id
     * @return ContactCollection the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = ContactCollection::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }

    public function actionNewPhones($id){
        $collection = ContactCollection::findOne($id);
        if (!$collection){
            return 'Ошибка в запросе. Обновите страницу';
        }
        // TODO проверить права на коллекцию
        $phone = new PgPhone();
        return $phone->importText($id, $_POST['txt']);

    }
    public function actionRemovePhones($id){
        $collection = ContactCollection::findOne($id);
        if (!$collection){
            return 'Ошибка в запросе. Обновите страницу';
        }
        // TODO проверить права на коллекцию
        $phone = new PgPhone();
        return $phone->removeList($id, $_POST['ids']);

    }
}
