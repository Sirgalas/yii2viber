<?php
namespace common\bootstrap;

use services\auth\PasswordResetService;
use yii\base\BootstrapInterface;
use Yii;
class SetUp implements BootstrapInterface
{
    public function bootstrap($app)
    {
        $conteiner= \Yii::$container;
        $conteiner->setSingleton(PasswordResetService::class,[],[
            [Yii::$app->params['supportEmail'] => Yii::$app->name . ' robot'],
            $app->mailer,
        ]);
    }

}