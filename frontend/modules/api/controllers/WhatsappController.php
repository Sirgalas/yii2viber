<?php

namespace frontend\modules\api\controllers;

use Yii;
use frontend\modules\api\components\AcViberController;
use common\entities\ViberTransaction;
use frontend\search\ReportSearch;
use yii\web\NotFoundHttpException;

class WhatsappController extends AcViberController
{
    public $modelClass = 'common\entities\ViberTransaction';
     public function actionIndex(){
         return ['ok'];
     }
     public function actionGetTask(){
         $result=[
             'msg_template'=>"Привет {name}! Жми на картинку! И полетишь как птица
                Полет в АЭРОТРУБЕ htpps://atmosfera30.ru,
             ",
             'images'=>[
                'link'=>'',
                'captions'=>'',
             ],
             'contacts'=>[
                 [
                     'phone'=>'79135701037',
                     'name'=>'Mike',
                     'id'=>'12345678'
                 ],
                 [
                     'phone'=>'79050885202',
                     'name'=>'',
                     'id'=>'222222222'
                 ],
             ]

         ];
         return $result;
     }

     public function actionReport(){
        return 'ok';
     }

}