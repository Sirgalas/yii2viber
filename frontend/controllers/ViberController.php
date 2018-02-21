<?php

namespace frontend\controllers;

use common\entities\ViberTransaction;
use frontend\forms\ViberNotification;

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
        echo "path =$path";
        if (! file_exists($path)) {
            echo 'notfound,   mkdir=', mkdir($path);
        }
        try {
            if (isset($_POST)) {
                file_put_contents($path.'/post_'.date('Ymd_H').'.txt', print_r($_POST, 1), FILE_APPEND);
            } else {
                file_put_contents($path.'/post_'.date('Ymd_H').'.txt', 'NO DATA');
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
        Yii::info("Notify:  \n " . print_r($data,1), 'viber');
        $vb_Note = new ViberNotification();
        $vb_Note->load($data, '');
        if ($vb_Note->validate()) {
            $viber_transaction =  ViberTransaction::findOne($vb_Note->p_transaction_id);
            if ($viber_transaction) {
                $viber_transaction->handleViberNotification($vb_Note);
            }
        }

        return 'OK';
    }

    public function actionLog($id = null){
        $path = '@common/runtime/logs/viber/';
    }
}
