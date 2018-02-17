<?php

namespace frontend\controllers;

class ViberController extends \yii\web\Controller
{
    public function actionIndex()
    {
        return $this->render('index');
    }

    public function beforeAction($action) {
        if (in_array($action->id, ['report'])) {
            $this->enableCsrfValidation = false;
        }
        return parent::beforeAction($action);
    }
    public function actionReport()
    {
        $path = \Yii::getAlias('@frontend').'/runtime/viber_report';
        echo "path =$path";
        if (!file_exists($path)){
            echo 'notfound,   mkdir=',  mkdir($path);
        }
        try {
            if (isset($_POST)) {
                file_put_contents($path .'/post_'.date('Ymd_His').'.txt',
                    print_r($_POST, 1));
                    print_r($_POST);
            } else {
                file_put_contents($path .'/post_'.date('Ymd_His').'.txt', 'NO DATA');
                echo 'POST: NO DATA';
            }
            if (isset($_GET)) {
                file_put_contents($path .'/get_'.date('Ymd_His').'.txt',
                    print_r($_GET, 1));
                print_r($_GET);
            } else {
                file_put_contents($path .'/get_'.date('Ymd_His').'.txt', 'NO DATA');
                echo 'GET: NO DATA';
            }
        } catch(\Exception $e) {
            file_put_contents($path .'/error_'.date('Ymd_His').'.txt', $e->getMessage());
            echo "Error", $e->getMessage();
        }

       return 'OK';
    }

}
