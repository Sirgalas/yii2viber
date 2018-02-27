<?php
return [
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm' => '@vendor/npm-asset',
    ],
    'name'=>'Личный кабинет',
    'vendorPath' => dirname(dirname(__DIR__)).'/vendor',
    'bootstrap' => [
        'log',
        'common\bootstrap\SetUp',
    ],
    'components' => [
        'resourceManager' => [
            'class' => 'common\components\FileSystemResourseManager',
            'basePath' => dirname(dirname(__DIR__)).'/files',
            'baseUrl' => '/files',
        ],
        'cache' => [
            'class' => 'yii\caching\MemCache',
            'useMemcached' => true,
        ],
        'formatter' => [
            'class' => 'yii\i18n\Formatter',
            'dateFormat' => 'php:d.m.Y',
            'datetimeFormat' => 'php:j F, H:i',
            'timeFormat' => 'php:H:i:s',
            'defaultTimeZone' => 'Europe/Moscow',
            'locale' => 'ru-RU',
            'currencyCode' => 'руб',
        ],
        'i18n' => [
            'class' => Zelenin\yii\modules\I18n\components\I18N::className(),
            'languages' => ['ru-RU'],
            'translations' => [
                'yii' => [
                    'class' => yii\i18n\DbMessageSource::className(),
                ],
            ],
        ],
        'log' => [
            'flushInterval' => 1,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['info'],
                    'exportInterval' => 1,
                    'logVars' => [/*'_GET', '_POST', '_FILES', '_COOKIE', '_SESSION'*/],
                    'categories' => ['viber'],
                    'logFile' => '@common/runtime/logs/viber/viber.log',
                    'maxFileSize' => 1024 * 2*1024,
                    'maxLogFiles' => 200,
                ],
            ],
        ],
    ],
    'modules' => [
        //'modules' => [
        //    'api' => [
        //        'class' => 'common\modules\api\Module',
        //    ],
        //],
        'user' => [
            'class' => 'dektrium\user\Module',
            'enableUnconfirmedLogin' => true,
            'emailChangeStrategy' => 1,
            'admins' => ['Sergalas','kev'],
            'modelMap' => [
                'User' => 'common\entities\user\User',
            ],
        ],
        'gridview' => [
            'class' => 'kartik\grid\Module',
        ],
        'datecontrol' => [
            'class' => 'kartik\datecontrol\Module',

            // format settings for displaying each date attribute
            'displaySettings' => [
                'date' => 'd-m-Y',
                'time' => 'H:i:s A',
                'datetime' => 'd-m-Y H:i:s A',
            ],

            // format settings for saving each date attribute
            'saveSettings' => [
                'date' => 'Y-m-d',
                'time' => 'H:i:s',
                'datetime' => 'Y-m-d H:i:s',
            ],

            // automatically use kartik\widgets for each of the above formats
            'autoWidget' => true,
        ],



    ],
];
