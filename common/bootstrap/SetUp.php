<?php
namespace common\bootstrap;

use common\mailers\WantDealer;
use frontend\services\contact\ContactService;
use yii\base\BootstrapInterface;
use Yii;
class SetUp implements BootstrapInterface
{
    public function bootstrap($app)
    {
        $container= \Yii::$container;
        $container->setSingleton(ContactService::class, [], [
            $app->params['adminEmail']
        ]);
       /* $container->setSingleton(WantDealer::class, [], [
            $app->params['adminEmail']
        ]);*/
    }

}