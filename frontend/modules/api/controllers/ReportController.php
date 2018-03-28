<?php

namespace frontend\modules\api\controllers;

use Yii;
use frontend\modules\api\components\AcViberController;
use common\entities\ViberTransaction;
use frontend\search\ReportSearch;
use yii\web\NotFoundHttpException;

class ReportController extends AcViberController
{
    public $modelClass = 'common\entities\ViberMessage';

    public function actionIndex(){
        $viberTransacion=ViberTransaction::find()->where(['user_id' => \Yii::$app->user->identity->id])->all();
        return $viberTransacion;
    }

    public function actionFind(){
        $searchModel = new ReportSearch();
        if(!Yii::$app->request->post())
            throw new NotFoundHttpException('request not found',403);
        $data = $searchModel->searchApi(Yii::$app->request->post());
        return $data;
    }

}