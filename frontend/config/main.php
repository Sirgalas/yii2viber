<?php
$params = array_merge(
    require __DIR__ . '/../../common/config/params.php',
    require __DIR__ . '/../../common/config/params-local.php',
    require __DIR__ . '/params.php',
    require __DIR__ . '/params-local.php'
);

return [
    'id' => 'app-frontend',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'defaultRoute' => '/home/default/index',
    'controllerNamespace' => 'frontend\controllers',
    'name'=>"ViberShop24",
    'language'=>'ru',
    'components' => [
        'request' => [
            'csrfParam' => '_csrf-frontend',
                'cookieValidationKey' => 'KMIAhRwevYKdPhMvNWAhzxbeFRsFfNCD',
        ],
        'user' => [
            'identityCookie' => [
                'name'     => '_frontendIdentity',
                'path'     => '/',
                'httpOnly' => true,
            ],
        ],
        'session' => [
            'name' => 'FRONTENDSESSID',
            'cookieParams' => [
                'httpOnly' => true,
                'path'     => '/',
            ],
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'frontendUrlManager' =>require  __DIR__.'/urlManager.php',
        'urlManager' => function(){
            return Yii::$app->get('frontendUrlManager');
        },
        'backendUrlManager' => require  __DIR__.'/../../backend/config/urlManager.php',
    ],
   /* 'as access' => [
        'class' => 'yii\filters\AccessControl',
        'except' => ['/user/security/login', 'site/error'],
        'rules' => [
            [
                'allow' => true,
                'roles' => ['@'],
            ],
        ],
    ],*/
    'modules' => [
        'user' => [
            'class' => 'dektrium\user\Module',
            'as frontend' => 'dektrium\user\filters\FrontendFilter',
            'urlPrefix'=>'auth',
            'modelMap' => [
                'User' => 'frontend\entities\User',
                'LoginForm' => 'dektrium\user\models\LoginForm',
                'RegistrationForm' => 'frontend\forms\RegistrationForm',
            ],
            'controllerMap' => [
                'registration' => 'frontend\controllers\RegistrationController'
            ],
        ],
        'home' => [
            'class' => 'frontend\modules\home\Home',
        ],

    ],
    'params' => $params,
];
