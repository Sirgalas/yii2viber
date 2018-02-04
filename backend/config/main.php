<?php
$params = array_merge(
    require __DIR__ . '/../../common/config/params.php',
    require __DIR__ . '/../../common/config/params-local.php',
    require __DIR__ . '/params.php',
    require __DIR__ . '/params-local.php'
);

return [
    'id' => 'app-backend',
    'basePath' => dirname(__DIR__),
    'controllerNamespace' => 'backend\controllers',
    'bootstrap' => ['log'],
    'language'=>'ru',
    'modules' => [
        'user' => [
            'class' => 'dektrium\user\Module',
           'as backend' => 'dektrium\user\filters\BackendFilter',
           'urlPrefix'=>'auth'
        ],
        'i18n' => Zelenin\yii\modules\I18n\Module::className(),
    ],
    'components' => [
        'request' => [
            'csrfParam' => '_csrf-backend',
            'cookieValidationKey'=>$params['cookieValidationKey']
        ],
        'user' => [

            'identityCookie' => [
                'name'     => '_backendIdentity',
                'path'     => '/',
                'httpOnly' => true,
            ],
        ],
        'session' => [
            'name' => 'BACKENDSESSID',
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
        'backendUrlManager' => require  __DIR__.'/urlManager.php',
        'urlManager' => function(){
            return Yii::$app->get('backendUrlManager');
        },
        'frontendUrlManager' => require  __DIR__.'/../../frontend/config/urlManager.php',
    ],
    /*'as access' => [
        'class' => 'yii\filters\AccessControl',
        'except' => ['site/login', 'site/error'],
        'rules' => [
            [
                'allow' => true,
                'roles' => ['@'],
            ],
        ],
    ],*/
    'params' => $params,
];
