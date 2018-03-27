<?php

namespace frontend\modules\api\controllers;

use Codeception\Module\Cli;
use frontend\modules\api\components\AcViberController;
use Yii;
use frontend\forms\RegistrationForm;
use dektrium\user\traits\AjaxValidationTrait;
use common\entities\user\Client;
use yii\web\NotFoundHttpException;
use dektrium\user\traits\EventTrait;

class ClientController extends AcViberController
{

    use AjaxValidationTrait;
    use EventTrait;
    public $modelClass = 'common\entities\user\Client';

    public function actionIndex()
    {
        
        $ids = Yii::$app->user->identity->getChildList();
        $client = Client::find()->where('coalesce(blocked_at, 0)<1')->andWhere(['in', 'id', $ids])->all();
        return $client;
    }

    public function actionRegistration()
    {

        if (!$this->module->enableRegistration) {
            throw new NotFoundHttpException();
        }
        $user = Client::findOne(Yii::$app->params['defaultDealer']);
        $id = Yii::$app->request->post('token');
        if ($id) {
            $user = Client::findOne(['token' => $id]);
        }
        $model = \Yii::createObject(RegistrationForm::class);
        $event = $this->getFormEvent($model);
        $model->dealer_id = $user->id;
        $this->trigger(self::EVENT_BEFORE_REGISTER, $event);

        $this->performAjaxValidation($model);

        if ($model->load(\Yii::$app->request->post()) && $model->register()) {
            $this->trigger(self::EVENT_AFTER_REGISTER, $event);
        }
    }

    public function actionBalance()
    {
        $id = Yii::$app->request->post('id');
        $user = User::findOne(['id' => $id]);
        $user->balance = Yii::$app->request->post('balance');
        $user->save();
    }
}