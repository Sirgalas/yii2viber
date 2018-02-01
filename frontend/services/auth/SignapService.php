<?php
/**
 * Created by PhpStorm.
 */

namespace frontend\services\auth;

use common\entities\User;
use frontend\forms\SignupForm;

class SignapService
{
    public function signup(SignupForm $form): User
    {
        if(User::find()->andWhere(['username'=>$form->username])){
            throw new \DomainException('Username is already exist');
        }
        if(User::find()->andWhere(['email'=>$form->username])){
            throw new \DomainException('Email is already exist');
        }

        $user = User::signup(
            $form->username,
            $form->email,
            $form->password
        );

        if (!$user->save()) {
            throw new \RuntimeException('Saving Error');
        }

        return $user;

    }
}