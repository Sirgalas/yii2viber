<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 20.02.18
 * Time: 17:09
 */

namespace frontend\controllers;

use frontend\entities\User;
use yii\base\Exception;
use yii\web\Controller;
use Yii;
class ProfileController extends Controller
{
    public function actionViews(){
        $entities=$this->findModel();
        return $this->render('views',compact('entities'));
    }

    public function actionUpdate(){
        $model=$this->findModel();
        $model->scenario=User::SCENARIO_PROFILE;
        if($model->load(\Yii::$app->request->post())){
            try{
                if(!$model->save())
                    throw new Exception(json_encode($model->errors));
                return $this->redirect(['views']); 
            }catch (Exception $ex){
                Yii::$app->errorHandler->logException($ex);
                Yii::$app->session->setFlash($ex->getMessage());
            }
            
        }

        return $this->render('update',compact('model'));
    }
    private function findModel(){
        return User::findOne(\Yii::$app->user->identity->id);
    }

}