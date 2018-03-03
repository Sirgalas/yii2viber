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
        if(!Yii::$app->user->isGuest)
            return $this->redirect('/site/index');
        $config=new Config();
        return $this->render('index',[
            'config'=>$config
        ]);
    }
}
