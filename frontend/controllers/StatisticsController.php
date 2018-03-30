<?php

namespace frontend\controllers;

use common\entities\MessageContactCollection;
use common\entities\mongo\Message_Phone_List;
use common\entities\ViberTransaction;
use frontend\search\StatisticsSearch;
use common\entities\ContactCollection;
use frontend\entities\User;
use yii\helpers\ArrayHelper;
use frontend\search\StatisticsMongoSearch;
use Yii;
use common\entities\mongo\Phone;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\web\NotFoundHttpException;

class StatisticsController extends Controller
{


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

    public function actionIndex(){
        $model= new ViberTransaction(['scenario' => ViberTransaction::SCENARIO_SEARCH]);
        $model->load(Yii::$app->request->post());
        $contact_collections = ContactCollection::find()->andWhere(['user_id' => Yii::$app->user->identity->id])->select([
            'id',
            'title',
        ])->orderBy('title')->asArray()->all();
        $contact_collections = ArrayHelper::map($contact_collections, 'id', 'title');
        $clients=ArrayHelper::map(User::find()->select(['id','username'])->where(['dealer_id'=>Yii::$app->user->identity->id])->orderBy('username')->asArray()->all(),'id','username');
        $messagePhoneList= new Message_Phone_List();
        $post=false;
        if(Yii::$app->request->isPost)
        $post=Yii::$app->request->post('ViberTransaction');
        $searchModel = new StatisticsMongoSearch();
        $status=$messagePhoneList->allStatus();
        ViberTransaction::find()->where(['user_id'=>Yii::$app->user->identity->id]);
        $dataProvider = $searchModel->search(Yii::$app->request->post('ViberTransaction'));
        $providerFromGetModel=(new StatisticsSearch())->search(Yii::$app->request->post('ViberTransaction'));
        return $this->render('index', compact('model','contact_collections','searchModel', 'dataProvider','clients','post','messagePhoneList','status','providerFromGetModel'));
    }


    public function actionViews($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

   
    public function actionCreateExel($id){
            
    }

    private function findModel($id)
    {
        if (($model = Phone::find()->where(['_id' => $id])->one()) != null) {
            return $model;
        }
        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }
}