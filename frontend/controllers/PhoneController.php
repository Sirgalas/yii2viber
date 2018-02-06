<?php

namespace frontend\controllers;

use frontend\forms\PhoneCreateForm;
use frontend\forms\PhoneUpdateForm;
use frontend\services\phone\PhoneFormCreateService;
use frontend\services\phone\PhoneFormUpdateService;
use PHPUnit\Framework\MockObject\RuntimeException;
use Yii;
use common\entities\mongo\Phone;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\web\NotFoundHttpException;

class PhoneController extends Controller
{
    private $createService;
    private $updateService;
    public function __construct($id, $module,PhoneFormCreateService $create,PhoneFormUpdateService $update, array $config = [])
    {
        $this->createService=$create;
        $this->updateService=$update;
        parent::__construct($id,$module, $config);
    }

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

    public function actionCreate($id){

        $form = new PhoneCreateForm();
        if($id)
            $form->contact_collection_id=Yii::$app->request->get('id');
        $form->clients_id=Yii::$app->user->identity->id;
        if($form->load(Yii::$app->request->post())&&$form->validate()){
            try{
               $entities=$this->createService->create($form);
               return $this->redirect('view',['id'=>$entities->_id]);
            }catch (RuntimeException $ex){
                Yii::$app->errorHandler->logException($ex);
                Yii::$app->session->setFlash('error', $ex->getMessage());
            }
        }else{
            return $this->render('create',[
                'model'=>$form
            ]);
        }

    }

    public function actionUpdate($id){
        try{
            $entities=$this->findModel($id);
        }catch (NotFoundHttpException $ex){
            Yii::$app->errorHandler->logException($ex);
            Yii::$app->session->setFlash('error',$ex);
        }
        $form = new PhoneUpdateForm($entities);
        if($form->load(Yii::$app->request->post())&&$form->validate()){
            try{
                $this->updateService($form);
                return $this->redirect('view',['id'=>$entities->_id]);
            }catch (RuntimeException $ex){
                Yii::$app->errorHandler->logException($ex);
                Yii::$app->session->setFlash('error', $ex->getMessage());
            }
        }else{
            return $this->render('update',[
                'model'=>$form,
                'entities'=>$entities
            ]);
        }
    }

    public function actionViews($id){
        return $this->render('view',[
            'model'=>$this->findModel($id)
        ]);
    }

    public function actionDelete($id){
        if(!Phone::find()->where(['_id'=>$id,'clients_id'=>Yii::$app->user->identity->id]))
            throw new RuntimeException('Этот телефон вам не принадлежит');
        $entities=$this->findModel($id);
        try{
            if(!$entities->delete())
                throw new RuntimeException(json_encode($entities->errors));
            return $this->redirect($this->goBack());
        }catch (RuntimeException $ex){
            Yii::$app->errorHandler->logException($ex);
            Yii::$app->session->setFlash('error', $ex->getMessage());
        }
    }

    private function findModel($id){
        if(($model=Phone::find()->where(['_id'=>$id]))!=null)
            return $model;
        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }

}