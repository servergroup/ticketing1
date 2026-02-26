<?php

$params = require __DIR__ . '/params.php';
$db = require __DIR__ . '/db.php';
$smtpDsn = $params['smtpDsn'] ?? '';

$config = [
    'id' => 'ticketing',
    'basePath' => dirname(__DIR__),
    'timeZone' => 'Europe/Rome',

    'bootstrap' => ['log'],

    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],

    'components' => [

        'db' => $db,

        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],

        'request' => [
            'cookieValidationKey' => 't01WuOCqJYwM90-YE6WOdya_UuYUqNjO',
        ],

        'user' => [
            'identityClass' => 'app\models\User',
            'enableAutoLogin' => false,
            'enableSession' => true,
        ],

        'errorHandler' => [
            'errorAction' => 'site/error',
        ],

        'mailer' => [
            'class' => \yii\symfonymailer\Mailer::class,
            'useFileTransport' => empty($smtpDsn),
            'transport' => [
                'dsn' => $smtpDsn ?: 'smtp://localhost:25',
            ],
        ],

        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'enableStrictParsing' => false,
            'rules' => [
                'site/visualizzato/<codice_ticket>' => 'site/visualizzato',
            ],
        ],

        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'app\components\JsonFileTarget',
                    'levels' => ['error', 'warning', 'info'],
                    'logFile' => '@runtime/logs/app.json',
                    'logVars' => [],
                ],
            ],
        ],
    ],

    'on beforeRequest' => function () {
        try {
            Yii::$app->db->open();
        } catch (\Throwable $e) {
            Yii::error(
                [
                    'message' => 'Database non disponibile',
                    'exception' => $e->getMessage(),
                ],
                'database'
            );
            throw new \yii\web\ServiceUnavailableHttpException('Database non disponibile. Riprova più tardi.');
        }
    },

    'params' => $params,
];

/* ================= GII (solo in ambiente DEV) ================= */
if (YII_ENV_DEV) {
    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
        'allowedIPs' => ['*'], // permette accesso da ovunque
    ];
}


return $config;
