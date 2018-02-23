<?php
namespace common\mailers;


use common\entities\user\User;
use Yii;
class WantDealer
{
    public function send(User $user,  $dealer )
    {
        if ($dealer){
            $to = $dealer->email;
        } else {
            $to = Yii::$app->params['supportEmail'];
        }
        $sent=Yii::$app->mailer;
        $sent->compose('dealer/wantDealer.php', ['clients' => $user])
            ->setFrom(\Yii::$app->params['adminEmail'])
            ->setTo($to)
            ->setSubject(Yii::t('mailer','You clients'.$user->username.' want dealer'))
            ->send();
    }
}