<?php

namespace console\controllers;

use common\components\providers\infobip\InfoBipScenario;
use common\components\providers\ProviderFactory;
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
        Yii::warning('test123456','viber');
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



}