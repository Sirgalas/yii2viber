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


    public function  actionScenario()
    {

        $vm = ViberMessage::find()
            ->where(['in','id',['109']])->one();
        $pf = new ProviderFactory();
        $provider=$pf->createProvider($vm);
        $IBScenario = new InfoBipScenario($vm, Yii::$app->params['infobip']);
        if ($IBScenario->defineScenario()){
            $scenario=$IBScenario->getScenario();
            print_r($scenario->getAttributes());
            $vm->scenario_id = $scenario->id;
            $vm->save();
        } else {
            echo "\nError " . $IBScenario->getError();
        }

    }

    public function  actionSend()
    {
        $message_id=111;
        $vm = ViberMessage::find()
            ->where(['in','id',[$message_id]])->one();
        $vm->status='new';
        if (!$vm->save()){
            die('Ошибка сохранения ViberMessage '. print_r($vm->getErrors(),1));
        }
        Message_Phone_List::deleteAll(['message_id'=>$message_id]);
        ViberTransaction::deleteAll(['viber_message_id'=>$message_id]);
        $v = new Viber($vm);
        $v->prepareTransaction();

        $viber_transaction = ViberTransaction::find()->isNew($vm->id)->one();
        if (!$viber_transaction){
            die('Транзакция не найдена');
        }
        $phonesArray = Message_Phone_List::find()->indexBy('phone')->where(['transaction_id' => $viber_transaction->id])->all();

        $phonesA = [];
        foreach ($phonesArray as $phone) {
            $phonesA[$phone->phone] = $phone;
        }

        $pf = new ProviderFactory();
        $provider=$pf->createProvider($vm);

        $provider->setMessage($vm);

        if (!$provider->sendToViber($phonesA, $viber_transaction->id)){
           print_r($provider->err);
           die('Ошибка отправки');
        }
        echo "\n ANSWER======== \n";
        print_r($provider->answer);

        $provider->parseSendResult($phonesA);
    }


}