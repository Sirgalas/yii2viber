<?php

namespace frontend\controllers;


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

            if (!$vm){
                echo " Vm for process not found\n";
                $vm = ViberMessage::find()->isNew()->one();
                if (!$vm){
                    echo 'Нечего отправлять!';
                    return;
                }
                $v=new Viber($vm);
                $v->prepareTransaction();
            }

            if ($vm){
                echo 'Отправляем ' , $vm->title , $vm->user_id ;
                $v=new Viber($vm);
                $v->sendMessage();
            }
            sleep(Yii::$app->params['smsonline']['min_delay']);
        }
    }
}