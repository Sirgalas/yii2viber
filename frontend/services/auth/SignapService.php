<?php
namespace frontend\services\auth;

use common\entities\User;
use frontend\forms\SignupForm;

class SignapService
{
    public function signup(SignupForm $form): User
    {
        if(User::find()->andWhere(['username'=>$form->username])->one()){
            throw new \DomainException('Username is already exist');
        }
        if(User::find()->andWhere(['email'=>$form->username])->one()){
            throw new \DomainException('Email is already exist');
        }

        $user = User::signup(
            $form->username,
            $form->email,
            $form->password
        );

        if (!$user->save()) {
            throw new \RuntimeException(json_encode($user->errors));
        }

        return $user;

    }
}