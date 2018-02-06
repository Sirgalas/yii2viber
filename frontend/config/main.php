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
    'bootstrap' => ['log','debug'],
    'controllerNamespace' => 'frontend\controllers',
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
    'modules' => [
        'user' => [
            'class' => 'dektrium\user\Module',
            'as frontend' => 'dektrium\user\filters\FrontendFilter',
        ],
        'debug' => [
            'class' => 'yii\\debug\\Module',
            'panels' => [
                'mongodb' => [
                    'class' => 'yii\\mongodb\\debug\\MongoDbPanel',
                    // 'db' => 'mongodb', // ID MongoDB компонента, по умолчанию `db`. Раскоментируйте и измените эту строку, если вы регистрируете компонент MongoDB с другим ID.
                ],
            ],
        ],
    ],
    'params' => $params,
];
