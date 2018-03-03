<?php

namespace frontend\modules\home\widgets;

use common\entities\Config;
use yii\base\Widget;

class PriceWidget extends Widget
{
    public function run()
    {
        $config= Config::find()->where(['description'=>'price'])->orderBy(['id'=>SORT_ASC])->all();
        return $this->render('price',[
            'config'=>$config
        ]);
    }
}