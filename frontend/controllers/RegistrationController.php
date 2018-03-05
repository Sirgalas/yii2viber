<?php

namespace frontend\controllers;

use common\entities\user\User;
use Yii;
use dektrium\user\controllers\RegistrationController as BaseRegistration;
use frontend\forms\RegistrationForm;
use dektrium\user\traits\AjaxValidationTrait;
use yii\web\NotFoundHttpException;

class RegistrationController extends BaseRegistration
{
    use AjaxValidationTrait;
    public function actionRegister($id=0)
    {
        if (!$this->module->enableRegistration) {
            throw new NotFoundHttpException();
        }
        $user=User::findOne(Yii::$app->params['defaultDealer']);
        if($id)
            $user=User::findOne(['token'=>$id]);
        /** @var RegistrationForm $model */
        $model = \Yii::createObject(RegistrationForm::class);
        $event = $this->getFormEvent($model);
        $model->dealer_id   =  $user->id;
        $this->trigger(self::EVENT_BEFORE_REGISTER, $event);

        $this->performAjaxValidation($model);

        if ($model->load(\Yii::$app->request->post()) && $model->register()) {
            $this->trigger(self::EVENT_AFTER_REGISTER, $event);

            return $this->render('/message', [
                'title'  => \Yii::t('user', 'Your account has been created'),
                'module' => $this->module,
            ]);
        }

        return $this->render('register', [
            'model'  => $model,
            'module' => $this->module,
        ]);
    }
}