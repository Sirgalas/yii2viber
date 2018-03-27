<?php

namespace frontend\forms;

use dektrium\user\models\RegistrationForm as FormModel;
use Yii;
use common\entities\user\User;
class RegistrationForm extends FormModel
{
    public $token;

    public $dealer_id;

    public $reCaptcha;

    public function rules()
    {
        $rules = parent::rules();
        $rules['dealer_id'] = ['dealer_id', 'integer'];
        $rules['token'] = ['token', 'string', 'max' => 12];
        $rules['reCaptcha'] = [
            ['reCaptcha'],
            \himiklab\yii2\recaptcha\ReCaptchaValidator::class,

        ];

        return $rules;
    }

    public function attributeLabels()
    {
        $labels = parent::attributeLabels();
        $labels['dealer_id'] = 'Родительский дилер';
        $labels['token'] = 'Токен';

        return $labels;
    }

    public function afterValidate()
    {

        parent::afterValidate();
        if (! $this->hasErrors() && $this->getScenario() == 'default') {
            \Yii::$app->params['captcha'] = 1;
        }
    }

    /**
     * Registers a new user account. If registration was successful it will set flash message.
     *
     * @return bool
     */
    public function register()
    {
        if (!$this->validate()){
            return false;
        }
        $this->setScenario('register');
        if (\Yii::$app->params['captcha'] != 1  ) {
            return false;
        }


        /** @var User $user */
        $user = Yii::createObject(User::classname());
        $user->setScenario('register');
        $this->loadAttributes($user);
        $user->username =   $user->generateUsername();
        if (! $user->register()) {
            return false;
        }

        //Yii::$app->session->setFlash('info', Yii::t('user',
        //                                            'Your account has been created and a message with further instructions has been sent to your email'));

        return true;
    }
}