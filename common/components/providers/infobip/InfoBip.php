<?php
/**
 * Created by PhpStorm.
 * User: mike
 * Date: 09.02.2018
 * Time: 15:19
 */

namespace common\components\providers\infobip;

use common\components\providers\infobip\models\InfobipStatus;
use common\components\providers\Provider;
use common\entities\mongo\Message_Phone_List;
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
                'messageId' => (string)$phone->_id,
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

        $this->err = '';
        $this->answer = '';
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
            CURLOPT_URL => 'http://api.infobip.com/omni/1/advanced',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $this->toJson($phones, $transaction_id),

            CURLOPT_HTTPHEADER => [
                'accept: application/json',
                'authorization: '.$bpAuth->getAuthenticationHeader(),
                'content-type: application/json',
            ],
        ]);

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            $this->error = $err;
            Yii::warning('GET INFOBIP REPORT CURl ERROR ::'.$err);
        } else {
            $this->answer = $response;
        }
        Yii::warning('Query:: '.$this->toJson($phones, $transaction_id));
        Yii::warning('Response:: '.$response);

        return $response;
    }

    /**
     * Разбираем ответ провайдера и меняем статусы телефона
     *
     * @param $phonesArray
     * @return bool
     */
    public function parseSendResult($phonesArray)
    {
        if (is_string($this->answer)) {
            $json = \GuzzleHttp\json_decode($this->answer, 1);
            if (isset($json['messages'])) {
                foreach ($json['messages'] as $message) {
                    Yii::warning('Message_Response:'.print_r($message, 1));
                    $message_id = $message['messageId'];
                    $phone = $message['to']['phoneNumber'];
                    if (isset($phonesArray[''.$phone])) {
                        $mPhone = $phonesArray[''.$phone];
                        if ((string)$mPhone->_id !== $message_id) {
                            Yii::error('Msg_id not equal  '.$message_id);
                            Yii::error('Phone_Query:'.print_r($mPhone->getAttributes(), 1));
                            Yii::error('Message_Response:'.print_r($message, 1));
                        }

                        $mPhone['status'] = InfobipStatus::parseStatus($message);
                        $mPhone->save();
                    } else {
                        Yii::error('Response has unknown phone');
                        Yii::error('Message_Response:'.print_r($message, 1));
                    }
                }

                return true;
            }
        }

        //TODO SendAdminNotification
        return false;
    }

    public function parseDeliveryReport()
    {
        if (! $this->answer) {
            return;
        }
        $answer = json_decode($this->answer, 1);
        $err = json_last_error();
        if ($err) {
            $this->error = $err;
            Yii::warning('GET INFOBIP PARSE REPORT JSON ERROR ::'.json_last_error_msg());

            return;
        }
        $transaction_ids = [];
        $commandsCount = 0;
        $command = Yii::$app->mongodb->createCommand();
        foreach ($answer['results'] as $result) {
            $update = [];
            $update['status'] = InfobipStatus::parseStatus($result);
            $update['error'] = 0;
            if (isset($result['error'])) {
                $update['error'] = $result['error']['id'];
            }
            $update['messageCount'] = $result['messageCount'];
            if (isset($result['price']) && isset($result['price']['currency']) && isset($result['price']['pricePerMessage'])) {
                $update['currency'] = $result['price']['currency'];
                $update['pricePerMessage'] = $result['price']['pricePerMessage'];
            }
            if (is_array($result['bulkId'])) {
                $transaction_ids[] = $result['bulkId'];
            }
            try {
                $where = ['=', '_id', new \MongoDB\BSON\ObjectId($result['messageId'])];
            } catch (\MongoDB\Driver\Exception\InvalidArgumentException $e) {

                $where = ['=', 'msg_id', $result['messageId']];
            }
            if ($update['status'] === Message_Phone_List::DELIVERED) {

                $update['date_delivered'] = strtotime($result['doneAt']);
            }
            if ($update['status'] === Message_Phone_List::VIEWED) {

                $update['date_viewed'] = strtotime($result['doneAt']);
            }
            $commandsCount += 1;
            $command->addUpdate($where,$update);

            print_r(['W:'=>$where,'U:'=>$update]);
        }
        if ($commandsCount) {
            $command->addUpdate(['status'=>Message_Phone_List::VIEWED, 'date_delivered'=>null],$update);
            $r = $command->executeBatch(Message_Phone_List::collectionName());
            var_dump($r);
        }
        return true;
    }

    /**
     *
     */
    public function getDeliveryReport()
    {

        $curl = curl_init();
        $bpAuth = new BasicAuthConfiguration($this->config['login'], $this->config['password']);
        curl_setopt_array($curl, [
            CURLOPT_URL => 'http://api.infobip.com/omni/1/reports?channel=VIBER',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_POSTFIELDS => '',
            CURLOPT_HTTPHEADER => [
                'accept: application/json',
                'authorization: '.$bpAuth->getAuthenticationHeader(),
            ],
        ]);
        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);

        if ($err) {
            Yii::warning('GET INFOBIP REPORT CURl ERROR ::'.$err);
            echo 'cURL Error #:'.$err;
        } else {
            $this->answer = $response;
            Yii::warning('GET INFOBIP REPORT::'.$response);
        }
    }
}