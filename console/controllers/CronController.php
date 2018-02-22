<?php

namespace console\controllers;


use yii\console\Controller;
use Yii;
use common\components\Viber;
use common\entities\ViberMessage;
use common\entities\ViberTransaction;

class CronController extends Controller
{

    const VIBER_TIME_LIMIT =30;
    private $time_stop;


    public function actionMarkWaitAsReady(){

        $wait_ids=ViberMessage::find()->where(['status'=>'wait'])->select("id")->limit(3)->orderBy('id')->column();

        $ids = ViberTransaction::find()->where(['!=','status','ready' ])->andWhere(['in', 'viber_message_id', $wait_ids])->select(['viber_message_id'])->distinct()->column();

        $id_ready =array_diff($wait_ids, $ids);

        $wait_ids2 = ViberMessage::find()->where(['status'=>'wait'])
            ->andWhere("date_send_finish + coalesce(dlr_timeout, 24*3600) < " . time())
            ->select("id")->limit(3)->orderBy('id')->column();

        $id_ready=array_merge($id_ready, $wait_ids2);

        $r = ViberMessage::updateAll(['status'=>'ready'], ['in', 'id', $id_ready]);
        echo $r;
    }
    public function actionViberQueueHandle(){
        $this->actionMarkWaitAsReady();
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