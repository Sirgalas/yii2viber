<?php

namespace frontend\modules\home\widgets;

use common\entities\Config;
use yii\base\Widget;

class ServicesWidget extends Widget
{
    public function run()
    {
        $config=Config::find()->where(['description'=>'services'])->andWhere(['like','param','service_services_'])->all();
        $text=false;
        
        return $this->render('services',[
            'config'=>$config,
            'text'=>$text
        ]);
    }

}