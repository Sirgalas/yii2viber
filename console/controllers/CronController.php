<?php

namespace console\controllers;

use common\entities\mongo\Message_Phone_List;
use yii\console\Controller;
use Yii;
use common\components\Viber;
use common\entities\ViberMessage;
use common\entities\ViberTransaction;

class CronController extends Controller
{
    const VIBER_TIME_LIMIT = 30;

    private $time_stop;

    private function findTransactionInProcess($limit=200)
    {
        $wait_ids = ViberMessage::find()->where(['status' => 'wait'])->select("id")->limit(3)->orderBy('id')->column();

        return ViberTransaction::find()
            ->where(['!=', 'status', 'ready'])
            ->andWhere(['in', 'viber_message_id', $wait_ids,])
            ->select(['viber_message_id'])
            ->limit($limit)
            ->distinct()
            ->column();
    }

    /**
     * Иногда в сообщениях сразу приходит статус прочитано, и врмя доставки тогда не устанавливается
     * В этой процедуре, ищем просмотренный сообщения, вермя доставки которых не установлено
     * и устанавливаем время доставки
     */
    public function actionFixDateDelivered()
    {
        while (true) {
            $cnt = 0;
            foreach (Message_Phone_List::find()
                         ->where(['status' => Message_Phone_List::VIEWED])
                         ->andWhere(["date_delivered" => ['$not' => ['$exists' => true]]])
                         ->andWhere(["date_viwed" => ['$exists' => true]])
                         ->batch(300) as $phone) {
                $phone->date_delivered = $phone->date_viewed;
                $phone->save();
                $cnt += 1;
            };
            if ($cnt < 200) {
                break;
            }
        }
    }

    public function actionTransactionResults()
    {

        $ids        = $this->findTransactionInProcess(100);
        $collection = Yii::$app->mongodb->getCollection(Message_Phone_List::collectionName());
        $results     = $collection->aggregate([['$match' => ['transaction_id' => ['$in' => $ids]]],
                                              ['$group' => ['_id' => ['"transaction_id"' => '$transaction_id',
                                                                      '"status"'         => '$status',],
                                                            'cnt' => ['$sum' => 1],],],]);

        if ($results){
            $transactions = ViberTransaction::find()->where(['in','id',$ids])->indexBy('id')->limit(100)->all();
            foreach ($results as $result){
                if ($result['_id']['status'] === Message_Phone_List::DELIVERED ||
                $result['_id']['status'] === Message_Phone_List::VIEWED ) {
                    $transactions[$result['_id']['transaction_id']][$result['_id']['status']] = $result['cnt'];
                }
            }
            foreach ($transactions as $transaction){
                $transaction->checkStatus();
                $transaction->save();
            }
        }

    }

    /**
     * Обработка рассылок и транзакций, отправка которых началась, но нет данных о завершении
     */
    public function actionMarkWaitAsReady()
    {


        $wait_ids = ViberMessage::find()->where(['status' => 'wait'])->select("id")->limit(3)->orderBy('id')->column();

        $ids = ViberTransaction::find()
            ->where(['!=', 'status', 'ready'])
            ->andWhere(['in', 'viber_message_id', $wait_ids,])
            ->select(['viber_message_id'])
            ->distinct()
            ->column();

        $id_ready = array_diff($wait_ids, $ids);

        $wait_ids2 = ViberMessage::find()
            ->where(['status' => 'wait'])
            ->andWhere("date_send_finish + coalesce(dlr_timeout, 24*3600) < ".time())
            ->select("id")
            ->limit(3)
            ->orderBy('id')
            ->column();

        $id_ready = array_merge($id_ready, $wait_ids2);

        $r = ViberMessage::updateAll(['status' => 'ready'], ['in', 'id', $id_ready]);
        echo $r;
    }

    public function ViberQueueHandle()
    {

        $this->actionMarkWaitAsReady();

        $this->time_stop = time() + self::VIBER_TIME_LIMIT;

        while ($this->time_stop > time()) {
            $vm = ViberMessage::find()->isProcess()->one();
            if ($vm) {
                echo "\nfound $vm->id";
            }
            if (! $vm) {
                echo " Vm for process not found\n";
                $id = ViberMessage::find()
                    ->select('viber_message.id')
                    ->joinWith('user')
                    ->where('"user"."balance" >= "viber_message"."cost"')
                    ->isNew()
                    ->scalar();
                if (! $id) {
                    echo 'Queue is empty !';

                    return;
                }
                $vm = ViberMessage::findOne($id);
                $v  = new Viber($vm);
                $v->prepareTransaction();
            }

            if ($vm) {
                echo 'SEND ', $vm->id, $vm->title, $vm->user_id;
                $v = new Viber($vm);
                $v->sendMessage();
            }

            sleep(Yii::$app->params['smsonline']['min_delay']);
        }
    }

    public function actionViberQueueHandle()
    {
        return $this->ViberQueueHandle();
    }

    public function actionTestViberQueueHandle()
    {

        Yii::$app->params['smsonline'] = Yii::$app->params['viber-test'];

        return $this->ViberQueueHandle();
    }
}