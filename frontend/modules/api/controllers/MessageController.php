<?php

namespace frontend\modules\api\controllers;

use frontend\entities\User;
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



    public function actionUpdate()
    {
        if (!Yii::$app->user->identity->id) 
            return 'User not Auth';
        if(!Yii::$app->request->post('id'))
            throw new NotFoundHttpException('id not specified');
        $id=Yii::$app->request->post('id');
        if ($id) {
            $model = ViberMessage::findOne(['id'=>$id]);
            if (! Yii::$app->user->identity->isAdmin() && ! Yii::$app->user->identity->amParent($model->user_id) && Yii::$app->user->id != $model->user_id) {
                throw new NotFoundHttpException('Этот рассылка вам не принадлежит', 403);
            }
        } else {
            $model = new ViberMessage();
        }
        if (! $model->status) {
            $model->status = ViberMessage::STATUS_PRE;
        }
    }


}