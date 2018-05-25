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
        //curl -d  "phone=1234567&sender=test&message=TEXT"  -X POST "http://gatesms.org/api/v1/messages/send" -H "Authorization: Bearer TOKEN"

        $encoded = urlencode('phone').'='.urlencode($phone->phone).'&';
        $encoded .= urlencode('sender').'=' . urlencode($this->from);
        $encoded .= urlencode('message').'=' .urlencode( $this->text);
        $ch = curl_init($this->config['url']);
        $headers = array(
            'Content-Type: application/json',
            sprintf('Authorization: Bearer %s', $this->access_token)
        );
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_VERBOSE, $this->debug);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $encoded);
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
                $phone->status = Message_Phone_List::SENDED;
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

        $smsInProceses= ViberMessage::find()->where(['channel'=>'sms'])->andWhere(['status'=>ViberMessage::STATUS_PROCESS])->all();
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
                    Yii::warning('GET INFOBIP REPORT CURl ERROR ::'.$err);
                    echo 'cURL Error #:'.$err;
                } else {
                    $this->answer = $response;
                    if($response->data=='DELIVERED'){
                        $message_id->status=Message_Phone_List::DELIVERED;
                        if(!$message_id->save())
                            throw new \RuntimeException(print_r($message_id->errors,1));
                    }
                    Yii::warning('GET INFOBIP REPORT::'.$response);
                }
            }
        }
    }

}