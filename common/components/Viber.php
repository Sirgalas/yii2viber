<?php
/**
 * Created by PhpStorm.
 * User: mike
 * Date: 09.02.2018
 * Time: 15:19
 */

namespace common\components;

use common\entities\mongo\Phone;
use common\entities\mongo\Message_Phone_List;
use common\entities\user\User;
use common\entities\ViberMessage;
use common\entities\ContactCollection;
use common\entities\ViberTransaction;
use Yii;

class Viber
{
    public $phones;

    public $viber_message;

    public $image_id;

    public $image;

    public $debug = true;

    public $viberQuery;

    /**
     * Viber constructor.
     *
     * @param \common\entities\ViberMessage $viber_message
     * @param array $phones
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

    /**
     * Посылаем изображение, предварительным запросом.
     *
     * @return bool
     */
    public function sendImage()
    {

        $filePath = realpath($this->viber_message->getUploadedFile());
        $sign = md5(Yii::$app->params['viber']['login'].md5_file($filePath).Yii::$app->params['viber']['secret']);
        $ch = curl_init('http://media.sms-online.com/upload/');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_VERBOSE, $this->debug);
        curl_setopt($ch, CURLOPT_POSTFIELDS, [
            'login' => Yii::$app->params['viber']['login'],
            'image' => new  \CURLFile(realpath($filePath)),
            'sign' => $sign,
        ]);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Content-Type:multipart/form-data",
        ]);
        $result = curl_exec($ch);

        $result = json_decode($result, true);
        curl_close($ch);
        $imageId = false;
        if (! empty($result['image_id'])) {
            $imageId = $result['image_id'];
        }
        $this->image_id = $imageId;

        return $imageId !== false;
    }

    /**
     * @param $phones
     * @param $viber_transaction
     * @return mixed
     */
    public function sendToViber($phones, $viber_transaction)
    {
        $from = $this->viber_message->alpha_name;
        $encoded = urlencode('user').'='.urlencode(Yii::$app->params['viber']['login']).'&';
        $encoded .= urlencode('from').'='.urlencode($from).'&';
        $encoded .= urlencode('sending_method').'='.urlencode('viber').'&';
        $signString = Yii::$app->params['viber']['login'].$from;

        foreach ($phones as $phone) {
            $encoded .= urlencode('phone').'='.urlencode($phone).'&';
            $signString .= $phone;
        }

        if ($this->viber_message->type !== ViberMessage::ONLYIMAGE) {
            $encoded .= urlencode('txt').'='.urlencode($this->viber_message->text).'&';
            $signString .= $this->viber_message->text;
        }

        if ($this->viber_message->type === ViberMessage::ONLYIMAGE || $this->viber_message->type === ViberMessage::TEXTBUTTONIMAGE) {
            if (! $this->viber_message->viber_image_id) {
                if (! $this->sendImage()) {
                    echo 'Error of image sending';
                    throw new \RuntimeException('Image Sending error.');
                }
                $this->viber_message->viber_image_id = $this->image_id;
                $this->viber_message->save();
            }
            $encoded .= urlencode('image_id').'='.$this->image_id.'&';
        }

        if ($this->viber_message->type === ViberMessage::TEXTBUTTON || $this->viber_message->type === ViberMessage::TEXTBUTTONIMAGE) {
            $encoded .= urlencode('button_text').'='.urlencode($this->viber_message->title_button).'&';
            $encoded .= urlencode('button_link').'='.urlencode($this->viber_message->url_button).'&';
        }
        $encoded .= urlencode('p_transaction_id').'='.((int)$viber_transaction->id).'&';
        $encoded .= urlencode('dlr').'=1&';
        $encoded .= urlencode('dlr_timeout').'=360&';

        $signString .= Yii::$app->params['viber']['secret'];
        $this->viberQuery = $encoded . urlencode('sign').'='.md5($signString);

        //echo "\n\n", $encoded, "\n";
        $ch = curl_init(Yii::$app->params['viber']['url']);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_VERBOSE, $this->debug);
        curl_setopt($ch, CURLOPT_POSTFIELDS,  $this->viberQuery);
        $result = curl_exec($ch);
        return $result;
    }

    private function wrtieToTextLog($result, $viber_transaction, $phones)
    {
        $path = \Yii::getAlias('@frontend').'/runtime/viber_report';
        $fileName = $path.'/query_'.$viber_transaction->id.'_'.date('Ymd_H').'.txt';
        file_put_contents($fileName, '\n'.$this->viberQuery, FILE_APPEND);
        file_put_contents($fileName, '\n=================='.date('H:i:s').'====================', FILE_APPEND);
        if ($viber_transaction->status == 'error') {
            file_put_contents($fileName, '\n=================='.date('H:i:s').'====================', FILE_APPEND);
            file_put_contents($fileName, print_r($phones, 1), FILE_APPEND);
            file_put_contents($fileName, '\n======================================', FILE_APPEND);
            file_put_contents($fileName, $result, FILE_APPEND);
            file_put_contents($fileName, '\n======================================', FILE_APPEND);
        }
    }

    /**
     * @param $xml
     * @param $phonesArray
     * @return bool
     */
    private function parseSendResult($xml, $phonesArray)
    {
        if (is_string($xml)) {
            $xml = simplexml_load_string($xml);
            if ($xml->code == 0) {
                foreach ($xml->msg_id as $key => $msg) {
                    $attr = $msg->attributes();
                    $msg = ((string)$msg);
                    $mPhone = $phonesArray[''. $attr['phone']];
                    $mPhone['status'] = 'sended';
                    $mPhone['msg_id'] = $msg;
                    $mPhone->save();
                }
                return true;
            }
        } else {
            //TODO SendAdminNotification
            return false;
        }
    }

    /**
     * @param $phonesA
     * @param $phones
     * @param $viber_transaction
     */
    public function handleResult($xml_result, $phonesA, $phones, $viber_transaction)
    {
        $viber_transaction->date_send = time();
        if ($this->parseSendResult($xml_result, $phonesA)) {
            $viber_transaction->status = 'sended';
        } else {
            $viber_transaction->status = 'error';
            Yii::error($xml_result);
        }
        $this->wrtieToTextLog($xml_result, $viber_transaction,  $phones);
        $viber_transaction->save();
    }

    /**
     *
     */
    public function sendMessage()
    {
        if ($this->viber_message->status !== ViberMessage::STATUS_PROCESS) {
            return;
        }

        $viber_transaction = ViberTransaction::find()->isNew($this->viber_message->id)->one();
        if (! $viber_transaction) {
            return $this->viber_message->setWait();
        }

        $phonesArray = Message_Phone_List::find()->indexBy('phone')->where(['transaction_id' => $viber_transaction->id])->all();
        $phones = [];
        $phonesA = [];
        foreach ($phonesArray as $phone) {
            $phones[] = $phone->phone;
            $phonesA[$phone->phone] = $phone;
        }
        if (! $phones) {
            return;
        }
        // Отправка сообщения
        $xml_result = $this->sendToViber($phones, $viber_transaction);
        $this->handleResult($xml_result, $phonesA, $phones, $viber_transaction);

        return;
    }

    /**
     * @param array $phones
     * @throws \Exception
     */
    private function saveNewTransaction(array $phones)
    {
        $user = User::find()->where(['id' => $this->viber_message->user_id])->one();

        if ($user->balance < count($phones)) {
            throw new \Exception('balance is small  not save');
        }
        $tVM = new ViberTransaction([
            'user_id' => $this->viber_message->user_id,
            'viber_message_id' => $this->viber_message->id,
            'status' => 'new',
            'size' => count($phones),
            'created_at' => time(),
        ]);
        $tVM->save();
        Message_Phone_List::deleteAll(['transaction_id' => $tVM->id]);

        // списание баланса

        $user->balance = $user->balance - count($phones);
        if (! $user->save()) {
            $tVM->status = 'error';
            $tVM->save();
            print_r($user->getAttributes());
            print_r($user->getErrors());
            throw new \Exception('not save');
        }

        foreach ($phones as $i => $P) {
            $phones[$i]['transaction_id'] = $tVM->id;
        }
       // echo "\n created phones ", count($phones), ' transaction id=', $tVM->id;
        if (! Yii::$app->mongodb->getCollection(Message_Phone_List::collectionName())->batchInsert($phones)) {
            throw new \Exception('not save');
        }
    }

    /**
     * Подготовка транзакций
     * генерация записей в таблице Viber_transaction :: разбиваем список телефонов входящих в рассылку на блоки
     * и для каждого блока создаем заготовку транзакции
     *
     * @return bool
     */
    public function prepareTransaction()
    {

        $db = Yii::$app->db;

        $transaction = $db->beginTransaction();
        $contact_collection_ids = $this->viber_message->getMessageContactCollections()->select(['contact_collection_id'])->distinct('contact_collection_id')->column();
        foreach ($contact_collection_ids as $k => $v) {
            if (is_integer($v)) {
                $contact_collection_ids[] = (string)$v;
            } else {
                $contact_collection_ids[] = (int) $v;
            }
        }

        try {
            if (count($this->phones) > 0) {
                $phones = $this->phones;
            } else {
                $phones = Phone::find()->select(['phone'])->where(['in','contact_collection_id', $contact_collection_ids ])->distinct('phone');
            }

            $user = User::find()->where(['id' => $this->viber_message->user_id])->one();
            if ($user->balance < count($phones)) {
                throw new \Exception('balance is small, not save');
            }

            $tPhones = [];

            foreach ($phones as $phone) {
                $tPhones[] = ['phone' => $phone, 'status' => 'new', 'message_id' => $this->viber_message->id];
                if (count($tPhones) >= Yii::$app->params['viber']['transaction_size_limit']) {

                    echo "\n prepared ", count($tPhones);
                    $this->saveNewTransaction($tPhones);
                    $tPhones = [];
                }
            }

            if (count($tPhones) > 0) {
                print_r($tPhones);
                $this->saveNewTransaction($tPhones);
            }

            $this->viber_message->status = ViberMessage::STATUS_PROCESS;
            $this->viber_message->save();

            $transaction->commit();
        } catch (\Exception $e) {
            $transaction->rollback();
            echo "\n Error ", $e->getMessage();
            return false;
        }
    }
}