<?php
$params = array_merge(require __DIR__.'/../../common/config/params.php',
    require __DIR__.'/../../common/config/params-local.php', require __DIR__.'/params.php',
    require __DIR__.'/params-local.php');

return [
    'id' => 'app-backend',
    'basePath' => dirname(__DIR__),
    'controllerNamespace' => 'backend\controllers',
    'bootstrap' => ['log'],
    'language' => 'ru',
    'modules' => [
        'user' => [
            'class' => 'dektrium\user\Module',
            'as backend' => 'dektrium\user\filters\BackendFilter',
            'urlPrefix' => 'auth',
            'modelMap' => [
                'User' => 'backend\entities\user\User',
                'LoginForm' => 'backend\entities\user\LoginForm',
            ],
        ],
        //'i18n' => Zelenin\yii\modules\I18n\Module::class(),
        'log-viewer' => [
            'class' => 'adeattwood\logviewer\Module',
            'logLimit' => 10000,   // The amount of log items to send to the view.
            'logCacheTime' => 30,  // The amount of time the log items will be cached in seconds.
            'pageCacheTime' => 30, // The amount of time the page html will be cached in seconds.
            'tableColors' => true, // Different colors for different log levels in the table.
            'allowedIPs' => [      // The ip addressed allowed to access the logs view.
                '127.0.0.1',
                '192.168.0.*',
                '::1',
            ],
        ],
        'homepage' => [
            'class' => 'backend\modules\homepage\Homepage',
        ],

    ],
    'components' => [
        'request' => [
            'csrfParam' => '_csrf-backend',
            'cookieValidationKey' => $params['cookieValidationKey'],
        ],
        'user' => [
            'identityCookie' => [
                'name' => '_backendIdentity',
                'path' => '/',
                'httpOnly' => true,
            ],
            'identityClass' => 'common\entities\user\User',
        ],
        'session' => [
            'name' => 'BACKENDSESSID',
            'cookieParams' => [
                'httpOnly' => true,
                'path' => '/',
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
        'backendUrlManager' => require __DIR__.'/urlManager.php',
        'urlManager' => function () {
            return Yii::$app->get('backendUrlManager');
        },

        'frontendUrlManager' => require __DIR__.'/../../frontend/config/urlManager.php',
    ],
    'as access' => [
        'class' => 'yii\filters\AccessControl',
        'except' => ['site/login', 'site/error'],
        'rules' => [
            [
                'allow' => true,
                'roles' => ['@'],
            ],
        ],
    ],
    'params' => $params,
];
