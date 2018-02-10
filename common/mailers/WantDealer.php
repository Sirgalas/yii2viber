<?php
namespace common\mailers;


use common\entities\user\User;
use Yii;
class WantDealer
{
    public function send(User $user, User $dealer ): void
    {
        $sent=Yii::$app->mailer
            ->compose('dealer/wantDealer.php', ['clients' => $user])
            ->setFrom(\Yii::$app->params['adminEmail'])
            ->setTo($dealer->email)
            ->setSubject(Yii::t('mailer','You clients'.$user->username.' want dealer'))
            ->send();

    }
}