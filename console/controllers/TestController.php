<?php


namespace console\controllers;

use common\entities\phone\Phone;
use common\entities\user\User;
use PHPUnit\Framework\MockObject\RuntimeException;
use yii\console\Controller;
use Yii;
use common\components\Viber;
use common\entities\ContactCollection;
use common\entities\ViberMessage;
class TestController extends Controller
{

    public function actionAddPhone(){
        $phone = Yii::$app->mongodb->getCollection('phone');

        try{
            if(! $phone->insert([
                'contact_collection_id'=>1,
                'phone'=>79789877832,
                'clients_id'=>1,
                'username'=>'Tom'
            ]))
                throw new RuntimeException( json_encode($phone->errors));
        }catch (RuntimeException $ex){
            var_dump($ex->getMessage());
        }

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

    public function actionCheckViber(){
        $vm=new ViberMessage([
            'id'=>1,
            'user_id'=>8,
            'title'=>'Title',
            'text'=>'Привет, а вто теперь рассылка на 2 номера',
            'title_button' => 'Жми',
            'url_button' => 'http://bernik.ru',
            'image'=>'kanban.png',
            'type' => ViberMessage::TEXTBUTTONIMAGE,
                             ]);
        $v=new Viber($vm);
        $v->sendMessage();
    }
}