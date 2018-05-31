<?php

namespace frontend\modules\api\controllers;

use frontend\entities\User;
use frontend\modules\api\services\ViberMessageServices;
use Yii;
use frontend\modules\api\components\AcViberController;
use common\entities\ViberMessage;
use yii\web\NotFoundHttpException;
use yii\web\UploadedFile;
class MessageController extends AcViberController
{
    public $modelClass = 'common\entities\ViberMessage';

    public function actionIndex()
    {

        if (!Yii::$app->user->identity->id) {
            return 'User not Auth';
        }
        $viberMessages = ViberMessage::find()->where(['user_id' => Yii::$app->user->identity->id])->all();
        $user = User::findOne(['id' => Yii::$app->user->identity->id]);
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
        if (!Yii::$app->user->identity->id) {
            return 'User not Auth';
        }

        if (Yii::$app->request->post('id')) {
            $id = Yii::$app->request->post('id');
            $model = ViberMessage::findOne(['id' => $id]);
            if (!Yii::$app->user->identity->isAdmin() && !Yii::$app->user->identity->amParent($model->user_id) && Yii::$app->user->id != $model->user_id) {
                throw new NotFoundHttpException('newsletter does not belong user', 500);
            }
        } else {
            $model = new ViberMessage();
        }
        $channel = Yii::$app->request->post('channel');
        $type = Yii::$app->request->post('type');

        if ($channel == 'whatsapp' && (($type == ViberMessage::TEXTBUTTON) || !empty(Yii::$app->request->post('title_button') || !empty(Yii::$app->request->post('url_button')) || !empty(Yii::$app->request->post('alpha_name'))))) {
            throw new NotFoundHttpException('the forbidden fields are indicated', 500);
        }

        if ($channel == 'sms' && (($type != ViberMessage::ONLYTEXT) || (!empty(Yii::$app->request->post('upload_file')) || !empty(Yii::$app->request->post('title_button')) || !empty(Yii::$app->request->post('url_button')) || !empty(Yii::$app->request->post('alpha_name'))))) {
            throw new NotFoundHttpException('the forbidden fields are indicated', 500);
        }
        $a['ViberMessage'] = Yii::$app->request->post();
        $a['ViberMessage']['user_id'] = Yii::$app->user->identity->id;
        $a['button'] = ViberMessage::STATUS_NEW;
        if (!$model->load($a)) {
            throw new NotFoundHttpException('request not validate' . print_r($model->getErrors(), 1), 500);
        }
        $model->status=ViberMessage::STATUS_NEW;
        $services = new ViberMessageServices();
        $file = Yii::$app->request->post('upload_file');
        return $file;
        try {
            if (!$services->send($a, $model)) {
                throw new NotFoundHttpException('message not send', 404);
            }
            return ['success' => 'message sending', 'messageId '=> $model->id];
        } catch (NotFoundHttpException $e) {
            return $e->getMessage();
        }
    }

    public function actionCancel($id)
    {
        $model = ViberMessage::findOne($id);
        if ($model->Cancel()) {
            return ['succes' => 'message cancel'];
        }
        return ['error' => 'message not cancel'];
    }
}
