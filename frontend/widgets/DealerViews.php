<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 17.02.18
 * Time: 17:13
 */

namespace frontend\widgets;

use frontend\entities\User;
use yii\base\Widget;
class DealerViews extends Widget
{
    public $id;
    public function run()
    {
       $dealer=User::find()->where(['id'=>$this->id])->select(['username','tel','email','time_work'])->one();
       return $this->render('dealer',compact('dealer')); 

    }

}