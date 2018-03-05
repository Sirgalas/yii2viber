<?php

namespace frontend\modules\home\controllers;

use yii\web\Controller;
use common\entities\Config;
use Yii;

/**
 * Default controller for the `home` module
 */
class DefaultController extends Controller
{
    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex()
    {
        $config=new Config();
        if (Yii::$app->user->isGuest){
            if(Yii::$app->request->get('id'))
                return $this->redirect(['auth/register','id'=>Yii::$app->request->get('id')]);
            return $this->render('index',['config'=>$config]);
        };
        ;
        return $this->redirect('/site/index');
    }
}
