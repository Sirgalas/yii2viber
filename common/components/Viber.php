<?php
/**
 * Created by PhpStorm.
 * User: mike
 * Date: 09.02.2018
 * Time: 15:19
 */

namespace common\components;

use common\components\providers\ProviderFactory;
use common\entities\Balance;
use common\entities\mongo\Phone;
use common\entities\mongo\Message_Phone_List;
use common\entities\user\User;
use common\entities\ViberMessage;
use common\entities\ViberTransaction;

use Yii;
use yii\web\NotFoundHttpException;

class Viber
{
    public $phones;

    public $viber_message;

    public $image_id;

    public $image;

    public $debug = true;

    public $provider;

    public $viberQuery;

    /**
     * Viber constructor.
     *
     * @param \common\entities\ViberMessage $viber_message
     * @param array $phones
     * @throws \yii\web\NotFoundHttpException
     */
    public function __construct(ViberMessage $viber_message, array $phones = [])
    {
        $this->viber_message = $viber_message;
        if ($this->viber_message == null) {
            throw new NotFoundHttpException(Yii::t('app', 'The requested messages does not exist.'));
        }
        $this->image_id = 0;
        $this->phones = $phones;
    }

    private function writeToTextLog($result, $viber_transaction, $phones)
    {
        $path = \Yii::getAlias('@frontend').'/runtime/viber_report';
        $fileName = $path.'/query_'.$viber_transaction->id.'_'.date('Ymd_H').'.txt';
        file_put_contents($fileName, "\n".$this->viberQuery, FILE_APPEND);
        file_put_contents($fileName, "\n".'=================='.date('H:i:s').'====================', FILE_APPEND);
        if ($viber_transaction->status === 'error') {
            file_put_contents($fileName, "\n".'=================='.date('H:i:s').'====================', FILE_APPEND);
            file_put_contents($fileName, implode(',', $phones), FILE_APPEND);
            file_put_contents($fileName, "\n".'======================================', FILE_APPEND);
            file_put_contents($fileName, $result, FILE_APPEND);
            file_put_contents($fileName, "\n".'======================================', FILE_APPEND);
        }
    }

    /**
     *
     * @throws \Exception
     */
    public function sendMessage()
    {
        if ($this->viber_message->status !== ViberMessage::STATUS_PROCESS) {
            return false;
        }
        $viber_transaction = ViberTransaction::find()->isNew($this->viber_message->id)->one();
        if (! $viber_transaction) {
            return $this->viber_message->setWait();
        }
        $phonesArray = Message_Phone_List::find()->indexBy('phone')->where(['transaction_id' => $viber_transaction->id])->all();

        $phonesA = [];
        foreach ($phonesArray as $phone) {
            $phonesA[$phone->phone] = $phone;
        }
        if (! $phonesA) {
            $viber_transaction->status = 'error';
            $viber_transaction->save();

            return false;
        }
        // списание баланса
        $channel = $this->viber_message->channel;
        $user = User::find()->where(['id' => $this->viber_message->user_id])->one();
        if (!$user->checkBalance( $channel, \count($phonesA))) {
            $this->viber_message->setWaitPay();

            return false;
        }
        $balance=$user->balance;

        $balance->$channel -= \count($phonesA);
        $balance->validate();
        if (! $balance->save()) {
            Yii::error('Viber balance->save error  '.print_r($balance->getErrors()));
            throw new \RuntimeException('not save' . print_r($balance->getErrors()));
        }
        // Отправка сообщения
        $pf = new ProviderFactory();
        $provider = $pf->createProvider($this->viber_message);
        $provider->setMessage($this->viber_message);
        $result = $provider->sendToViber($phonesA, $viber_transaction->id);

        if ($provider->image_id) {
            $this->viber_message->viber_image_id = $provider->image_id;
            $this->viber_message->save();
        }

        if ($provider->parseSendResult( $phonesA)) {
            $viber_transaction->status = 'sended';
        } else {
            $viber_transaction->status = 'error';
            Yii::error($result);
        }
        $this->writeToTextLog($result, $viber_transaction, array_keys($phonesA));

        return $viber_transaction->save();
    }

    /**
     * @param array $phones
     * @throws \Exception
     */
    private function saveNewTransaction(array $phones)
    {
        $tVM = new ViberTransaction([
                                        'user_id' => $this->viber_message->user_id,
                                        'viber_message_id' => $this->viber_message->id,
                                        'status' => 'new',
                                        'size' => \count($phones),
                                        'created_at' => time(),
                                    ]);
        $tVM->save();
        Message_Phone_List::deleteAll(['transaction_id' => $tVM->id]);
        foreach ($phones as $i => $P) {
            $phones[$i]['transaction_id'] = $tVM->id;
        }
        // echo "\n created phones ", count($phones), ' transaction id=', $tVM->id;
        if (! Yii::$app->mongodb->getCollection(Message_Phone_List::collectionName())->batchInsert($phones)) {
            throw new \RuntimeException('not save');
        }
    }

    /**
     * Подготовка транзакций
     * генерация записей в таблице Viber_transaction :: разбиваем список
     * телефонов входящих в рассылку на блоки
     * и для каждого блока создаем заготовку транзакции
     *
     * @return bool
     * @throws \yii\db\Exception
     */
    public function prepareTransaction()
    {
        $db = Yii::$app->db;
        $transaction = $db->beginTransaction();
        $contact_collection_ids = $this->viber_message->getMessageContactCollections()->select(['contact_collection_id'])->distinct('contact_collection_id')->column();

        foreach ($contact_collection_ids as $k => $v) {
            if (\is_int($v)) {
                $contact_collection_ids[] = (string)$v;
            } else {
                $contact_collection_ids[] = (int)$v;
            }
        }
        try {
            if (\count($this->phones) > 0) {
                $phones = $this->phones;
            } else {
                $phones = Phone::find()->select(['phone'])->where([
                                                                      'in',
                                                                      'contact_collection_id',
                                                                      $contact_collection_ids,
                                                                  ])->distinct('phone');
            }
            //$user = User::find()->where(['id' => $this->viber_message->user_id])->one();
            $balance=Balance::find()->where(['user_id' => $this->viber_message->user_id])->one();
            $channel=$this->viber_message->channel;
            if ($balance->$channel < \count($phones)) {
                throw new \RuntimeException('balance is small, not save');
            }
            $tPhones = [];
            foreach ($phones as $phone) {
                $tPhones[] = ['phone' => $phone, 'status' => 'new', 'message_id' => $this->viber_message->id];
                if (\count($tPhones) >= Yii::$app->params[$this->viber_message->provider]['transaction_size_limit']) {
                    $this->saveNewTransaction($tPhones);
                    $tPhones = [];
                }
            }
            if (\count($tPhones) > 0) {
                $this->saveNewTransaction($tPhones);
            }
            $this->viber_message->status = ViberMessage::STATUS_PROCESS;
            $this->viber_message->save();
            $transaction->commit();

            return true;
        } catch (\Exception $e) {
            $transaction->rollBack();
            echo "\n Error ", $e->getMessage();

            return false;
        }
    }
}