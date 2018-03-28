<?php

namespace frontend\modules\api\controllers;

use frontend\entities\User;
use frontend\services\message\ViberMessageServices;
use Yii;
use frontend\modules\api\components\AcViberController;
use common\entities\ViberMessage;
use yii\web\NotFoundHttpException;

class MessageController extends AcViberController
{
    public $modelClass = 'common\entities\ViberMessage';

    public function actionIndex()
    {

        if (!Yii::$app->user->identity->id) {
            return 'User not Auth';
        }
        $viberMessages = ViberMessage::find()->where(['user_id' => Yii::$app->user->identity->id])->all();
        $user = User::findOne(['id'=>Yii::$app->user->identity->id]);
        if (!$user) {
            return ['error' => 'user not find'];
        }
        foreach ($viberMessages as $viberMessage) {
            $result[] = [
                'id' => $viberMessage->id,
                'user' => $user->username,
                'image' => $viberMessage->image,
                'type' => $viberMessage->type,
                'status' => $viberMessage->status,
                'cost' => $viberMessage->cost,
            ];
        }
        if (!$result) {
            $result = ['error' => 'message not find'];
        }
        return $result;
    }



    public function actionSend()
    {
        if (!Yii::$app->user->identity->id) 
            return 'User not Auth';
        if(!Yii::$app->request->post('id'))
            throw new NotFoundHttpException('id not specified');
        $id=Yii::$app->request->post('id');
        if ($id) {
            $model = ViberMessage::findOne(['id'=>$id]);
            if (! Yii::$app->user->identity->isAdmin() && ! Yii::$app->user->identity->amParent($model->user_id) && Yii::$app->user->id != $model->user_id) {
                throw new NotFoundHttpException('newsletter does not belong user', 500);
            }
        } else {
            $model = new ViberMessage();
        }
        if (! $model->status) {
            $model->status = ViberMessage::STATUS_PRE;
        }

        if (!$model->load(Yii::$app->request->post()))
            throw new NotFoundHttpException('request not validate', 500);

        $services= new ViberMessageServices();
        try{
            if(!$services->send(Yii::$app->request->post(),$model))
                throw new NotFoundHttpException('message not send',404);
            return ['success'=>'message sending'];
        }catch (NotFoundHttpException $e){
            return $e->getMessage();
        }
    }
}