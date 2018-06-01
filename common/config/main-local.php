<?php
return [
    'components' => [
        'db' => [
            'class' => 'yii\db\Connection',
            'dsn' => //Removed for confidentiality,
            'username' => //Removed for confidentiality,
            'password' => '',
            'charset' => 'utf8',
        ],
        'mailer' => [
            //Removed for confidentiality
            ],
            'viewPath' => '@common/mail',
            // send all mails to a file by default. You have to set
            // 'useFileTransport' to false and configure a transport
            // for the mailer to send real emails.
            //'useFileTransport' => false,
        ],
    ],
];
