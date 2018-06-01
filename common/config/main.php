<?php

return [

    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',

    'name' => //Removed for confidentiality,
    'sourceLanguage' => 'en',
    'language' => 'ar',
    'timeZone'=>'Asia/Riyadh',
    'bootstrap' => [
        'LanguageSwitcher',
    ],

    /*'timeZone' => 'Australia/Sydney',*/
    'modules' => [
        'gridview' => [
        'class' => 'kartik\grid\Module',]
    ],
    'components' => [
        'LanguageSwitcher' => [
            'class' => 'common\components\LanguageSwitcher',
        ],

        'cache' => [

            'class' => 'yii\caching\FileCache',

        ],

        'authManager' => [

        	'class' => 'yii\rbac\DbManager',

        ],

        'formatter' => [

	        'class' => 'yii\i18n\Formatter',

	        'dateFormat' => 'php:m/d/Y',

	        'datetimeFormat' => 'php:m/d/Y H:i:s',

	        'timeFormat' => 'php:H:i:s',

        ],

        'frontendUrlManager'=>[

            'class' => 'yii\web\urlManager',

            'enablePrettyUrl'=>true,

            'showScriptName'=>false,

            'baseUrl'=>//Removed for confidentiality,

        ],

        'backendUrlManager'=>[

            'class' => 'yii\web\urlManager',

            'enablePrettyUrl'=>true,

            'showScriptName'=>false,

            'baseUrl'=>//Removed for confidentiality,

        ],

    ],

];

