<?php
$params = array_merge(
    require(__DIR__ . '/../../common/config/params.php'),
    require(__DIR__ . '/../../common/config/params-local.php'),
    require(__DIR__ . '/params.php'),
    require(__DIR__ . '/params-local.php')
);

return [
    'id' => 'app-backend',
    'basePath' => dirname(__DIR__),
    'controllerNamespace' => 'backend\controllers',    
    'bootstrap' => ['log'],
    'modules' => [],
    'components' => [
        'request' => [
            'csrfParam' => '_csrf-backend-frlan',
        ],
        'user' => [
            /*
            Removed For privacy
             */
        ],
        'session' => [
            // this is the name of the session cookie used for login on the backend
            'name' => /*
            Removed For privacy
             */
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
        'urlManager'=>[
            'enablePrettyUrl'=>true,
            'showScriptName'=>false,
            /*'enableStrictParsing'=>false,
            'rules' => [
                'user/type/<type:\d+>/<typeName:\w+>' => 'user/index',
            ],*/
        ],
        'frontendUrlManager'=>[
            'class' => 'yii\web\urlManager',
            'enablePrettyUrl'=>true,
            'showScriptName'=>false,
            
            'baseUrl'=>/*
            Removed For privacy
             */,
        ],
    ],
    'params' => $params,
];
