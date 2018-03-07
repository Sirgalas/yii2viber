<?php
/** @var array $params */
return  [
    'class'=>'yii\web\UrlManager',
    'hostInfo'=>$params['frontendHostInfo'],
    'enablePrettyUrl' => true,
    'showScriptName' => false,
    'rules' => [
        //'' => 'site/index',
        'auth/register/<id:\w+>' => 'user/registration/register',
        '<_a:login|logout>' => 'site/<_a>',
        '<_c:[\w\-]+>' => '<_c>/index',
        '<_c:[\w\-]+>/<id:\d+>' => '<_c>/view',
        '<_c:[\w\-]+>/<_a:[\w-]+>' => '<_c>/<_a>',
        '<_c:[\w\-]+>/<id:\d+>/<_a:[\w\-]+>' => '<_c>/<_a>',
        'tst/viber/notification'=>'tst/viber/notification',

    ],
]; ?>