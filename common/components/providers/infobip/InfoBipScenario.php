<?php
/**
 * Created by PhpStorm.
 * User: mike
 * Date: 03.03.2018
 * Time: 3:45
 */

namespace common\components\providers\infobup;

use common\entities\Scenario;
use common\entities\ViberMessage;
use infobip\api\configuration\BasicAuthConfiguration;
class InfoBupScenario
{
    private $viberMessage;

    /**
     * InfoBupScenario constructor.
     *
     * @param $viberMessage
     */
    public function __construct(ViberMessage $viberMessage)
    {
        $this->viberMessage = $viberMessage;
    }

    /**
     * @return \common\entities\Scenario
     */
    private function buildScenario()
    {
        $scenario = new Scenario([
                                     'name' => $this->viberMessage->alpha_name.'_viber',
                                     'provider' => $this->viberMessage->provider,
                                     'from1' => $this->viberMessage->alpha_name,
                                     'channel1' => 'Viber',
                                     'default' => false,
                                     'created_at' => time(),

                                 ]);
        $scenario->save();
        $this->viberMessage->scenario_id = $scenario->id;

        return $scenario;
    }

    /**
     * @return \common\entities\Scenario
     * @throws \Exception
     */
    public function defineScenario():Scenario
    {
        $scenario = Scenario::find()->where([
                                                'provider' => $this->viberMessage->provider,
                                                'from1' => $this->viberMessage->alpha_name,
                                            ])->one();
        if (! $scenario) {
            $scenario = $this->buildScenario();
        }
        if (! $scenario) {
            throw new \Exception('Error of scenario '.print_r($scenario->getErrors(), 1));
        }
        if (! $scenario->provider_scenario_id) {
            $this->createQuery($scenario);
        }

        return $scenario;
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

    private function createQuery(Scenario $scenario)
    {
        $curl = curl_init();
        $data = [
            'name' => $scenario->name,
            'flow' => [
                [
                    'from' => $scenario->from1,
                    'channel' => $scenario->channel1,
                ],
            ],
            'default' => $scenario->default,
        ];
        curl_setopt_array($curl, [
            CURLOPT_URL => "http://api.infobip.com/omni/1/scenarios",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_HTTPHEADER => [
                "accept: application/json",
                "authorization: Basic QWxhZGRpbjpvcGVuIHNlc2FtZQ==",
                "content-type: application/json",
            ],
        ]);

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            echo "cURL Error #:".$err;
        } else {
            echo $response;
        }
    }
}