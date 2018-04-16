<?php

namespace frontend\modules\api\controllers;

use common\entities\mongo\Message_Phone_List;
use common\entities\ViberMessage;
use Yii;
use frontend\modules\api\components\AcViberController;
use common\entities\ViberTransaction;
use common\entities\user\User;
use frontend\search\ReportSearch;
use yii\helpers\Json;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class WhatsappController extends AcViberController
{
    public $modelClass = 'common\entities\ViberTransaction';

    public function actionIndex()
    {
        return ['ok'];
    }

    public function sendMessage($viber_message)
    {
        if ($viber_message->status !== ViberMessage::STATUS_PROCESS) {
            $this->writeToTextLog('no status');

            return false;
        }
        $viber_transaction = ViberTransaction::find()->isNew($viber_message->id)->one();
        if (! $viber_transaction) {
            $this->writeToTextLog('no transactions for '.$viber_message->id);

            return $viber_message->setWait();
        }

        $phonesArray = Message_Phone_List::find()->where(['transaction_id' => $viber_transaction->id])->all();

        $phonesA = [];
        foreach ($phonesArray as $rec) {
            $phonesA[] = ['phone' => $rec->phone, 'name' => $rec->name, 'id' => (string)$rec->_id];
        }
        if (! $phonesA) {
            $viber_transaction->status = 'error';
            $viber_transaction->save();
            $this->writeToTextLog('no phones', $viber_transaction);

            return false;
        }
        // списание баланса
        $user = User::find()->where(['id' => $viber_message->user_id])->one();
        if (!$user->checkBalance('whatsapp', \count($phonesA))) {
            $this->writeToTextLog('balance', $viber_transaction);
            $this->viber_message->setWaitPay();

            return false;
        }
        $balances=$user->balance;
        $balance=$balances[0];
        $balance->whatsapp -= \count($phonesA);
        if (! $balance->save()) {
            $this->writeToTextLog('not save', $viber_transaction);
            throw new \RuntimeException('not save');
        }

        $result = ['msg_template' => ['text' => $viber_message->text]];
        if ($viber_message->image) {
            $result['msg_template']['images'] = [['link'    => $viber_message->image,
                                                  'caption' => $viber_message->image_caption]];
        }
        $result['contacts']        = $phonesA;
        $viber_transaction->status = 'sended';
        if (! $viber_transaction->save()) {
            return ['error' => $viber_transaction->getErrors()];
        };
        $this->writeToTextLog($result, $viber_transaction);

        return $result;
    }

    private function writeToTextLog($result, $viber_transaction = null)
    {
        $path     = \Yii::getAlias('@frontend').'/runtime/whatsapp_report';
        $fileName = $path.'/query_'.date('Ymd_H').'.txt';

        file_put_contents($fileName, "\n".'=================='.date('H:i:s').'====================', FILE_APPEND);

        file_put_contents($fileName, Json::encode($result), FILE_APPEND);
        if ($viber_transaction) {
            file_put_contents($fileName, "\ntransaction_id=".$viber_transaction->id, FILE_APPEND);
        }
        file_put_contents($fileName, "\n".'======================================', FILE_APPEND);
    }

    public function actionGetTask()
    {
        $vm = ViberMessage::find()->isProcess()->andWhere(['channel' => 'whatsapp'])->one();
        if (! $vm) {
            $this->writeToTextLog('no message');

            return '';
        }

        return $this->sendMessage($vm);
    }

    public function actionReport()
    {


        $post = file_get_contents("php://input");

        $path = \Yii::getAlias('@frontend').'/runtime/whatsapp_report';

        if (! file_exists($path)) {
            echo 'notfound,   mkdir=', mkdir($path);
        }
        try {
            $fileName = $path.'/post_'.date('Ymd_H').'.txt';
            if (isset($post) && $post) {
                file_put_contents($fileName, date("H:i:s")."\n=====================\n".$post, FILE_APPEND);
            } else {
                file_put_contents($fileName, 'NO DATA', FILE_APPEND);
                echo 'POST: NO DATA';
            }

        } catch (\Exception $e) {
            file_put_contents($path.'/error_'.date('Ymd_H').'.txt', $e->getMessage(), FILE_APPEND);
            echo "Error", $e->getMessage();
        }

        $data =Json::decode($post,1);
        foreach ( $data['report'] as $rec){

            $phone = Message_Phone_List::find()->where(['_id' => $rec['id']])->one();
            if (! $phone) {
                echo 2;
                file_put_contents($fileName, "\n      NOT FOUND ", FILE_APPEND);

                return 'OK';
            }
            $changed = false;
            file_put_contents($fileName, "\n --- Before action ---\n".print_r($phone->getAttributes(), 1), FILE_APPEND);
            if ($rec['status'] == 'undelivered') {
                $phone->status = 'undelivered';
                $changed       = true;
            } else {

                if (is_object($phone) & $phone->getAttribute('status') === 'new' || $phone->getAttribute('status') === 'sended') {
                    if ($rec['status'] === 'delivered' || $rec['status'] === 'delivery') {


                            $phone->status         = 'delivered';
                            $phone->date_delivered = time();
                            $changed               = true;

                    }
                    if ($rec['status'] == 'seen') {


                        $phone->status         = 'viewed';
                        $phone->date_delivered = time();
                        $phone->date_viewed    = time();
                        $changed               = true;
                    }
                } elseif ($phone['status'] === 'delivered') {

                    if ($rec['status'] == 'seen') {

                        $phone->status      = 'viewed';
                        $phone->date_viewed = time();
                        $changed            = true;
                    }
                }
            }
            if ($changed) {
                file_put_contents($fileName, "\n --- after action ---\n".print_r($phone->getAttributes(), 1),
                                  FILE_APPEND);
                $phone->save();
            }
        }

        return 'ok';
    }
}