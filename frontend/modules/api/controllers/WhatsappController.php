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
            $this->writeToTextLog('no transactions for ' . $viber_message->id);
            return $viber_message->setWait();
        }

        $phonesArray = Message_Phone_List::find()

            ->where(['transaction_id' => $viber_transaction->id])
            ->all();

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
        if ($user->balance < \count($phonesA)) {
            $this->viber_message->setWaitPay();
            $this->writeToTextLog('balance',$viber_transaction);
            return false;
        }
        $user->balance -= \count($phonesA);
        if (! $user->save()) {
            $this->writeToTextLog('not save',$viber_transaction);
            throw new \RuntimeException('not save');
        }

        $result = ['msg_template' => ['text' => $viber_message->text]];
        if ($viber_message->image) {
            $result['msg_template']['images'] = [['link'    => $viber_message->image,
                                                  'caption' => $viber_message->image_caption]];
        }
        $result['contacts'] = $phonesA;
        $viber_transaction->status = 'sended';
        if (!$viber_transaction->save()){
            return ['error'=>$viber_transaction->getErrors()];
        };
        $this->writeToTextLog($result,$viber_transaction);
        return $result;
    }

    private function writeToTextLog($result, $viber_transaction=null)
    {
        $path     = \Yii::getAlias('@frontend').'/runtime/whatsapp_report';
        $fileName = $path.'/query_'.date('Ymd_H').'.txt';

        file_put_contents($fileName, "\n".'=================='.date('H:i:s').'====================', FILE_APPEND);

        file_put_contents($fileName, Json::encode($result), FILE_APPEND);
        if ($viber_transaction) {
            file_put_contents($fileName, "\ntransaction_id=" . $viber_transaction->id, FILE_APPEND);
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
        \Yii::warning('WhatsApp report POST data '.print_r($_POST, 1));
        \Yii::warning('WhatsApp raw data '.file_get_contents("php://input"));

        return 'ok';
    }
}