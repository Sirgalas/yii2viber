<?php

namespace frontend\controllers;

use common\entities\ViberTransaction;
use frontend\forms\ViberNotification;
use common\entities\mongo\Message_Phone_List;
use Yii;

class ViberController extends \yii\web\Controller
{
    public function actionIndex()
    {
        return $this->render('index');
    }

    public function beforeAction($action)
    {
        if (in_array($action->id, ['report'])) {
            $this->enableCsrfValidation = false;
        }

        return parent::beforeAction($action);
    }

    public function actionReport()
    {
        $path = \Yii::getAlias('@frontend').'/runtime/viber_report';

        if (! file_exists($path)) {
            echo 'notfound,   mkdir=', mkdir($path);
        }
        try {
            $fileName =$path.'/post_'.date('Ymd_H').'.txt';
            if (isset($_POST)) {
                file_put_contents($fileName, date("H:i:s") . "\n=====================\n".print_r($_POST, 1), FILE_APPEND);
            } else {
                file_put_contents($fileName, 'NO DATA', FILE_APPEND);
                echo 'POST: NO DATA';
            }
            if (isset($_GET)) {
                file_put_contents($path.'/get_'.date('Ymd_H').'.txt', print_r($_GET, 1), FILE_APPEND);
            } else {
                file_put_contents($path.'/get_'.date('Ymd_H').'.txt', 'NO DATA', FILE_APPEND);
                echo 'GET: NO DATA';
            }
        } catch (\Exception $e) {
            file_put_contents($path.'/error_'.date('Ymd_H').'.txt', $e->getMessage(), FILE_APPEND);
            echo "Error", $e->getMessage();
        }

        $data = $_POST;



        $vb_Note = new ViberNotification();
        $vb_Note->load($data, '');

        if ($vb_Note->validate()) {

            //$viber_transaction = ViberTransaction::findOne($vb_Note->p_transaction_id);
            //
            //if ($viber_transaction) {
            //
            //    $viber_transaction->handleViberNotification($vb_Note, $fileName);
            //
            //}
            $phone = Message_Phone_List::find()->where(['msg_id' => $vb_Note->msg_id])->one();
            if (!$phone) {
                file_put_contents($fileName,"\n      NOT FOUND ", FILE_APPEND);
                return 'OK';
            }
            $changed = false;
            file_put_contents($fileName, "\n --- Before action ---\n" .  print_r($phone->getAttributes(), 1), FILE_APPEND);
            if ($vb_Note->type == 'undelivered') {
                $phone->status = 'undelivered';
                $changed = true;
            } else {

                if (is_object($phone) & $phone->getAttribute('status') === 'new' || $phone->getAttribute('status') === 'sended') {
                    if ($vb_Note->type === 'delivered' || $vb_Note->type === 'delivery') {

                        if ($vb_Note->status == 'undelivered') {

                            $phone->status = 'undelivered';
                            $changed = true;
                        } else {

                            ;
                            $phone->status = 'delivered';
                            $phone->date_delivered = time();
                            $changed = true;
                        }
                    }
                    if ($vb_Note->type == 'seen') {


                        $phone->status = 'viewed';
                        $phone->date_delivered = time();
                        $phone->date_viewed = time();
                        $changed = true;
                    }
                } elseif ($phone['status'] === 'delivered') {

                    if ($vb_Note->type == 'seen') {

                        $phone->status = 'viewed';
                        $phone->date_viewed = time();
                        $changed = true;
                    }
                }
            }
            if ($changed) {
                file_put_contents($fileName, "\n --- after action ---\n" .  print_r($phone->getAttributes(), 1), FILE_APPEND);
                $phone->save();

            }
        }

        return 'OK';
    }

    public function actionTest(){
        return $this->render('test');
    }

    public function actionLog($id = null)
    {
        $path = '@common/runtime/logs/viber/';
    }
}
