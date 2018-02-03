<?php
namespace common\bootstrap;

use frontend\services\auth\PasswordResetService;
use frontend\services\contact\ContactService;
use yii\base\BootstrapInterface;
use Yii;
class SetUp implements BootstrapInterface
{
    public function bootstrap($app)
    {
        $container= \Yii::$container;
        $container->setSingleton(PasswordResetService::class,[],[
            [Yii::$app->params['supportEmail'] => Yii::$app->name . ' robot'],
            $app->mailer,
        ]);
        $container->setSingleton(ContactService::class, [], [
            $app->params['adminEmail']
        ]);
    }

}