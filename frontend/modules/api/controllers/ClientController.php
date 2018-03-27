<?php

namespace frontend\modules\api\controllers;

use Codeception\Module\Cli;
use frontend\modules\api\components\AcViberController;
use Yii;
use frontend\forms\RegistrationForm;
use dektrium\user\traits\AjaxValidationTrait;
use common\entities\user\Client;
use yii\web\HttpException;
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
        $clients = Client::find()->where('coalesce(blocked_at, 0)<1');
        if ($ids != -1) {
            $clients->andWhere(['in', 'id', $ids]);
        }
        $clients->all();
        foreach ($clients->all() as $client) {
            $result[] = [
                'id'            => $client->id,
                'email'         => $client->email,
                'login'         => $client->username,
                'created_at'    => $client->created_at,
                'confirmed'     => $client->confirmed_at?'Yes':'No',
                'blocked'       => $client->blocked_at?'Yes':'No',
                'status'        => $client->type,
                'cost'          => $client->cost?$client->cost:'0.00',
                'balance'       => $client->balance
            ];
        }
        return $result;
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
        try{
            $id = Yii::$app->request->post('id');
            if(!$id)
                throw new \Exception('id not specified');
            $user = Client::findOne(['id' => $id]);
            if(!$user)
                throw new \Exception('client not find');
            if(!Yii::$app->request->post('balance'))
                throw new \Exception('balance not specified');
            $user->balance = Yii::$app->request->post('balance');
            if(!$user->save())
                throw new \Exception(var_dump($user->getFirstError()));
            return 'balance update';
        }catch(\Exception $e){
            return $e->getMessage();
        }
    }
}