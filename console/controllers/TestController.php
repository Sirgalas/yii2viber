<?php


namespace console\controllers;

use common\entities\mongo\Phone;
use common\entities\user\User;
use PHPUnit\Framework\MockObject\RuntimeException;
use yii\console\Controller;
use Yii;
class TestController extends Controller
{

    public function actionAddPhone(){
       // $phone = Yii::$app->mongodb->getCollection('phone');
        $array=['contact_collection_id'=>1,
                'phone'=>79789877878,
                'clients_id'=>1,
                'username'=>'John'];
        $phone= new Phone($array);
        var_dump($phone->save());
        /*try{
            if(!$phone->save())
                throw new RuntimeException( json_encode($phone->errors));
        }catch (RuntimeException $ex){
            var_dump($ex->getMessage());
        }*/

    }
    public function actionAddMessage(){
        $message_phone_list = Yii::$app->mongodb->getCollection('message_phone_list');

        try{
            if(! $message_phone_list->insert([
                'message_id'=>1,
                'last_date_message'=>79789877878,
                'status'=>1,]))
                throw new RuntimeException( json_encode($message_phone_list->errors));
        }catch (RuntimeException $ex){
            var_dump($ex->getMessage());
        }

    }
    public function actionCheckUserRecursive(){
        $user=User::findOne(8);
        echo 'actionCheckUserRecursive 8 & 9, result= ', $user->amParent(9);
        echo "\n",'    actionCheckUserRecursive 8 & 10    result=', $user->amParent(10);
    }
}