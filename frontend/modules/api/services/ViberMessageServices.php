<?php

namespace frontend\modules\api\services;

use common\entities\ViberMessage;
use yii\web\UploadedFile;
use common\notifications\AdminModerateNotification;

class ViberMessageServices
{
    public function send($post, ViberMessage $model)
    {
            $model->scenario = ViberMessage::SCENARIO_HARD;
            $model->status = ViberMessage::STATUS_NEW;
            if ($model->send()) {
                AdminModerateNotification::create('moderate', ['message' => $model])->send();
                return true;
            } else {
                return false;
            }

    }
}