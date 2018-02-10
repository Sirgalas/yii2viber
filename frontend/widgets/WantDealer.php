<?php

namespace frontend\widgets;

use yii\base\Widget;
use common\entities\user\User;

class WantDealer extends Widget
{
    public function run()
    {
       $wantDealer=User::find()->where(['dealer_id'=>\Yii::$app->user->identity->id])->all();
       if(!$wantDealer)
           return false;
       return $this->render('wantDealer',
           [
               'users'=>$wantDealer
           ]);
    }

}