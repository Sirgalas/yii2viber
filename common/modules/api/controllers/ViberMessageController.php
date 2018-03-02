<?php
namespace common\modules\api\controllers;


use yii\rest\ActiveController;
/**
 * Default controller for the `api` module
 */
class MessageController extends ActiveController
{
    use \common\modules\api\traits\Aplication;
    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index');
    }
}
