<?php
Yii::setPathOfAlias('root', '/home/apicom/public_html/test/');
Yii::setPathOfAlias('website', 'https://test.api.completeintel.com/');

// This is the main Web application configuration. Any writable
// CWebApplication properties can be configured here.
return array(
    'basePath' => dirname(__FILE__) . DIRECTORY_SEPARATOR . '..',
    'name' => 'Complete Intelligence Pte Ltd',

    // preloading 'log' component
    'preload' => array('log', 'minScript'),

    // autoloading model and component classes
    'import' => array(
        'application.models.*',
        'application.components.*',
        'application.extensions.YiiMailer.YiiMailer',
    ),

    'modules' => array(
        // uncomment the following to enable the Gii tool
        /*'gii'=>array(
            'class'=>'system.gii.GiiModule',
            'password'=>'admin',
            'ipFilters'=>array('127.0.0.1','::1'),
        ),*/
    ),

    // application components
    'components' => array(

        'authManager' => array(
            'class' => 'PermissionManager',
            'connectionID' => 'db',
        ),

        'cache' => array(
            'class' => 'system.caching.CDbCache',
            'connectionID' => 'db',
        ),

        /*'clientScript'=>array(
            'class'=>'ext.minScript.components.ExtMinScript',
        ),*/

        'db' => array(
            'connectionString' => 'mysql:host=localhost;dbname=apicom_test',
            'username' => 'apicom_main',
            'password' => '@8VhUXE*3Sgv',
            'emulatePrepare' => true,
            'charset' => 'utf8',
            'enableProfiling' => true, //uncomment for debugging
            'enableParamLogging' => true, //uncomment for debugging
            'schemaCachingDuration' => 600,
        ),

        'errorHandler' => array(
            'errorAction' => 'site/error',
            'discardOutput' => false,
        ),

        'format' => array(
            'class' => 'application.components.Formatter',
        ),

        'log' => array(
            'class' => 'CLogRouter',
            'routes' => array(
                array(
                    'class' => 'CFileLogRoute',
                    //'levels'=>'error, warning',
                    'levels' => 'error',
                ),
                array(
                    'class' => 'CFileLogRoute',
                    'logFile' => 'application_warning.log',
                    'levels' => 'warning',
                ),
                array(
                    'class' => 'CFileLogRoute',
                    'logFile' => 'application_trace.log',
                    'levels' => 'error',
                    'filter' => array(
                        'class' => 'CLogFilter',
                        'logVars' => array(
                            '_SERVER',
                        ),
                    ),
                ),
                array(
                    'class'=>'CFileLogRoute',
                    'levels'=>'trace,log',
                    'categories' => 'system.db.CDbCommand',
                    'logFile' => 'db.log',
                ),
                /*array(
                    'class'=>'ext.yii-debug-toolbar.YiiDebugToolbarRoute',
                    //'ipFilters'=>array('127.0.0.1', '82.41.232.96'),
                    'enabled'=>YII_DEBUG,
                ),*/
            ),
        ),

        'reCaptcha' => [
            'name' => 'reCaptcha',
            'class' => 'application.extensions.yiiReCaptcha.ReCaptcha',
            'key' => '6Lcv7hcUAAAAALaF-fHYnIvvL3fOm5aVFgEBDd2I',
            'secret' => '6Lcv7hcUAAAAAO_jNm4bbNDMrEgEi3DyOFIjko1L',
        ],

        'saml' => [
            'class'=>'SamlLoader',
        ],

        'session' => array(
            'class' => 'CDbHttpSession',
            'connectionID' => 'db',
            'autoCreateSessionTable' => true,
        ),

        // uncomment the following to enable URLs in path-format
        'urlManager' => array(
            'urlFormat' => 'path',
            'showScriptName' => false,
            'rules' => array(

                //search params
                array(
                    'class' => 'application.components.UrlRuleDash',
                ),

                //Public
                'login' => 'site/login',
                'logout' => 'site/logout',
                'forgotPassword' => 'site/forgotPassword',
                'passwordSent' => 'site/passwordSent',
                'unsubscribe' => 'site/unsubscribe',

                //Site
                '' => 'site/index',
                'dashboard' => 'site/dashboard',
                'access' => 'site/access',
                'error' => 'site/error',
                'faq' => 'site/faq',
                'upgrade' => 'site/upgrade',
                'search' => 'site/search',
                'how-to' => 'site/howTo',

                //Commodity
                'commodity' => 'commodity/index',
                'commodity/code/<id:\w+>' => 'commodity/index',

                //Currency
                'currency' => 'currency/index',
                'currency/code/<id:\w+>' => 'currency/index',

                //Equity
                'equity' => 'equity/index',
                'equity/code/<id:\w+>' => 'equity/index',

                //Report
                'report' => 'report/countries',
                'report/country/<id:\w+>' => 'report/country',
                'report/code/<id:\w+>' => 'report/country',

                //Trade
                'trade' => 'trade/index',
                'trade/country/<id:\w+>' => 'trade/country',
                'trade/code/<id:\w+>' => 'trade/country',

                //Macro
                'economics' => 'macro/index',
                'macro' => 'macro/index',
                'economics/<action:\w+>' => 'macro/<action>',

                //Mail
                'mail/link/<hash>' => 'mail/link',

                //Admin
                'admin/partnerGroups/<page:\w+>' => 'admin/partnerGroups',

                //GII
                /*'gii'=>'gii',
                'gii/<controller:\w+>'=>'gii/<controller>',
                'gii/<controller:\w+>/<action:\w+>'=>'gii/<controller>/<action>',*/

                //Default
                '<controller:\w+>/<action:\w+>/<id:\d+>' => '<controller>/<action>',
                '<controller:\w+>' => '<controller>/index',
                '<controller:\w+>/<action:\w+>' => '<controller>/<action>',
            ),
        ),

        'user' => array(
            // enable cookie-based authentication
            'allowAutoLogin' => true,
            'autoRenewCookie' => true,
            'class' => 'WebUser',
        ),

        'widgetFactory' => array(
            'class' => 'CWidgetFactory',
            'widgets' => array(
                'CJuiAccordion' => array('cssFile' => false),
                'CJuiAutoComplete' => array('cssFile' => false),
                'CJuiButton' => array('cssFile' => false),
                'CJuiDatePicker' => array('cssFile' => false),
                'CJuiDialog' => array('cssFile' => false),
                'CJuiDraggable' => array('cssFile' => false),
                'CJuiDroppable' => array('cssFile' => false),
                'CJuiInputWidget' => array('cssFile' => false),
                'CJuiProgressBar' => array('cssFile' => false),
                'CJuiResizable' => array('cssFile' => false),
                'CJuiSelectable' => array('cssFile' => false),
                'CJuiSlider' => array('cssFile' => false),
                'CJuiSliderInput' => array('cssFile' => false),
                'CJuiSortable' => array('cssFile' => false),
                'CJuiTabs' => array('cssFile' => false),
                'CJuiTouch' => array('cssFile' => false),
                'CJuiWidget' => array('cssFile' => false),
                'CLinkPager' => array(
                    'htmlOptions' => array(
                        'class' => 'pagination'
                    ),
                    'header' => false,
                    'maxButtonCount' => 5,
                    'cssFile' => false,
                ),
                'CGridView' => array(
                    'htmlOptions' => array(
                        'class' => 'table-responsive'
                    ),
                    'pagerCssClass' => 'dataTables_paginate paging_bootstrap',
                    'itemsCssClass' => 'table table-striped table-hover',
                    'cssFile' => false,
                    'summaryCssClass' => 'dataTables_info',
                    'summaryText' => 'Showing {start} to {end} of {count} entries',
                    'template' => '{items}<div class="col-md-5 col-sm-12">{summary}</div><div class="col-md-7 col-sm-12">{pager}</div><div class="clearfix"></div><br />',
                ),
            ),
        ),

    ),

    'onBeginRequest' => (YII_DEBUG ? create_function('$event', 'return ob_start();') : create_function('$event', 'return ob_start("ob_gzhandler");')),
    'onEndRequest' => create_function('$event', 'ob_end_flush();'),

    'controllerMap' => array(
        'min' => array(
            'class' => 'ext.minScript.controllers.ExtMinScriptController',
        ),
    ),

    // application-level parameters that can be accessed
    // using Yii::app()->params['paramName']
    'params' => array(s
        'name' => 'Complete Intelligence Pte Ltd',
        'address' => 'Complete Intelligence, 181A, Telok Ayer Street, Singapore, 068629',
        'email' => 'it@completeintel.com',
        'phone' => '0123 456 7890',

        'googleApiKey' => 'AIzaSyCJzzCI2jXQrIENxgv2PEXErdbJIlxdzHY',

        //'url-pricing' => 'http://www.completeintel.com/index.php/subscription/',
        'url-pricing' => 'http://www.completeintel.com/index.php/contact/',


        //!!!!!!!!!!!!!!!!!!!!!!! UNDER NO CIRCUMSTANCES CHANGE THESE SALTS !!!!!!!!!!!!!!!!!!!!!!!
        'privateSalt' => '$d>s48AZZtk$wz3[Y4.lqo717|s^1i',
    ),
);

