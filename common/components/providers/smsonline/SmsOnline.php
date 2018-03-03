<?php
/**
 * Created by PhpStorm.
 * User: mike
 * Date: 09.02.2018
 * Time: 15:19
 */

namespace common\components\providers\smsonline;

use common\components\providers\Provider;
use common\entities\ViberMessage;
use Yii;

class SmsOnline extends Provider
{
    public function sendImage()
    {
        $filePath = realpath($this->image);
        $sign = md5($this->config['login'].md5_file($filePath).$this->config['secret']);
        $ch = curl_init('http://media.sms-online.com/upload/');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_VERBOSE, $this->debug);
        curl_setopt($ch, CURLOPT_POSTFIELDS, [
            'login' =>$this->config['login'],
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
     * @param $transaction_id
     * @return mixed (в штатном режиме xml)
     */
    public function sendToViber($phones, $transaction_id)
    {
        $this->err='';
        $this->answer='';

        $from = $this->from;
        $encoded = urlencode('user').'='.urlencode($this->config['login']).'&';
        $encoded .= urlencode('from').'='.urlencode($from).'&';
        $encoded .= urlencode('sending_method').'='.urlencode('viber').'&';
        $signString = $this->config['login'].$from;

        foreach ($phones as $phone=>$rec) {
            $encoded .= urlencode('phone').'='.urlencode($phone).'&';
            $signString .= $phone;
        }

        if ($this->type !== ViberMessage::ONLYIMAGE) {
            $encoded .= urlencode('txt').'='.urlencode($this->text).'&';
            $signString .= $this->text;
        }

        if ($this->type === ViberMessage::ONLYIMAGE || $this->type === ViberMessage::TEXTBUTTONIMAGE) {
            if (! $this->image_id) {
                if (! $this->sendImage()) {
                    echo 'Error of image sending';
                    throw new \RuntimeException('Image Sending error.');
                }
            }
            $encoded .= urlencode('image_id').'='.$this->image_id.'&';
        }

        if ($this->type === ViberMessage::TEXTBUTTON || $this->type === ViberMessage::TEXTBUTTONIMAGE) {
            $encoded .= urlencode('button_text').'='.urlencode($this->title_button).'&';
            $encoded .= urlencode('button_link').'='.urlencode($this->url_button).'&';
        }
        $encoded .= urlencode('p_transaction_id').'='.((int)$transaction_id).'&';
        $encoded .= urlencode('dlr').'=1&';
        $encoded .= urlencode('dlr_timeout').'=360&';

        $signString .= $this->config['secret'];
        $this->viberQuery = $encoded.urlencode('sign').'='.md5($signString);

        //echo "\n\n", $encoded, "\n";
        $ch = curl_init($this->config['url']);
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
                    $mPhone = $phonesArray[''.$attr['phone']];
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