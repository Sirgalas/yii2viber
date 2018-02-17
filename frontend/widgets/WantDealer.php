<?php

namespace frontend\widgets;

use common\entities\user\Client;
use yii\base\Widget;
use common\entities\user\User;

class WantDealer extends Widget
{
    public function run()
    {
       if(\Yii::$app->user->isGuest)
           return false;
       $wantDealer=User::find()->where(['dealer_id'=>\Yii::$app->user->identity->id,'want_dealer'=>Client::WANT])->all();
       if(!$wantDealer)
           return false;
       return $this->render('wantDealer',
           [
               'users'=>$wantDealer
           ]);
    }

}