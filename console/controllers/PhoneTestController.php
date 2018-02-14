<?php
/**
 * Created by PhpStorm.
 * User: mike
 * Date: 14.02.2018
 * Time: 10:55
 */

namespace console\controllers;

use common\entities\mongo\Phone;
use common\entities\ViberMessage;
use yii\console\Controller;
use Yii;
class PhoneTestController extends Controller
{
    public function actionCheck(){
        $phones = Phone::find()->select(['phone'])
            ->where(['in','contact_collection_id',['1','2']])
            ->limit(2)
            ->offset(5)
            ->distinct('phone');

    }

    public function actionVmCheck(){
        $vm = ViberMessage::find()
            ->where(['in','id',['1']])->one();
        $cc = $vm->getMessageContactCollections()
            ->select(['contact_collection_id'])->distinct('contact_collection_id')->column();
        foreach ($cc as $k=>$v){
            $cc[$k]= '' . $v;
        }
        print_r($cc);
        $phones = Phone::find()->select(['phone'])
            ->where(['in','contact_collection_id',$cc])
            ->limit(2)
            ->offset(5)
            ->distinct('phone');
        print_r($phones);

    }
    public function actionDbCheck(){
        $mdb = Yii::$app->mongodb;
        print_r($mdb);


    }
}