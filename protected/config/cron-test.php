<?php
Yii::setPathOfAlias('root','/home/apicom/public_html/test/');
Yii::setPathOfAlias('website','http://test.api.completeintel.com/');

// This is the configuration for yiic console application.
// Any writable CConsoleApplication properties can be configured here.
return array(
    // This path may be different. You can probably get it from `config/main.php`.
    'basePath'=>dirname(__FILE__).DIRECTORY_SEPARATOR.'..',
    'name'=>'Complete Intelligence API',

    'preload'=>array('log'),

    'import'=>array(
        'application.components.*',
        'application.models.*',
    ),
    // We'll log cron messages to the separate files
    'components'=>array(

        'authManager' => array(
            'class' => 'PermissionManager',
            'connectionID' => 'db',
        ),
        'db'=>array(
            'connectionString' => 'mysql:host=localhost;dbname=apicom_test',
            'username' => 'apicom_main',
            'password' => '@8VhUXE*3Sgv',
            'emulatePrepare' => true,
            'charset' => 'utf8',
            'enableProfiling' => true, //uncomment for debugging
            'enableParamLogging' => true, //uncomment for debugging
        ),

        'format' => array(
            'class' => 'application.components.Formatter',
        ),

        'log'=>array(
            'class'=>'CLogRouter',
            'routes'=>array(
                array(
                    'class'=>'CFileLogRoute',
                    'logFile'=>'cron.log',
                    'levels'=>'error, warning',
                ),
                array(
                    'class'=>'CFileLogRoute',
                    'logFile'=>'cron_trace.log',
                    'levels'=>'trace',
                ),
            ),
        ),

    ),

    'params'=>array(
        'vat' => 0.2,

        'name' => 'Complete Intelligence',
        'address' => 'Complete Intelligence, Main Street, London',
        'email' => 'test@test.com',
        'phone' => '0123 456 7890',

        'googleApiKey' => 'AIzaSyCJzzCI2jXQrIENxgv2PEXErdbJIlxdzHY',


        //!!!!!!!!!!!!!!!!!!!!!!! UNDER NO CIRCUMSTANCES CHANGE THESE SALTS !!!!!!!!!!!!!!!!!!!!!!!
        'privateSalt' => '$d>s48AZZtk$wz3[Y4.lqo717|s^1i',
    ),
);