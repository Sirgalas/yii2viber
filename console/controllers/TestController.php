<?php


namespace console\controllers;

use common\entities\phone\Phone;
use PHPUnit\Framework\MockObject\RuntimeException;
use yii\console\Controller;
use Yii;
class TestController extends Controller
{

    public function actionAddPhone(){
        $phone = Yii::$app->mongodb->getCollection('phone');

        try{
            if(! $phone->insert([
                'contact_collection_id'=>1,
                'phone'=>79789877878,
                'clients_id'=>1,
                'username'=>'John'
            ]))
                throw new RuntimeException( json_encode($phone->errors));
        }catch (RuntimeException $ex){
            var_dump($ex->getMessage());
        }

    }
}