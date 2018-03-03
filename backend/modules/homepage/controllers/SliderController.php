<?php

namespace backend\modules\homepage\controllers;

use Yii;
use common\entities\Config;
use backend\modules\homepage\search\SliderSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;

/**
 * SliderController implements the CRUD actions for Config model.
 */
class SliderController extends Controller
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
     * Lists all Config models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new SliderSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Config model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Config model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Config();
        $model->description='slider';
        if ($model->load(Yii::$app->request->post())) {
            $model->upload_file = UploadedFile::getInstance($model, 'upload_file');
            try{
                if (!($text=$model->upload()))
                    throw new \RuntimeException('Сохранить картинку не удалось');
                $model->text=$text;
                if(!$model->save()){
                    foreach ($model->getErrors()as $errors)
                        $erorr[]=$errors[0];
                    throw new \RuntimeException('Сохранить слайдер не удалось '.implode(', ',$erorr));
                }
                return $this->redirect(['view', 'id' => $model->id]);
            }catch (\RuntimeException $e){
                Yii::$app->session->setFlash($e->getMessage());
            }
        }
        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Config model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post())) {
            $model->upload_file = UploadedFile::getInstance($model, 'upload_file');
            try{
                if($model->upload_file){
                    if (!($text=$model->upload()))
                        throw new \RuntimeException('Сохранить картинку не удалось');
                    $model->text=$text;
                }
                if(!$model->save()){
                    foreach ($model->getErrors()as $errors)
                        $erorr[]=$errors[0];
                    throw new \RuntimeException('Сохранить слайдер не удалось '.implode(', ',$erorr));
                }
                return $this->redirect(['view', 'id' => $model->id]);
            }catch (\RuntimeException $e){
                Yii::$app->session->setFlash($e->getMessage());
            }
        }
        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Config model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
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
     * Finds the Config model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Config the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Config::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
