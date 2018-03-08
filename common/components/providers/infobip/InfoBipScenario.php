<?php
/**
 * Created by PhpStorm.
 * User: mike
 * Date: 03.03.2018
 * Time: 3:45
 */

namespace common\components\providers\infobip;

use common\entities\Scenario;
use common\entities\ViberMessage;
use infobip\api\configuration\BasicAuthConfiguration;
use Yii;

class InfoBipScenario
{
    private $viberMessage;

    private $config;

    private $error;

    private $answer;

    private $scenario;

    /**
     * @return mixed
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * @return mixed
     */
    public function getScenario()
    {
        return $this->scenario;
    }

    /**
     * @return mixed
     */
    public function getScenarios($id='')
    {
        $curl = curl_init();
        $bpAuth = new BasicAuthConfiguration($this->config['login'], $this->config['password']);
        $url="http://api.infobip.com/omni/1/scenarios";
        if ($id){
            $url.='/'.$id;
        }
        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_POSTFIELDS => "",
            CURLOPT_HTTPHEADER => array(
                "accept: application/json",
                "authorization: " . $bpAuth->getAuthenticationHeader()
            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            echo "cURL Error #:" . $err;
        } else {
          echo 'New Scenario created ';
        }
        Yii::warning('Get scenario ');
        Yii::warning($url);
        Yii::warning($response);

        return true;
    }

    /**
     * InfoBipScenario constructor.
     *
     * @param $viberMessage
     * @param $config
     */
    public function __construct(ViberMessage $viberMessage, $config)
    {
        $this->viberMessage = $viberMessage;
        $this->config = $config;
    }

    /**
     * @return \common\entities\Scenario
     */
    private function buildScenario()
    {
        $this->scenario = new Scenario([
                                           'name' => $this->viberMessage->alpha_name.'_viber',
                                           'provider' => $this->viberMessage->provider,
                                           'from1' => $this->viberMessage->alpha_name,
                                           'channel1' => 'VIBER',
                                           'default' => true,
                                           'created_at' => time(),

                                       ]);

        return $this->scenario->save();
    }

    /**
     * @return \common\entities\Scenario
     * @throws \Exception
     */
    public function defineScenario()
    {
        $this->scenario = Scenario::find()->where([
                                                      'provider' => $this->viberMessage->provider,
                                                      'from1' => $this->viberMessage->alpha_name,
                                                  ])->one();
        if (! $this->scenario) {
            $this->scenario = $this->buildScenario();
        }
        if (! $this->scenario) {
            throw new \Exception('Error of scenario '.print_r($this->scenario->getErrors(), 1));
        }

        if (! $this->scenario->provider_scenario_id) {
            $this->createQuery();
            if ($this->error || ! $this->parseAnswer()) {
                Yii::warning('InfoBipScenario:'.$this->toJson(), "\ Error: ".$this->error);
                return false;
            }
        }
        return true;
    }

    private function parseAnswer()
    {
        if (! $this->answer) {
            $this->error .= 'Empty infobip answer';

            return false;
        }
        $json = json_decode($this->answer, 1);
        if (isset($json['requestError'])) {
            $this->error .= serialize($json['requestError']);

            return false;
        }
        if (! isset($json['key'])) {
            $this->error .= 'json error';

            return false;
        }
        $this->scenario->provider_scenario_id = $json['key'];
        if ($this->scenario->save()) {
            return true;
        }
        $this->error = ['Scanario save error', $this->scenario->getErrors()];

        return false;
    }

    public function updateScenario()
    {
    }

    private function getQuery()
    {
    }

    private function updateQuery()
    {
    }

    private function toJson()
    {
        $data = [
            'name' => $this->scenario->name,
            'flow' => [
                [
                    'from' => $this->scenario->from1,
                    'channel' => $this->scenario->channel1,
                ],
            ],
            'default' => $this->scenario->default,
        ];

        return json_encode($data);
    }

    private function createQuery()
    {
        $this->error = '';
        $this->answer = '';
        $curl = curl_init();

        $bpAuth = new BasicAuthConfiguration($this->config['login'], $this->config['password']);
        curl_setopt_array($curl, [
            CURLOPT_URL => "http://api.infobip.com/omni/1/scenarios",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => $this->toJson(),

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
    }
}