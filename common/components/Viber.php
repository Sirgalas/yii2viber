<?php
/**
 * Created by PhpStorm.
 * User: mike
 * Date: 09.02.2018
 * Time: 15:19
 */

namespace common\components;

use common\entities\mongo\Phone;
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

    /**
     * Viber constructor.
     *
     * @param $viber_message_id
     */
    public function __construct(ViberMessage $viber_message)
    {
        $this->viber_message = $viber_message;
        if ($this->viber_message == null) {
            throw new NotFoundHttpException(Yii::t('app', 'The requested messages does not exist.'));
        }
        $this->image_id = 0;
    }

    /**
     * @return bool
     */
    public function sendImage()
    {
        echo '=============================Start Send Image ==================';
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
        $result = json_decode(curl_exec($ch), true);
        curl_close($ch);
        $imageId = false;
        if (! empty($result['image_id'])) {
            $imageId = $result['image_id'];
        }
        $this->image_id = $imageId;
        echo '============================= End ==================';

        return $imageId !== false;
    }

    /**
     *
     */
    public function sendMessage()
    {

        if ($this->viber_message->status !== ViberMessage::STATUS_PROCESS) {
            return;
        }
        $from = Yii::$app->params['viber']['from'];
        $viber_transaction = ViberTransaction::find()->isNew($this->viber_message->id)->one();

        if (!$viber_transaction){
            $this->viber_message->status = ViberMessage::STATUS_WAIT;
            $this->viber_message->save();
            return;
        }
        $phonesArray = $viber_transaction->getPhonesArray();
        $phones=array_keys($phonesArray);
        if (! $phones) {
            return;
        }
        // Отправка сообщения

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
                    throw new \RuntimeException('Sending error.');
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
        $encoded .= urlencode('p_transaction_id').'=t2_'. $viber_transaction->id .'&';
        $encoded .= urlencode('dlr').'=1&';
        $encoded .= urlencode('dlr_timeout').'=360&';

        $signString .= Yii::$app->params['viber']['secret'];
        $encoded .= urlencode('sign').'='.md5($signString);
        //echo "\n\n", $encoded, "\n";
        $ch = curl_init('https://bulk.sms-online.com/');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_VERBOSE, $this->debug);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $encoded);

        $result = curl_exec($ch);
        curl_close($ch);
        //echo '===============================';
        //print_r($result);

        if ($this->parseSendResult($result, $phonesArray)) {
            $viber_transaction->phones = \GuzzleHttp\json_encode($phonesArray);
            $viber_transaction->status = 'sended';
            $viber_transaction->save();
        } else {

            $viber_transaction->status = 'error';
            Yii::error($result);
            $viber_transaction->save();
        }
       return;

    }

    private function parseSendResult($xml, $phonesArray){
        if (is_string($xml)) {
            $xml = simplexml_load_string($xml);
        } else {
            echo 'no string';

            return false;
        }
        if ( $xml->code == 0){
            foreach ($xml->msg_id as $key => $msg){
                $attr=$msg->attributes();
                $phone =  $attr['phone'];
                $msg = ((string)$msg);
                $phonesArray['' . $phone] = ['status'=>'sended', 'msg_id'=>$msg];
            }
            return true;
        } else {
            echo 'error' . $xml->tech_message;
        }
    }
    private function saveNewTransaction(array $phones)
    {
        $tVM = new ViberTransaction([
            'user_id' => $this->viber_message->user_id,
            'viber_message_id' => $this->viber_message->id,
            'status' => 'new',
            'created_at' => time(),
            'phones' => \GuzzleHttp\json_encode($phones)]);
        $tVM->save();
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
        echo "prepareTransaction Start";
        $db = Yii::$app->db;
        $transaction = $db->beginTransaction();
        $contact_collection_ids = $this->viber_message->getMessageContactCollections()->select(['contact_collection_id'])->distinct('contact_collection_id')->column();
        foreach ($contact_collection_ids as $k => $v) {
            $contact_collection_ids[$k] = ''.$v;
        }
        try {
            $phones = Phone::find()->select(['phone'])->where([
                    'in',
                    'contact_collection_id',
                    $contact_collection_ids,
                ])->distinct('phone');

            $tPhones = [];
            foreach ($phones as $phone) {
                $tPhones[$phone] = ['status' => 'new'];
                if (count($tPhones) >= Yii::$app->params['viber']['transaction_size_limit']) {
                    $this->saveNewTransaction($tPhones);
                    $tPhones = [];
                }
            }
            if (count($tPhones) > 0) {
                $this->saveNewTransaction($tPhones);
            }
            $this->viber_message->status = ViberMessage::STATUS_PROCESS;
            $this->viber_message->save();
            $transaction->commit();
        } catch (\Exception $e) {
            $transaction->rollback();
            echo "\nError ", $e->getMessage();
            return false;
        }
    }


}