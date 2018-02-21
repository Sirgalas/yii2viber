<?php

namespace console\controllers;


use yii\console\Controller;
use Yii;
use common\components\Viber;
use common\entities\ViberMessage;

class CronController extends Controller
{

    const VIBER_TIME_LIMIT =10;
    private $time_stop;


    public function actionViberQueueHandle(){
        $this->time_stop = time() + self::VIBER_TIME_LIMIT;
        //file_put_contents(__DIR__ . '\cron_log_' . date('ymd').'log', 'started ' . date('H:i:s'). "\n" , FILE_APPEND);
        while ($this->time_stop > time()){
            $vm='';
            $vm = ViberMessage::find()->isProcess()->one();
            echo 'found $vm->id';
            if ($vm && $vm->id == 24){
                $vm->status=ViberMessage::STATUS_CANCEL;
                $vm->save();
                print_r($vm->getErrors());
                continue;
            }
            if (!$vm){
                echo " Vm for process not found\n";
                $vm = ViberMessage::find()->isNew()->one();
                if (!$vm){
                    echo 'Queue is empty !';
                    return;
                }
                $v=new Viber($vm);
                $v->prepareTransaction();
            }

            if ($vm){
                echo 'SEND ' , $vm->id , $vm->title , $vm->user_id ;
                $v=new Viber($vm);
                $v->sendMessage();
            }
            sleep(Yii::$app->params['viber']['min_delay']);
        }
    }
}