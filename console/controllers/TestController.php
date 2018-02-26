<?php

namespace console\controllers;

use common\entities\mongo\Message_Phone_List;
use common\entities\phone\Phone;
use common\entities\user\User;
use frontend\forms\ViberNotification;
use PHPUnit\Framework\MockObject\RuntimeException;
use yii\console\Controller;
use Yii;
use common\components\Viber;
use common\entities\ContactCollection;
use common\entities\ViberMessage;
use common\entities\ViberTransaction;
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
        $vm = ViberMessage::findOne(26);

        $v = new Viber($vm);
        //$v->prepareTransaction();
        $v->sendMessage();
    }

    public function actionCheckTransaction()
    {
        $h = new ViberCronHandler();
        $h->handle();
    }

    public function actionVb()
    {
        Yii::info('text', 'pixels');
        exit;

        $data = [
            'p_transaction_id' => '102',
            'sending_method' => 'viber',
            'msg_id' => '76d8e8d4-1404-11e8-947b-d66ab06d7258',
            'type' => 'seen',
        ];
        $vb_Note =new ViberNotification();
        $vb_Note->load($data,'');
        if ($vb_Note->validate()) {
            $viber_transaction =  ViberTransaction::findOne($vb_Note->p_transaction_id);
            if ($viber_transaction) {
                $viber_transaction->handleViberNotification($vb_Note);
            }
        } else {
            print_r($vb_Note->getErrors());
        }
    }

    public function actionHa1(){

        $lst = explode("\r", file_get_contents(__DIR__ .'/n.txt'));
        $cnt = 0;
        $cnt1 = 0;
        foreach($lst as $r){
            list($id,$dt,$from,$phone,$status,$msg,$type) = explode("\t", $r);
            if ($status =='Не доставлено'){
                $status = 'undelivered';
            }elseif ($status =='Доставлено'){
                $status = 'delivered';
            }elseif ($status =='Просмотрено'){
                $status = 'viewed';
            }

            $mls = Message_Phone_List::find()->where(['msg_id'=>trim($id)])->one();
            $cnt+=1;
            if ($mls){
                $cnt1+=1;
                $mls->setAttribute('status',$status);
                $mls->save();
            }
            echo "$id - $phone - $status $cnt   $cnt1\n";
        }
        echo count($lst);
        exit;
    }
    public function actionHa(){
        $txt= file_get_contents(__DIR__ .'/sm.txt');
        $lst = explode("\n", $txt);

        $cnt1 = 0;
        foreach($lst as $r){
            if (!$r){
                continue;
            }
            list($id,$status,$cnt) = explode(";", $r);

            if (trim($status) != 'undelivered' ){
                $mst = ViberTransaction::find()->where(['id'=>$id])->one();
                if ($mst) {
                    if (trim($status) != 'delivered') {
                        $mst->delivered = $cnt;
                        $mst->save();
                    } else {
                        if (trim($status) != 'viewed') {
                            $mst->viewed = $cnt;
                            $mst->save();
                        }
                    }
                }
            }


            $cnt1+=1;

            echo "$id -  $status  - $cnt   -- $cnt1\n";
        }

        exit;
    }
}