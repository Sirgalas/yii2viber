<?php
/**
 * Created by PhpStorm.
 * User: mike
 * Date: 03.03.2018
 * Time: 3:11
 */

namespace common\components\providers;

use common\entities\ViberMessage;
use common\components\providers\smsonline\SmsOnline;
use common\components\providers\infobip\InfoBip;
use common\components\providers\gatesms\GateSms;

use Yii;
class ProviderFactory
{
    public function createProvider(ViberMessage $viberMessage): Provider
    {
        if ($viberMessage->channel === 'viber') {
            if ($viberMessage->provider === 'smsonline') {
                return new SmsOnline(Yii::$app->params['smsonline']);
            }
            if ($viberMessage->provider === 'infobip') {
                return new InfoBip(Yii::$app->params['infobip']);
            }
        } else if ($viberMessage->channel === 'sms') {
                return new GateSms(Yii::$app->params['gatesms']);
        }
        return new InfoBip(Yii::$app->params['infobip']);
    }
}