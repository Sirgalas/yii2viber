<?php

namespace console\controllers;

use common\entities\mongo\Message_Phone_List;
use yii\console\Controller;
use Yii;
use common\components\Viber;
use common\entities\ViberMessage;
use common\entities\ViberTransaction;
use common\components\providers\ProviderFactory;

class CronController extends Controller
{
    const VIBER_TIME_LIMIT = 30;

    private $time_stop;

    /**
     * возвращает массив id незавершенных транзакций
     *
     * @param int $limit
     * @return array
     */
    private function findTransactionInProcess($limit = 200)
    {
        $wait_ids = ViberMessage::find()
            ->where(['status' => 'wait'])
            ->andWhere(['channel' => 'viber'])
            ->select("id")
            ->limit(3)
            ->orderBy('id')
            ->column();

        return ViberTransaction::find()
            ->where(['!=', 'status', 'ready'])
            ->andWhere([
                           'in',
                           'viber_message_id',
                           $wait_ids,])
            ->select(['id'])
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
                         ->andWhere(['>', 'date_viewed', 10000])
                         ->limit(300)
                         ->all() as $phone) {

                $phone->date_delivered = $phone->date_viewed;
                $phone->save();
                $cnt += 1;
            };
            if ($cnt < 200) {
                break;
            }
        }
    }

    /**
     * Для незавершенных транзакций обходим списки телефонов, уточнем количество доставленных\прочитанных
     * записываем это количество в транзакцию
     * если все доставлено, меняем статус транзакции
     */
    public function actionTransactionResults()
    {
        echo 'actionTransactionResults started';
        $ids = $this->findTransactionInProcess(100);
        print_r($ids);
        if (! $ids || count($ids) == 0) {
            return;
        }
        $collection = Yii::$app->mongodb->getCollection(Message_Phone_List::collectionName());

        $results = $collection->aggregate([
                                              ['$match' => ['transaction_id' => ['$in' => $ids]]],
                                              [
                                                  '$group' => [
                                                      '_id' => [
                                                          'transaction_id' => '$transaction_id',
                                                          'status'         => '$status',],
                                                      'cnt' => ['$sum' => 1],],],]);

        print_r($results);
        if ($results) {
            $transactions = ViberTransaction::find()->where(['in', 'id', $ids])->indexBy('id')->all();
            $undelivers   = [];
            $deliveres    = [];
            foreach ($results as $result) {
                if (! isset($transactions[$result['_id']['transaction_id']])) {
                    continue;
                }
                if ($result['_id']['status'] === Message_Phone_List::DELIVERED || $result['_id']['status'] === Message_Phone_List::VIEWED) {
                    $transactions[$result['_id']['transaction_id']][$result['_id']['status']] = $result['cnt'];
                    $deliveres[]                                                              = [$result['_id']['transaction_id']];
                }
                if ($result['_id']['status'] === Message_Phone_List::UNDELIVERED) {
                    $undelivers[$result['_id']['transaction_id']] = $result['cnt'];
                }

                print_r([
                            'result'      => $result,
                            '$deliveres'  => $deliveres,
                            '$undelivers' => $undelivers,

                        ]);
            }

            foreach ($transactions as $transaction) {
                $und = isset($undelivers[$transaction->id]) ? $undelivers[$transaction->id] : 0;
                $transaction->checkReady($und);
                $transaction->save();
            }
        }
    }

    /**
     * Обработка рассылок и транзакций, отправка которых началась, но нет данных о завершении
     * если у рассылки все транзакции завершены, или прошли таймоуты, то выставляем рассылку в ready
     */
    public function actionMarkWaitAsReady()
    {


        $wait_ids = ViberMessage::find()->where(['status' => 'wait'])->select("id")->limit(3)->orderBy('id')->column();

        $ids = ViberTransaction::find()
            ->where(['!=', 'status', 'ready'])
            ->andWhere([
                           'in',
                           'viber_message_id',
                           $wait_ids,])
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

    /**
     * Опрашиваем провайдеров. Получаем и обрабатываем отчет
     */
    public function actionLoadReports()
    {
        $vm = ViberMessage::find()
            ->where(['in', 'status', ['wait', 'process']])
            ->andWhere(['in','channel' ,['viber','sms']])
            ->one();
        if (! $vm) {
            echo 'No distribution messages';

            return;
        }
        $pf       = new ProviderFactory();
        $provider = $pf->createProvider($vm);
        $provider->getDeliveryReport();
        $provider->parseDeliveryReport();
    }

    public function ViberQueueHandle()
    {
        $this->actionMarkWaitAsReady();

        $this->time_stop = time() + self::VIBER_TIME_LIMIT;

        while ($this->time_stop > time()) {
            $vm = ViberMessage::find()->isProcess()->andWhere(['channel' => 'viber'])->one();
            if ($vm) {
                echo "\nfound $vm->id";
            }
            if (! $vm) {
                echo " Vm for process not found\n";
                $id = ViberMessage::find()
                    ->select('viber_message.id')
                    ->joinWith('user')
                    ->rightJoin('balance', 'balance.user_id = "user".id')
                    ->where('"balance"."viber" >= "viber_message"."cost"')
                    ->andWhere(['channel' => 'viber'])
                    ->isNew()
                    ->scalar();
                if (! $id) {
                    $id = ViberMessage::find()
                        ->select('viber_message.id')
                        ->joinWith('user')
                        ->rightJoin('balance', 'balance.user_id = "user".id')
                        ->where('"balance"."whatsapp" >= "viber_message"."cost"')
                        ->andWhere(['channel' => 'whatsapp'])
                        ->isNew()
                        ->scalar();
                }
                if (! $id) {
                    echo 'Queue is empty !';

                    return;
                }

                $vm = ViberMessage::findOne($id);
                $v  = new Viber($vm);
                $v->prepareTransaction();
            }

            if ($vm && $vm->channel == 'viber') {
                echo 'SEND ', $vm->id, $vm->title, $vm->user_id;
                $v = new Viber($vm);
                $v->sendMessage();
            }

            sleep(1);
        }
    }
    public function SmsQueueHandle()
    {
        $this->actionMarkWaitAsReady();

        $this->time_stop = time() + self::VIBER_TIME_LIMIT;

        while ($this->time_stop > time()) {
            $vm = ViberMessage::find()->isProcess()->andWhere(['channel' => 'sms'])->one();
            if ($vm) {
                echo "\nfound $vm->id";
            }
            if (! $vm) {
                echo " Vm for process not found\n";
                $id = ViberMessage::find()
                    ->select('viber_message.id')
                    ->joinWith('user')
                    ->rightJoin('balance', 'balance.user_id = "user".id')
                    ->where('"balance"."sms" >= "viber_message"."cost"')
                    ->andWhere(['channel' => 'sms'])
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

            if ($vm && $vm->channel == 'sms') {

                echo 'SEND ', $vm->id, $vm->title, $vm->user_id;

                $v = new Viber($vm);

                $v->sendMessage();
            }

            sleep(1);
        }
    }
    public function actionViberQueueHandle()
    {
        $this->ViberQueueHandle();
        $this->SmsQueueHandle();
    }

    public function actionTestViberQueueHandle()
    {

        $this->SmsQueueHandle();
    }
}