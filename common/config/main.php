<?php
return [
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'bootstrap'=>[
        'log',
        'common\bootstrap\SetUp',
    ],
    'components' => [
        'cache' => [
            'class' => 'yii\caching\MemCache',
            'useMemcached'=>true
        ],
        'formatter' => [
            'class' => 'yii\i18n\Formatter',
            'dateFormat' => 'php:d.m.Y',
            'datetimeFormat' => 'php:j F, H:i',
            'timeFormat' => 'php:H:i:s',
            'defaultTimeZone' => 'Europe/Moscow',
            'locale' => 'ru-RU'
        ],
        'i18n' => [
            'class' => Zelenin\yii\modules\I18n\components\I18N::className(),
            'languages' => ['ru-RU'],
            'translations' => [
                'yii' => [
                    'class' => yii\i18n\DbMessageSource::className()
                ]
            ]
        ],
    ],
    'modules' => [
        'user' => [
            'class' => 'dektrium\user\Module',
            'enableUnconfirmedLogin'=>true,
        ],
    ],
];
