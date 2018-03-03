<?php
/**
 * Created by PhpStorm.
 * User: mike
 * Date: 09.02.2018
 * Time: 15:19
 */

namespace common\components\providers\infobip;

use common\components\providers\Provider;
use common\entities\ViberMessage;
use Yii;
use common\entities\Scenario;

class InfoBip extends Provider
{
    private $viberMessage;

    private $sceanrio;

    public function setMessage(ViberMessage $viberMessage

    ) {
        parent::setMessage($viberMessage);
        $this->viberMessage = $viberMessage;
    }

    private function toJson($phones, $transaction_id)
    {
        $data = [
            'bulkId' => $transaction_id,
            'scenarioKey' => $this->sceanrio->prvider_scenario_id,
            'destinations' => [],
            'viber' => [
                'isPromotional' => $this->viberMessage->isPromotional(),
            ],
        ];

        if (! $this->type !== ViberMessage::ONLYIMAGE) {
            $data['viber']['text'] = $this->text;
        }
        if ($this->type === ViberMessage::ONLYIMAGE || $this->type === ViberMessage::TEXTBUTTONIMAGE) {
            $data['viber']['imageURL'] = $this->image;
        }
        if ($this->type === ViberMessage::TEXTBUTTON || $this->type === ViberMessage::TEXTBUTTONIMAGE) {
            $data['viber']['buttonText'] = $this->title_button;
            $data['viber']['buttonURL'] = $this->url_button;
        }

        foreach ($phones as $phone) {
            $data['destinations'][] = [
                'to' => [
                    'phoneNumber' => $phone,
                ],
            ];
        }

        return json_encode($data);
    }

    /**
     * @param $phones
     * @param $transaction_id
     * @return mixed (в штатном режиме xml)
     * @throws \Exception
     */
    public function sendToViber($phones, $transaction_id)
    {

        $IBScenario = new InfoBipScenario($this->viberMessage, $this->config);
        if ($IBScenario->defineScenario()) {
            $this->scenario = $IBScenario->getScenario();
            if ($this->viberMessage->scenario_id !== $this->scenario->id) {
                $this->viberMessage->scenario_id = $this->scenario->id;
                $this->viberMessage->save();
            }
        } else {
            return false;
        }

        $from = $this->from;
        $encoded = urlencode('user').'='.urlencode($this->params['login']).'&';
        $encoded .= urlencode('from').'='.urlencode($from).'&';
        $encoded .= urlencode('sending_method').'='.urlencode('viber').'&';
        $signString = Yii::$app->params['smsonline']['login'].$from;

        foreach ($phones as $phone) {
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

        $signString .= $this->params['secret'];
        $this->viberQuery = $encoded.urlencode('sign').'='.md5($signString);

        //echo "\n\n", $encoded, "\n";
        $ch = curl_init(Yii::$app->params['smsonline']['url']);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_VERBOSE, $this->debug);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $this->viberQuery);
        $result = curl_exec($ch);

        return $result;
    }

    /**
     * Разбираем ответ провайдера и меняем статусы телефона
     *
     * @param $xml
     * @param $phonesArray
     * @return bool
     */
    public function parseSendResult($xml, $phonesArray)
    {
        if (is_string($xml)) {
            $xml = simplexml_load_string($xml);
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
}