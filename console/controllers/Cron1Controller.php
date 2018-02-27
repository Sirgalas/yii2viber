<?php

namespace console\controllers;

use yii\console\Controller;
use Yii;
use common\components\Viber;
use common\entities\ViberMessage;
use common\entities\ViberTransaction;

class CronController extends Controller
{
    const VIBER_TIME_LIMIT = 30;

    private $time_stop;

    public function actionMarkWaitAsReady()
    {

        $wait_ids = ViberMessage::find()->where(['status' => 'wait'])->select("id")->limit(3)->orderBy('id')->column();

        $ids = ViberTransaction::find()->where(['!=', 'status', 'ready'])->andWhere([
            'in',
            'viber_message_id',
            $wait_ids,
        ])->select(['viber_message_id'])->distinct()->column();

        $id_ready = array_diff($wait_ids, $ids);

        $wait_ids2 = ViberMessage::find()->where(['status' => 'wait'])->andWhere("date_send_finish + coalesce(dlr_timeout, 24*3600) < ".time())->select("id")->limit(3)->orderBy('id')->column();

        $id_ready = array_merge($id_ready, $wait_ids2);

        $r = ViberMessage::updateAll(['status' => 'ready'], ['in', 'id', $id_ready]);
        echo $r;
    }

    public function ViberQueueHandle() {

        $this->actionMarkWaitAsReady();

        $this->time_stop = time() + self::VIBER_TIME_LIMIT;

        while ($this->time_stop > time()) {

            $vm = '';
            $vm = ViberMessage::find()->isProcess()->one();
            if ($vm) {
                echo "\nfound $vm->id";
            }
            if (! $vm) {
                echo " Vm for process not found\n";
                $id = ViberMessage::find()->select('viber_message.id')
                    ->joinWith('user')
                    ->where('"user"."balance" >= "viber_message"."cost"')
                    ->isNew()->scalar();
                if (! $id) {
                    echo 'Queue is empty !';

                    return;
                }
                $vm=ViberMessage::findOne($id);
                $v = new Viber($vm);
                $v->prepareTransaction();
            }

            if ($vm) {
                echo 'SEND ', $vm->id, $vm->title, $vm->user_id;
                $v = new Viber($vm);
                $v->sendMessage();
            }

            sleep(Yii::$app->params['viber']['min_delay']);
        }
    }

    public function  actionViberQueueHandle()
    {
        return $this->ViberQueueHandle();
    }

    public function  actionTestViberQueueHandle()
    {
        Yii::$app->params['viber'] = Yii::$app->params['viber-test'];
        return $this->ViberQueueHandle();
    }

}