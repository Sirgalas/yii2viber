<?php

namespace console\controllers;

use common\components\providers\infobip\InfoBipScenario;
use common\components\providers\ProviderFactory;
use common\entities\mongo\Message_Phone_List;
use yii\console\Controller;
use Yii;
use common\components\Viber;
use common\entities\ViberMessage;
use common\entities\ViberTransaction;

class IbController extends Controller
{
    const VIBER_TIME_LIMIT = 30;

    private $time_stop;

    public function actionScenario()
    {

        $vm = ViberMessage::find()->where(['in', 'id', ['109']])->one();
        $pf = new ProviderFactory();
        $provider = $pf->createProvider($vm);
        $IBScenario = new InfoBipScenario($vm, Yii::$app->params['infobip']);
        if ($IBScenario->defineScenario()) {
            $scenario = $IBScenario->getScenario();
            print_r($scenario->getAttributes());
            $vm->scenario_id = $scenario->id;
            $vm->save();
        } else {
            echo "\nError ".$IBScenario->getError();
        }
    }

    public function actionScenarios()
    {
        $vm = ViberMessage::find()->where(['in', 'id', ['111']])->one();
        $IBScenario = new InfoBipScenario($vm, Yii::$app->params['infobip']);
        $IBScenario->getScenarios('D7D60E85152AC4C18E0B28EBBE2A3884');
    }

    public function actionSend()
    {
        $message_id = 88;
        $vm = ViberMessage::findOne($message_id);

        $vm->status = 'new';
        if (! $vm->save()) {
            die('Ошибка сохранения ViberMessage '.print_r($vm->getErrors(), 1));
        }
        Message_Phone_List::deleteAll(['message_id' => $message_id]);
        ViberTransaction::deleteAll(['viber_message_id' => $message_id]);
        $v = new Viber($vm);
        $v->prepareTransaction();
        exit;
        $viber_transaction = ViberTransaction::find()->isNew($vm->id)->one();
        if (! $viber_transaction) {
            die('Транзакция не найдена');
        }
        $phonesArray = Message_Phone_List::find()->indexBy('phone')->where(['transaction_id' => $viber_transaction->id])->all();

        $phonesA = [];
        foreach ($phonesArray as $phone) {
            $phonesA[$phone->phone] = $phone;
        }

        $pf = new ProviderFactory();
        $provider = $pf->createProvider($vm);

        $provider->setMessage($vm);

        if (! $provider->sendToViber($phonesA, $viber_transaction->id)) {
            print_r($provider->err);
            die('Ошибка отправки');
        }
        echo "\n ANSWER======== \n";
        print_r($provider->answer);

        $provider->parseSendResult($phonesA);
    }

    public function actionAnswer()
    {
        $message_id = 111;
        $vm = ViberMessage::find()->where(['in', 'id', [$message_id]])->one();
        $pf = new ProviderFactory();
        $provider = $pf->createProvider($vm);

        $viber_transaction = ViberTransaction::find()->where(['viber_message_id' => $vm->id])->one();
        if (! $viber_transaction) {
            die('Транзакция не найдена');
        }
        $phonesArray = Message_Phone_List::find()->indexBy('phone')->where(['transaction_id' => $viber_transaction->id])->all();

        $phonesA = [];
        foreach ($phonesArray as $phone) {
            $phonesA[$phone->phone] = $phone;
        }

        $provider->setMessage($vm);
        $provider->answer = '{"bulkId":"201","messages":[{"to":{"phoneNumber":"79135701037"},"status":{"groupId":1,"groupName":"PENDING","id":7,"name":"PENDING_ENROUTE","description":"Message sent to next instance"},"messageId":"5a9bdf2442914a8564002df2"}]}';
        $provider->parseSendResult($phonesA);
    }

    public function actionReport()
    {
        $message_id = 118;

        $vm = ViberMessage::find()->where(['in', 'id', [$message_id]])->one();
        $pf = new ProviderFactory();
        $provider = $pf->createProvider($vm);
        $provider->getDeliveryReport();
    }

    public function actionParseReport()
    {


        //$r = Yii::$app->mongodb->createCommand()//->addUpdate(['status'=>'viewed'],['status'=>'sended', 'date_delivered'=>null, 'date_viewed'=>null])
        //->addUpdate([
        //                [
        //                    'date_delivered' => ['$not' => ['$exists' => true]],
        //                    'status' => 'sended',
        //                ],
        //            ], ['status' => 'sendedddd'])->executeBatch(Message_Phone_List::collectionName());

        $vm = ViberMessage::find()->where(['in', 'status', ['wait', 'process']])->one();
        if (! $vm) {
            echo 'No distribution messages';

            return;
        }
        $pf = new ProviderFactory();
        $provider = $pf->createProvider($vm);
        $provider->answer = $this->getReportData2();
        $provider->parseDeliveryReport();
    }

    private  function getReportData2(){
        return '{"results":[{"bulkId":"872","messageId":"5aa023d245625853e857b9b2","to":"79135701037","sentAt":"2018-03-07T17:39:33.693+0000","doneAt":"2018-03-07T17:39:35.285+0000","messageCount":1,"mccMnc":"null","price":{"pricePerMessage":0E-10,"currency":"EUR"},"status":{"groupId":3,"groupName":"DELIVERED","id":5,"name":"DELIVERED_TO_HANDSET","description":"Message delivered to handset"},"error":{"groupId":0,"groupName":"OK","id":0,"name":"NO_ERROR","description":"No Error","permanent":false},"channel":"VIBER"}]}';
    }

    private  function getReportData3(){
        return '{"results":[{"bulkId":"201","messageId":"5a9bdf2442914a8564002df2","to":"79135701037","sentAt":"2018-03-07T09:10:07.433+0000","doneAt":"2018-03-07T09:10:09.314+0000","messageCount":1,"mccMnc":"null","price":{"pricePerMessage":0E-10,"currency":"EUR"},"status":{"groupId":3,"groupName":"DELIVERED","id":5,"name":"DELIVERED_TO_HANDSET","description":"Message delivered to handset"},"error":{"groupId":0,"groupName":"OK","id":0,"name":"NO_ERROR","description":"No Error","permanent":false},"channel":"VIBER"},{"bulkId":"870","messageId":"5a9fbaba4562587d8f774012","to":"79135701037","sentAt":"2018-03-07T10:17:15.877+0000","doneAt":"2018-03-07T10:17:17.893+0000","messageCount":1,"mccMnc":"null","price":{"pricePerMessage":0E-10,"currency":"EUR"},"status":{"groupId":3,"groupName":"DELIVERED","id":5,"name":"DELIVERED_TO_HANDSET","description":"Message delivered to handset"},"error":{"groupId":0,"groupName":"OK","id":0,"name":"NO_ERROR","description":"No Error","permanent":false},"channel":"VIBER"},{"bulkId":"870","messageId":"5a9fbaba4562587d8f774013","to":"79788161626","sentAt":"2018-03-07T10:17:15.890+0000","doneAt":"2018-03-07T10:17:19.835+0000","messageCount":1,"mccMnc":"null","price":{"pricePerMessage":0E-10,"currency":"EUR"},"status":{"groupId":3,"groupName":"DELIVERED","id":5,"name":"DELIVERED_TO_HANDSET","description":"Message delivered to handset"},"error":{"groupId":0,"groupName":"OK","id":0,"name":"NO_ERROR","description":"No Error","permanent":false},"channel":"VIBER"},{"bulkId":"870","messageId":"5a9fbaba4562587d8f774012","to":"79135701037","sentAt":"2018-03-07T10:42:10.697+0000","doneAt":"2018-03-07T10:42:12.836+0000","messageCount":1,"mccMnc":"null","price":{"pricePerMessage":0E-10,"currency":"EUR"},"status":{"groupId":3,"groupName":"DELIVERED","id":5,"name":"DELIVERED_TO_HANDSET","description":"Message delivered to handset"},"error":{"groupId":0,"groupName":"OK","id":0,"name":"NO_ERROR","description":"No Error","permanent":false},"channel":"VIBER"},{"bulkId":"870","messageId":"5a9fbaba4562587d8f774013","to":"79788161626","sentAt":"2018-03-07T10:42:11.120+0000","doneAt":"2018-03-07T10:42:13.762+0000","messageCount":1,"mccMnc":"null","price":{"pricePerMessage":0E-10,"currency":"EUR"},"status":{"groupId":3,"groupName":"DELIVERED","id":5,"name":"DELIVERED_TO_HANDSET","description":"Message delivered to handset"},"error":{"groupId":0,"groupName":"OK","id":0,"name":"NO_ERROR","description":"No Error","permanent":false},"channel":"VIBER"},{"bulkId":"871","messageId":"5aa016e445625848c85f5602","to":"79135701037","sentAt":"2018-03-07T16:44:21.167+0000","doneAt":"2018-03-07T16:44:21.714+0000","messageCount":1,"mccMnc":"null","price":{"pricePerMessage":0E-10,"currency":"EUR"},"status":{"groupId":3,"groupName":"DELIVERED","id":5,"name":"DELIVERED_TO_HANDSET","description":"Message delivered to handset"},"error":{"groupId":0,"groupName":"OK","id":0,"name":"NO_ERROR","description":"No Error","permanent":false},"channel":"VIBER"}]}';
    }
    private function getReportData()
    {

        return '{"results":[{"messageId":"7763ffd0-efdf-4f83-b178-4f9b46e533b3","to":"79663396630","sentAt":"2018-03-06T08:01:15.720+0000","doneAt":"2018-03-06T08:01:23.273+0000","messageCount":1,"mccMnc":"null","price":{"pricePerMessage":0E-10,"currency":"EUR"},"status":{"groupId":3,"groupName":"DELIVERED","id":5,"name":"DELIVERED_TO_HANDSET","description":"Message delivered to handset"},"error":{"groupId":0,"groupName":"OK","id":0,"name":"NO_ERROR","description":"No Error","permanent":false},"channel":"VIBER"},{"messageId":"ca0fafad-4502-4f1d-af07-a7ba0b28813c","to":"79135701037","sentAt":"2018-03-06T08:07:56.553+0000","doneAt":"2018-03-06T08:13:28.670+0000","messageCount":1,"mccMnc":"null","price":{"pricePerMessage":0E-10,"currency":"EUR"},"status":{"groupId":3,"groupName":"DELIVERED","id":5,"name":"DELIVERED_TO_HANDSET","description":"Message delivered to handset"},"error":{"groupId":0,"groupName":"OK","id":0,"name":"NO_ERROR","description":"No Error","permanent":false},"channel":"VIBER"},{"messageId":"c54a1128-8033-424f-97ce-baed03dc6499","to":"79135701037","sentAt":"2018-03-05T23:24:37.700+0000","doneAt":"2018-03-06T08:13:28.902+0000","messageCount":1,"mccMnc":"null","price":{"pricePerMessage":0E-10,"currency":"EUR"},"status":{"groupId":3,"groupName":"DELIVERED","id":5,"name":"DELIVERED_TO_HANDSET","description":"Message delivered to handset"},"error":{"groupId":0,"groupName":"OK","id":0,"name":"NO_ERROR","description":"No Error","permanent":false},"channel":"VIBER"},{"messageId":"61d0cbe1-954d-4c51-9ef2-17046dd24ed0","to":"79135701037","sentAt":"2018-03-05T23:38:18.420+0000","doneAt":"2018-03-06T08:13:28.909+0000","messageCount":1,"mccMnc":"null","price":{"pricePerMessage":0E-10,"currency":"EUR"},"status":{"groupId":3,"groupName":"DELIVERED","id":5,"name":"DELIVERED_TO_HANDSET","description":"Message delivered to handset"},"error":{"groupId":0,"groupName":"OK","id":0,"name":"NO_ERROR","description":"No Error","permanent":false},"channel":"VIBER"},{"messageId":"2c924600-5e4d-4435-8435-bbab24579576","to":"79135701037","sentAt":"2018-03-05T23:33:10.843+0000","doneAt":"2018-03-06T08:13:28.916+0000","messageCount":1,"mccMnc":"null","price":{"pricePerMessage":0E-10,"currency":"EUR"},"status":{"groupId":3,"groupName":"DELIVERED","id":5,"name":"DELIVERED_TO_HANDSET","description":"Message delivered to handset"},"error":{"groupId":0,"groupName":"OK","id":0,"name":"NO_ERROR","description":"No Error","permanent":false},"channel":"VIBER"},{"messageId":"3d874823-9bdd-4e2a-b280-47b8bbf14bea","to":"79135701037","sentAt":"2018-03-05T23:30:08.177+0000","doneAt":"2018-03-06T08:13:28.905+0000","messageCount":1,"mccMnc":"null","price":{"pricePerMessage":0E-10,"currency":"EUR"},"status":{"groupId":3,"groupName":"DELIVERED","id":5,"name":"DELIVERED_TO_HANDSET","description":"Message delivered to handset"},"error":{"groupId":0,"groupName":"OK","id":0,"name":"NO_ERROR","description":"No Error","permanent":false},"channel":"VIBER"},{"bulkId":"215","messageId":"5a9dd10b42914a0c8d003c72","to":"79135701037","sentAt":"2018-03-05T23:22:39.703+0000","doneAt":"2018-03-06T08:13:30.069+0000","messageCount":1,"mccMnc":"null","price":{"pricePerMessage":0E-10,"currency":"EUR"},"status":{"groupId":3,"groupName":"DELIVERED","id":5,"name":"DELIVERED_TO_HANDSET","description":"Message delivered to handset"},"error":{"groupId":0,"groupName":"OK","id":0,"name":"NO_ERROR","description":"No Error","permanent":false},"channel":"VIBER"}]}';
    }
}