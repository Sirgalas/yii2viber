<?php

namespace frontend\forms;

use  dektrium\user\models\LoginForm as FormModel;

use Yii;
use yii\base\Model;

/**
 * LoginForm get user's login and password, validates them and logs the user in. If user has been blocked, it adds
 * an error to login form.
 *
 * @author Dmitry Erofeev <dmeroff@gmail.com>
 */
class LoginForm extends FormModel
{
    public $reCaptcha;


    public function rules()
    {
        $rules = parent::rules();
        $rules['reCaptcha'] = [
            ['reCaptcha'],
            \himiklab\yii2\recaptcha\ReCaptchaValidator::class,

        ];

        return $rules;
    }


}
