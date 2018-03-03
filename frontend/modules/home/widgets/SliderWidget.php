<?php

namespace frontend\modules\home\widgets;

use common\entities\Config;
use yii\base\Widget;
class SliderWidget extends Widget
{
    public function run(){
        $sliderItem=Config::find()->where(['description'=>'slider'])->all();
        return $this->render('slider',[
           'sliderItems'=>$sliderItem 
        ]);
    }

}