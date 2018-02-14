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
use  common\services\ViberCronHandler;
use yii\httpclient\XmlParser;

class TestController extends Controller
{
    public function actionAddPhone()
    {
        $phone = Yii::$app->mongodb->getCollection('phone');

        try {
            if (! $phone->insert([
                'contact_collection_id' => 1,
                'phone' => 79789877832,
                'clients_id' => 1,
                'username' => 'Tom',
            ])) {
                throw new RuntimeException(json_encode($phone->errors));
            }
        } catch (RuntimeException $ex) {
            var_dump($ex->getMessage());
        }
    }

    public function actionAddMessage()
    {
        $message_phone_list = Yii::$app->mongodb->getCollection('message_phone_list');

        try {
            if (! $message_phone_list->insert([
                'message_id' => 1,
                'last_date_message' => 79789877878,
                'status' => 1,
            ])) {
                throw new RuntimeException(json_encode($message_phone_list->errors));
            }
        } catch (RuntimeException $ex) {
            var_dump($ex->getMessage());
        }
    }

    public function actionCheckUserRecursive()
    {
        $user = User::findOne(8);
        echo 'actionCheckUserRecursive 8 & 9, result= ', $user->amParent(9);
        echo "\n", '    actionCheckUserRecursive 8 & 10    result=', $user->amParent(10);
    }

    public function actionCheckViber()
    {
        $vm = new ViberMessage([
            'id' => 1,
            'user_id' => 8,
            'title' => 'Title',
            'text' => 'Привет, а вто теперь рассылка на 2 номера',
            'title_button' => 'Жми',
            'url_button' => 'http://bernik.ru',
            'image' => 'kanban.png',
            'type' => ViberMessage::TEXTBUTTONIMAGE,
        ]);
        $v = new Viber($vm);
        $v->sendMessage();
    }

    public function actionCheckTransaction()
    {
        $h = new ViberCronHandler();
        $h->handle();
    }

    public function actionXML(){
        $xml="<response>
<code>0</code>
<tech_message>OK</tech_message>
<msg_id phone=\"79135701037\">7a987f56-1158-11e8-bad9-fc05600af937</msg_id>

</response>
";


        echo $xml;
        Yii::info($xml,'Viber');exit;
        try {

            if (is_string($xml)) {

                $xml = simplexml_load_string($xml);
            }
            echo "\ncode ",$xml->code;
            echo "\ntech_message ",$xml->tech_message;
             foreach ($xml->msg_id as $key => $msg){
               echo "\n-------";

             foreach ($msg->attributes() as $k=>$v){
                 echo "\n aaa[$k] == $v \n";
             };
             $a=$msg->attributes();
               echo $a['phone'];
               echo((string)$msg);
             }

             //var_dump($xml->msg_id[1]);
            echo '=====================';

            return true;
        } catch (\Exception $e) {
            echo $e->getMessage();
            return false;
        }
    }
}