<?php
/**
 * Created by PhpStorm.
 * User: mike
 * Date: 09.02.2018
 * Time: 15:19
 */

namespace common\components\providers\gatesms;

use common\components\providers\Provider;
use common\entities\mongo\Message_Phone_List;
use common\entities\ViberMessage;
use Yii;

class GateSms extends Provider
{

    private $access_token;

    public function getToken(){
        //curl -d "client_id=xxxxx&client_secret=xxxxxx" -X POST "http://gatesms.org/api/v1/oauth"
        $td='';
        if(file_exists('SMS_GATE_ACCESS_TOKEN')){
            $td =file_get_contents('SMS_GATE_ACCESS_TOKEN');
            $td=json_decode($td,1);
        }

        if ($td && isset( $td['access_token'] ) &&  isset($td['expires_in']) && time()<$td['expires_in']){
            $this->access_token = $td['access_token'];
            return true;
        }
        $ch = curl_init($this->config['url'] . 'oauth');
        $encoded='';
        $encoded .= urlencode('client_id').'='. $this->config['login'] .'&';
        $encoded .= urlencode('client_secret').'='. $this->config['password']  ;

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_VERBOSE, $this->debug);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $encoded);

        $result = curl_exec($ch);
        $err = curl_error($ch);
        if ($err){
            return false;
        }
        curl_close($ch);
        $td = json_decode($result,1);
        if (isset( $td['access_token'] ) &&  isset($td['expires_in'])){
            $td['expires_in'] +=time() -100 ;
            $this->access_token = $td['access_token'];
            $jtd=json_encode($td,1);
            file_put_contents('SMS_GATE_ACCESS_TOKEN',$jtd);
            return true;
        }
        return false;
    }

    public function sendToPhone(Message_Phone_List $phone){
        if (!$this->getToken()){
            return false;
        }
        //curl -d  "phone=79135701937&sender=test&message=TEXT"  -X POST "http://gatesms.org/api/v1/messages/send" -H "Authorization: Bearer 0fc387460aa95f63271e7734bd725b24"

        $encoded = [
            'phone'=>$phone->phone,
            'sender'=>'test', Yii::$app->params['gatesms']['from'],
            'message'=> $this->text . 'q4 я'
        ];
        $ch = curl_init($this->config['url']. 'messages/send');
        $headers = array(
            "Content-Type: application/json; charset=UTF-8",
            sprintf('Authorization: Bearer %s', $this->access_token )
        );
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers  );
        //curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($ch, CURLOPT_ENCODING, 'UTF-8');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        //curl_setopt($ch, CURLOPT_VERBOSE, $this->debug);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($encoded));
        $result = curl_exec($ch);
        $err = curl_error($ch);
        curl_close($ch);

        if ($err){
            $this->error = $err;
            return false;
        }
        return $result;
    }
    /**
     * в действительности к sms
     * @param $phones
     * @param $transaction_id
     * @return mixed (в штатном режиме xml)
     */
    public function sendToViber($phones, $transaction_id)
    {

        foreach ($phones as $phone){
            $result = $this->sendToPhone($phone);
            if ($result === false){
                $phone->status = Message_Phone_List::ERROR;
            } else {

                $result=json_decode($result,1);
                if (isset($result['data'])){
                    $phone->msg_id = $result['data'];

                }
                if (isset($result['success'])){
                    $phone->status = Message_Phone_List::SENDED;
                } else {
                    $phone->status = Message_Phone_List::ERROR;
                }
            }
            $phone->save();
        }
        return true;
    }

    /**
     * Разбираем ответ провайдера и меняем статусы телефона
     *
     * @param $xml
     * @param $phonesArray
     * @return bool
     */
    public function parseSendResult(  $phonesArray)
    {
       return true;
    }
    
    
    public function getDeliveryReport(){
        /**
         *
         *
         *      SCHEDULED - запланировано к отправке (есть только в smpp протоколе)
         *        ENROUTE - ожидает отправки (идет выбор маршрута)
         *        DELIVERED - доставлено
         *        EXPIRED - истек срок жизни доставки
         *        DELETED - отменена отправка (есть только в smpp протоколе)
         *        UNDELIVERABLE - невозможно доставить (не корректный номер получателя, нет маршрута, и т.п. дополняется кодом ошибки)
         *        ACCEPTED - сообщение принято оператором сотовой связи.
         *        UNKNOWN - не распознан статус (есть только в smpp и может быть только при сбоях в операторской сети)
         *         REJECTED - отклонено (либо api сервером, либо оператором)
         */
        $smsInProceses= ViberMessage::find()->where(['channel'=>'sms'])
            ->andWhere(['in','status', [ViberMessage::STATUS_PROCESS ,ViberMessage::STATUS_WAIT]])->all();
        foreach ($smsInProceses as $smsInProcess){
            $messages_id=Message_Phone_List::find()->where(['message_id'=>$smsInProcess->id])->all();
            foreach ($messages_id as $message_id){
                if (!$this->getToken()){
                    return false;
                }
                $ch   = curl_init($this->config['url'].'/messages/status?id='.$message_id->msg_id);
                $headers = array(
                    sprintf('Authorization: Bearer %s', $this->access_token)
                );
                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

                $response = curl_exec($ch);
                $err      = curl_error($ch);
                curl_close($ch);

                if ($err) {
                    Yii::warning('GET GETSMS REPORT CURl ERROR ::'.$err);
                    echo 'cURL Error #:'.$err;
                } else {
                    $this->answer = $response;
                    $response = json_decode($response);
                    if ( $response->data ==='DELIVERED'){
                        $message_id->status=Message_Phone_List::DELIVERED;
                        if(!$message_id->save())
                            throw new \RuntimeException(print_r($message_id->errors,1));
                    }
                    if ( in_array($response->data, ['EXPIRED','DELETED','UNDELIVERABLE','REJECTED'])){
                        $message_id->status=Message_Phone_List::UNDELIVERED;
                        if(!$message_id->save())
                            throw new \RuntimeException(print_r($message_id->errors,1));
                    }

                }
            }
        }
        return 0;
    }

    public function parseDeliveryReport(){
        return 0;
    }
}