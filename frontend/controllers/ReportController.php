<?php

namespace frontend\controllers;

use common\entities\ViberMessage;
use common\entities\ViberTransaction;
use frontend\search\ReportSearch;
use common\entities\ContactCollection;
use yii\helpers\ArrayHelper;
use frontend\search\ReportMongoSearch;
use Yii;
use yii\web\Controller;
use yii\filters\VerbFilter;

class ReportController extends Controller
{


    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    public function actionIndex(){
        $model= new ViberTransaction();
        $searchModel = new ReportSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $contact_collections=ArrayHelper::map(ContactCollection::find()->select(['id','title'])->where(['user_id'=>Yii::$app->user->identity->id])->asArray()->all(),'id','title');
        $status=$model::$statusSend;
        $viberMessage=ArrayHelper::map(ViberMessage::find()->select(['id','title'])->where(['user_id'=>Yii::$app->user->identity->id])->asArray()->all(),'id','title');
        return $this->render('index', compact('model','contact_collections','searchModel', 'dataProvider','status','viberMessage'));
    }


    public function actionList($id)
    {
        $searchModel = new ReportMongoSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        return $this->render('list', compact('searchModel','dataProvider'));
    }

   public function actionInfobip(){
        return 'ok';
   }
    
}