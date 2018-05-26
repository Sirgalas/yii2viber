<?php

namespace frontend\services\message;

use common\entities\ViberMessage;
use yii\web\UploadedFile;
use common\notifications\AdminModerateNotification;

class ViberMessageServices
{
    public function send($post, ViberMessage $model)
    {
        if (isset($post['button']) && $post['button'] == 'cancel') {
            $model->Cancel();
            return true;
        }
        var_dump($model->status && !$model->isEditable());
        if ($model->status && !$model->isEditable()) {
            return true;
        }
        if ($model->getAttribute('status') && $model->isEditable()) {
            $model->scenario = ViberMessage::SCENARIO_HARD;
            $model->status = $post['button'];
            if (!$model->validate()) {
                \Yii::warning('2 model->validate :: FALSE ' . print_r($model->getErrors()));
            }
            if ($model->send()) {
                AdminModerateNotification::create('moderate', ['message' => $model])->send();
                return true;
            } else {
                return false;
            }
        }else{
            return false;
        }
    }
}