<?php

namespace frontend\modules\api\controllers;

use Codeception\Module\Cli;
use common\entities\Balance;
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
        if(!Yii::$app->user->identity->id)
            return 'User not Auth';
        $ids = Yii::$app->user->identity->getChildList();
        $clients = Client::find()->where('coalesce(blocked_at, 0)<1');
        if ($ids != -1) {
            $clients->andWhere(['in', 'id', $ids]);
        }
        $clients->all();
        $cost=Balance::find(['user_id'=>Yii::$app->user->identity->id])->asArray()->one();
        $costarr=array_slice($cost,2);
        foreach ($clients->all() as $client) {
            $result[] = [
                'id' => $client->id,
                'email' => $client->email,
                'login' => $client->username,
                'created_at' => $client->created_at,
                'confirmed' => $client->confirmed_at ? 'Yes' : 'No',
                'blocked' => $client->blocked_at ? 'Yes' : 'No',
                'status' => $client->type,
                'balance' => $costarr ? $costarr : 'error send to administrator',

            ];
        }
        if(!$result)
            $result=['error'=>'Clients not find'];
        return $result;
    }

    public function actionRegistration()
    {
        if(!Yii::$app->user->identity->id)
            return 'User not Auth';
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
        try {
            $id = Yii::$app->request->post('id');
            if (!Yii::$app->user->identity->amParent($id))
                throw new \Exception('Нет доступа к этому пользователю');
            if (!$id)
                throw new \Exception('id not specified');
            $balanceModel=Balance::findOne(['user_id'=>$id]);
            if(!$balanceModel){
                $balanceModel=new Balance(['user_id'=>$id]);
                $balanceModel->save();

            }
            if (!Yii::$app->request->post('balance'))
                throw new \Exception('balance not specified');
            $balance = Yii::$app->request->post('balance');
            if(!Yii::$app->request->post('messenger'))
                throw new \Exception('messenger not specified');
            $messager=Yii::$app->request->post('messenger');
            $balanceModel->$messager=(int)$balance;
            if(Yii::$app->request->post('messenger_text')){
                $text_balance=$messager.'_price';
                $balance->$text_balance=Yii::$app->request->post('messenger_text');
            }
            if(!$balanceModel->save()) {
                throw new \Exception(var_dump($balanceModel->getError()));
            }
            return ['success'=>'balance update'];
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }


}