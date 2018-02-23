<?php

namespace frontend\controllers;

use common\entities\ViberTransaction;
use frontend\forms\ViberNotification;
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

            $viber_transaction = ViberTransaction::findOne($vb_Note->p_transaction_id);

            if ($viber_transaction) {

                $viber_transaction->handleViberNotification($vb_Note, $fileName);

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
