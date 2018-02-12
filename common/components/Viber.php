<?php
/**
 * Created by PhpStorm.
 * User: mike
 * Date: 09.02.2018
 * Time: 15:19
 */

namespace common\components;

use common\entities\ViberMessage;
use common\entities\ContactCollection;
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
        $from = Yii::$app->params['viber']['from'];
        $phones = $this->viber_message->getPhones();

        // Отправка сообщения

        $encoded = urldecode('user').'='.urldecode(Yii::$app->params['viber']['login']).'&';
        $encoded .= urldecode('from').'='.urlencode($from).'&';
        $encoded .= urldecode('sending_method').'='.urlencode('viber').'&';
        $signString = Yii::$app->params['viber']['login'].$from;

        foreach ($phones as $phone) {
            $encoded .= urldecode('phone').'='.urlencode($phone).'&';
            $signString .= $phone;
        }
        if ($this->viber_message->type !== ViberMessage::ONLYIMAGE) {
            $encoded .= urldecode('txt').'='.urlencode($this->viber_message->text).'&';
            $signString .= $this->viber_message->text;
        }

        if ($this->viber_message->type === ViberMessage::ONLYIMAGE || $this->viber_message->type === ViberMessage::TEXTBUTTONIMAGE) {
            if (! $this->sendImage()) {
                throw new \RuntimeException('Sending error.');
            }

            $encoded .= urldecode('image_id').'='.$this->image_id.'&';
        }

        if ($this->viber_message->type === ViberMessage::TEXTBUTTON || $this->viber_message->type === ViberMessage::TEXTBUTTONIMAGE) {
            $encoded .= urldecode('button_text').'='.urlencode($this->viber_message->title_button).'&';
            $encoded .= urldecode('button_link').'='. urlencode($this->viber_message->url_button).'&';

        }

        $signString .= Yii::$app->params['viber']['secret'];
        $encoded .= urldecode('sign').'='.md5($signString);

        $ch = curl_init('https://bulk.sms-online.com/');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_VERBOSE, $this->debug);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $encoded);

        $result = curl_exec($ch);
        curl_close($ch);
        print_r($result);
        //TODO parse result
    }

    /**
     *
     */
    public function run()
    {
        $this->sendMessage();
    }
}