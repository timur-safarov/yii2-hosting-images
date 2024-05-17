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
    'controllerNamespace' => 'frontend\controllers',
    'homeUrl' => '/',
    'components' => [
        'request' => [
            'csrfParam' => '_csrf-frontend',
            'baseUrl' => '',
            // Включение JSON на прием данных
            'parsers' => [
                'application/json' => 'yii\web\JsonParser',
            ]
        ],
        'user' => [
            'identityClass' => 'common\models\User',
            'enableAutoLogin' => true,
            'identityCookie' => ['name' => '_identity-frontend', 'httpOnly' => true],
        ],
        'session' => [
            // this is the name of the session cookie used for login on the frontend
            'name' => 'advanced-frontend',
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => \yii\log\FileTarget::class,
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'urlManager' => [
            
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'enableStrictParsing' => true,

            'rules' => [
                // Включаем вывод API для наших контроллеров
                ['class' => 'yii\rest\UrlRule', 'controller' => 'api-user'],
                ['class' => 'yii\rest\UrlRule', 'controller' => 'api-image'],
                '<controller:\w+>/<action:\w+>/' => '<controller>/<action>',
                '' => 'image/upload',
                'image/delete-url-ajax/<id:\d+>' => 'image/delete-url-ajax',
                'upload' => '/image/upload',
                'catalog' => '/image/catalog',
                'zip-arhive/<file_name:[\w\W\-]+>' => '/image/zip-arhive',
                
            ],
        ],
    ],
    'params' => $params,
];
