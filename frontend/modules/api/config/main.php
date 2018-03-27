<?php
return [
    'id' => 'api',
    'components' => [
        'response' => [
            'class'=> '\yii\web\Response',
            'formatters' => [
                \yii\web\Response::FORMAT_JSON => [
                    'class' => 'yii\web\JsonResponseFormatter',
                    'prettyPrint' => YII_DEBUG, // используем "pretty" в режиме отладки
                    'encodeOptions' => JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE,
                ],
            ],
        ],
        'request' => [
            'class'=>'yii\web\Request',
            'parsers' => [
                'application/json' => 'yii\web\JsonParser',
            ]
        ],
        'urlManager' => [
            'class'=>'yii\web\UrlManager',
            'enablePrettyUrl'       => true,
            'enableStrictParsing'   => true,
            'showScriptName'        => false,
            'rules' => [
                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => '/client',
                    'extraPatterns' => [
                        'POST index'            =>  'index',
                        'OPTIONS index'         =>  'index',
                        'POST registration'     =>  'registration',
                        'OPTIONS registration'  =>  'registration',
                        'POST redact balance'   =>  'balance',
                        'OPTIONS balance'       =>  'balance',
                        'DELETE deleteUser'     =>  'delete-user',
                        'OPTIONS deleteUser'    =>  'delete-user',
                    ]
                ],
                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => '/contact',
                    'extraPatterns' => [
                        'POST index'        => 'index',
                        'OPTIONS index'     => 'index',
                        'POST create'       => 'create',
                        'OPTIONS create'    => 'create',
                        'POST update'       => 'update',
                        'OPTIONS update'    => 'update',
                        'DELETE delete'     => 'delete',
                        'OPTIONS delete'    => 'delete',
                    ]
                ],
                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => '/message',
                    'extraPatterns' => [
                        'POST index'        => 'index',
                        'OPTIONS index'     => 'index',
                        'POST create'       => 'create',
                        'OPTIONS create'    => 'create',
                        'POST update'       => 'update',
                        'OPTIONS update'    => 'update',
                        'DELETE delete'     => 'delete',
                        'OPTIONS delete'    => 'delete',
                    ]
                ],
                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => 'report',
                    'extraPatterns' => [
                        'GET index'     =>  'index',
                        'OPTIONS index' =>  'index'
                    ]
                ],
            ],
        ]
    ],
];