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
            'image' => new  CURLFile(realpath($filePath)),
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

        return $imageId !== false;
    }

    public function sendMessage(){

        // Отправка сообщения
        $data = [
            'user' => Yii::$app->params['viber']['login'],
            'from' => $this->viber_message->user->username,
            'phone' =>$this->viber_message->getPhones(),
            'sending_method' => 'viber',
            'p_transaction_id'=>'100001-' . time()
        ];

        if ($this->viber_message->type !== ViberMessage::ONLYIMAGE){
            $data['txt']= $this->viber_message->text;
        }

        if ($this->viber_message->type === ViberMessage::ONLYIMAGE
            || $this->viber_message->type === ViberMessage::TEXTBUTTONIMAGE ) {
            if (!$this->sendImage()){
                throw new \RuntimeException('Sending error.');
            }
            $data['image_id']=$this->image_id;
        }
        if ($this->viber_message->type === ViberMessage::TEXTBUTTON
            || $this->viber_message->type===ViberMessage::TEXTBUTTONIMAGE ) {

            $data['button_text'] = $this->viber_message->title_button;
            $data['button_link'] = $this->viber_message->url_button;
        }
        $signString=$data['user'] .
            $data['from'] .
            implode("\n",$data['phone']) .
            //$data['phone'] .
            $data['txt'] .
            Yii::$app->params['viber']['secret'];
        $data['sign']=md5($signString);

        print_r($data);
        echo "\n === signString $signString\n";
        echo "\n === data['sign'] {$data['sign']}\n";


        $ch = curl_init('https://bulk.sms-online.com/');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_VERBOSE, $this->debug);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        $result = curl_exec($ch);
        curl_close($ch);
echo "\n Вывод результата отправки сообщения:\n";
        print_r($result);
    }

    /**
     *
     */
    public function run()
    {
        $this->sendMessage();
    }
}