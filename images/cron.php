<?php
//ini_set('memory_limit', '10000M');
//ini_set('max_execution_time', 2000);
//if($_GET['key']!='my_secret_key') die();

// change the following paths if necessary
$yii=dirname(__FILE__).'/vendor/yiisoft/yii/framework/yii.php';
$config=dirname(__FILE__).'/protected/config/cron.php';

// remove the following lines when in production mode
defined('YII_DEBUG') or define('YII_DEBUG',false);
// specify how many levels of call stack should be shown in each log message
defined('YII_TRACE_LEVEL') or define('YII_TRACE_LEVEL',3);

require_once($yii);
Yii::createConsoleApplication($config)->run();
Yii::setPathOfAlias('webroot',dirname($_SERVER['SCRIPT_FILENAME']));

