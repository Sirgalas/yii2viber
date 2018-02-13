<?php

namespace frontend\controllers;

use common\entities\ContactCollection;
use common\entities\MessageContactCollection;
use common\entities\mongo\Phone;
use frontend\entities\User;
use Yii;
use common\entities\ViberMessage;
use common\entities\ViberMessageSearch;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * ViberMessageController implements the CRUD actions for ViberMessage model.
 */
class ViberMessageController extends Controller
{
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Lists all ViberMessage models.
     *
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new ViberMessageSearch;
        $dataProvider = $searchModel->search(Yii::$app->request->getQueryParams());

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);
    }

    /**
     * Displays a single ViberMessage model.
     *
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('view', ['model' => $model]);
        }
    }

    /**
     * Creates a new ViberMessage model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     *
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new ViberMessage;
        $clients=ArrayHelper::map(User::find()->where(['dealer_id'=>Yii::$app->user->identity->id])->all(),'id','username');
        if ($model->load(Yii::$app->request->post()) && $model->save()) {

            return $this->redirect(['index']);
        } else {
            $contact_collections = ContactCollection::find()
                ->andWhere(['user_id'=>Yii::$app->user->id])
                ->select(['id','title'])
                ->orderBy('title')
                ->asArray()
                ->all();
            $contact_collections=ArrayHelper::map($contact_collections, 'id','title');
            return $this->render('create',compact('model','contact_collections','assign_collections','clients'));
        }
    }

    /**
     * Updates an existing ViberMessage model.
     * If update is successful, the browser will be redirected to the 'view' page.
     *
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $clients=ArrayHelper::map(User::find()->where(['dealer_id'=>Yii::$app->user->identity->id])->all(),'id','username');
        if ($model->load(Yii::$app->request->post())) {
            $upload_file = $model->uploadFile();
            if ($model->save()) {
                if ($upload_file !== false) {
                    $path = $model->getUploadedFile();
                    $upload_file->saveAs($path);
                }
                return $this->redirect(['index']);
            }
        }
        $contact_collections = ContactCollection::find()
            ->andWhere(['user_id'=>$model->user_id])
            ->select(['id','title'])
            ->orderBy('title')
            ->asArray()
            ->all();
        $contact_collections=ArrayHelper::map($contact_collections, 'id','title');
        $assign_collections = MessageContactCollection::find()
            ->select(['contact_collection_id'])
            ->andWhere(['viber_message_id'=>$id])
            ->column();


        return $this->render('update', compact('model','contact_collections','assign_collections','clients'));

    }

    /**
     * Deletes an existing ViberMessage model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     *
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    public function actionCoast(){
        $post=Yii::$app->request->post('data');
        try{
            return (new ViberMessage)->Coast($post);
        }catch (\Exception $ex){
            Yii::$app->errorHandler->logException($ex);
        }
    }

    /**
     * Finds the ViberMessage model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     *
     * @param integer $id
     * @return ViberMessage the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = ViberMessage::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    public function actionAssignCollection($id){
        $model= $this->findModel($id);
        if(!Yii::$app->user->identity->amParent($model->user_id)){
            throw new NotFoundHttpException('Этот пользователь вам не принадлежит',403);
        }
        return MessageContactCollection::assign($id,$model->user_id,  $_POST['data']);
        print_r($_POST);
    }
}
