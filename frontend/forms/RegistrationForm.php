<?php

namespace frontend\forms;

use dektrium\user\models\RegistrationForm as FormModel;

class RegistrationForm extends FormModel
{
    public $token;
    
    public $dealer_id;

    public function rules()
    {
        $rules = parent::rules();
        $rules['dealer_id'] = ['dealer_id', 'integer'];
        $rules['token'] = ['token', 'string', 'max' => 12];
        return $rules;
    }

    public function attributeLabels()
    {
        $labels = parent::attributeLabels();
        $labels['dealer_id'] = 'Родительский дилер';
        $labels['token'] = 'Токен';
        return $labels;
    }

}