<?php

namespace frontend\controllers;

class ViberController extends \yii\web\Controller
{
    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionReport()
    {

       file_put_contents(\Yii::getAlias('@frontend') . '\runtime\viber_report\post_' . time() .'.txt', print_r($_POST,1));
       file_put_contents(\Yii::getAlias('@frontend') . '\runtime\viber_report\get_' . time() .'.txt', print_r($_GET,1));
       return 'OK';
    }

}
