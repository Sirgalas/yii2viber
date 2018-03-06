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
use infobip\api\configuration\BasicAuthConfiguration;

class InfoBip extends Provider
{
    private $viberMessage;

    private $scenario;

    public function setMessage(ViberMessage $viberMessage

    ) {
        parent::setMessage($viberMessage);
        $this->viberMessage = $viberMessage;
    }

    private function toJson($phones, $transaction_id)
    {
        $data = [
            'bulkId' => $transaction_id,
            'scenarioKey' => $this->scenario->provider_scenario_id,
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
                'messageId'=>(string)$phone->_id,
                'to' => [
                    'phoneNumber' => $phone->phone,

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

        $this->err='';
        $this->answer='';
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

        $curl = curl_init();

        $bpAuth = new BasicAuthConfiguration($this->config['login'], $this->config['password']);
        curl_setopt_array($curl, [
            CURLOPT_URL => "http://api.infobip.com/omni/1/advanced",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => $this->toJson($phones, $transaction_id),

            CURLOPT_HTTPHEADER => [
                "accept: application/json",
                "authorization: ".$bpAuth->getAuthenticationHeader(),
                "content-type: application/json",
            ],
        ]);

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            $this->error = $err;
        } else {
            $this->answer = $response;
        }
        Yii::warning('Query:: ' . $this->toJson($phones, $transaction_id));
        Yii::warning('Response:: ' . $response);
        return $response;
    }

    /**
     * Разбираем ответ провайдера и меняем статусы телефона
     *
     * @param $xml
     * @param $phonesArray
     * @return bool
     */
    public function parseSendResult( $phonesArray)
    {
        if (is_string($this->answer)) {
            $json = \GuzzleHttp\json_decode($this->answer,1);
            if (isset($json['messages'])) {
                foreach ($json['messages'] as $message) {
                  Yii::warning('Message_Response:'.print_r($message, 1));
                  $message_id = $message['messageId'];
                  $phone = $message['to']['phoneNumber'];
                    if (isset($phonesArray[''.$phone])) {
                        $mPhone = $phonesArray[''.$phone];
                        if ((string)$mPhone->_id != $message_id) {
                            Yii::error('Msg_id not equal  ' . $message_id);
                            Yii::error('Phone_Query:'.print_r($mPhone->getAttributes(), 1));
                            Yii::error('Message_Response:'.print_r($message, 1));
                        }
                        $mPhone['status'] = 'sended';
                        $mPhone->save();
                    } else {
                        Yii::error('Response has uncnoun phone');
                        Yii::error('Message_Response:'.print_r($message, 1));
                    }
                }

                return true;
            }
        }

        //TODO SendAdminNotification
        return false;
    }

    /**
     *
     */
    public function getDeliveryReport(){

        $curl = curl_init();
        $bpAuth = new BasicAuthConfiguration($this->config['login'], $this->config['password']);
        curl_setopt_array($curl, array(
            CURLOPT_URL => "http://api.infobip.com/omni/1/reports?channel=VIBER",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_POSTFIELDS => "",
            CURLOPT_HTTPHEADER => array(
                "accept: application/json",
                "authorization: ".$bpAuth->getAuthenticationHeader(),
            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            echo "cURL Error #:" . $err;
        } else {
            echo $response;
        }
    }
}