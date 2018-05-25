<?php
/**
 * Created by PhpStorm.
 * User: mike
 * Date: 09.02.2018
 * Time: 15:19
 */

namespace common\components\providers\gatesms;

use common\components\providers\Provider;
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

    /**
     * в действительности к sms
     * @param $phones
     * @param $transaction_id
     * @return mixed (в штатном режиме xml)
     */
    public function sendToViber($phones, $transaction_id)
    {
        if ($this->getToken()){
            return false;
        }
        $this->err='';
        $this->answer='';
        //curl -d  "phone=1234567&sender=test&message=TEXT"  -X POST "http://gatesms.org/api/v1/messages/send" -H "Authorization: Bearer TOKEN"

        $encoded='';
        $encoded .= urlencode('p_transaction_id').'='.((int)$transaction_id).'&';
        $encoded .= urlencode('dlr').'=1&';
        $encoded .= urlencode('dlr_timeout').'=360&';




        //echo "\n\n", $encoded, "\n";
        $ch = curl_init($this->config['url']);
        $headers = array(
            'Content-Type: application/json',
            sprintf('Authorization: Bearer %s', $this->access_token)
        );



        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_VERBOSE, $this->debug);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $this->viberQuery);
        $result = curl_exec($ch);
        $err = curl_error($ch);

        curl_close($ch);

        if ($err) {
            $this->error = $err;
            return false;
        } else {
            $this->answer = $result;
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
        if (is_string($this->answer)) {
            $xml = simplexml_load_string($this->answer);
            if ($xml->code == 0) {
                foreach ($xml->msg_id as $key => $msg) {
                    $attr = $msg->attributes();
                    $msg = ((string)$msg);
                    $phone=urldecode(''.$attr['phone']);
                    if (!isset($phonesArray[$phone])){
                        $phone = '+' . $phone;

                        if (!isset($phonesArray[$phone])) {
                            continue;
                        }
                    }
                    $mPhone = $phonesArray[$phone];
                    $mPhone['status'] = 'sended';
                    $mPhone['msg_id'] = $msg;
                    $mPhone->save();
                }
                return true;
            }
        }
        //TODO SendAdminNotification
        return false;
    }

    public function getDeliveryReport(){}
}