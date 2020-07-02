<?php

class Controller extends CController {

    public $breadcrumbs = array('home' => true, 'start' => array(), 'controller' => true, 'middle' => array(), 'action' => true, 'end' => array());
    public $layout = 'main';
    public $maintenance = false;
    public $metaDesc;
    public $metaTitle;
    public $user;
    public $assetVersion; //time that js/css files were last modified
    public $deferredCss;

    public function actionError () {
        if ($error = Yii::app()->errorHandler->error) {
            if (Yii::app()->request->isAjaxRequest) {
                if (YII_DEBUG && !empty(Yii::app()->user) && !empty(Yii::app()->user->id) && Yii::app()->user->checkAccess('dev')) {
                    $this->renderPartial('error', array('error' => $error));
                } else {
                    echo $error['message'];
                }
            } else {
                if(!$this->isLoggedIn()){
                    $this->registerLayout('login');
                }
                $this->render('error', array('error' => $error));
            }
            Yii::app()->end();
        }
    }

    public function autoLayout () {
        $this->layout = 'main';
        $layouts = array(
            'login' => array(
                'mail/link',
                'site/login',
                'site/loginAdmin',
                'site/forgotPassword',
                'site/passwordSent',
                'site/passwordReset',
                'site/maintenance',
                'site/unsubscribe',
                'site/terms',
                'sso/index',

            ),
            'mail' => array(
                'mail/viewBody',
            ),
        );
        $url = Yii::app()->controller->id . '/' . Yii::app()->controller->action->id;
        foreach ($layouts as $layout => $data) {
            if (in_array($url, $data)) {
                $this->layout = $layout;
            }
        }
    }

    public function beforeRender ($view) {
        $this->registerMetaData();

        $url = Yii::app()->controller->id . '/' . Yii::app()->controller->action->id;
        $filename = $url . (YII_DEBUG ? '' : '.min') . '.js';
        if (file_exists(Yii::getPathOfAlias('root') . '/js/' . $filename)) {
            $this->registerFile('js', $url);
        }

        return true;
    }

    public function isLoggedIn () {
        if (!empty(Yii::app()->user) && !empty(Yii::app()->user->id)) {
            return true;
        }

        return false;
    }

    protected function beforeAction ($action) {
        $return = parent::beforeAction($action);

        $this->setUser();

        if ($this->maintenance && (!$this->isLoggedIn() || Yii::app()->user->checkAccess('dev'))) {
            //$this->maintenance = false;
        }

        $this->forceLogin();
        $this->honeypot();
        $this->autoLayout();
        $this->registerFiles();

        return $return;
    }

    public function formatChartData ($chart, $data, $echo = true) {
        $data['error'] = $chart->error;
        $data['valid'] = false;
        if (!empty($chart->results)) {
            $data['chart'] = $chart->results;
            $data['valid'] = true;
            $data['name'] = $chart->name;
        }

        if ($echo) {
            header('Content-Type: application/json');
            echo CJSON::encode($data);
        } else {
            return $data;
        }
    }

    public function checkAccess ($authItemName, $tool = null) {
        if (!empty(Yii::app()->user)) {
            if (!empty($tool)) {
                if (Yii::app()->user->checkToolAccess($tool, $authItemName)) {
                    return true;
                }
            } else {
                if (Yii::app()->user->checkAccess($authItemName)) {
                    return true;
                }
            }

            $this->redirect(array('site/upgrade'));
        }
    }

    public function chartOptionDefaults () {
        $chartOptions = array(
            'view' => 'group',
            'startTime' => strtotime('-6 months'),
            'endTime' => strtotime('+18 months'),
            'period' => 'month',
            'init' => true,
            'editable' => true,
        );

        $sessionAttrs = array(
            'startTime' => 'chartStartTime',
            'endTime' => 'chartEndTime',
            'period' => 'chartPeriod',
        );
        foreach ($sessionAttrs as $key => $val) {
            if (isset(Yii::app()->session[$val])) {
                $chartOptions[$key] = Yii::app()->session[$val];
            } else {
                //Yii::app()->session[$val] = $chartOptions[$key];
            }
        }

        return $chartOptions;
    }

    protected function forceLogin () {
        $public = array(
            'mail/link',
            'site/login',
            'site/loginAdmin',
            'site/forgotPassword',
            'site/passwordSent',
            'site/passwordReset',
            'site/maintenance',
            'site/unsubscribe',
            'user/addWp',
            'user/existsWp',
            'sso/index',
            'saml/login',
            'saml/acs',
            'saml/meta',
        );
        $privatePublic = array(
            'mail/link',
            'site/maintenance',
            'site/unsubscribe',
        );
        $url = Yii::app()->controller->id . '/' . Yii::app()->controller->action->id;

        if ($this->isLoggedIn() && $this->user->isExpired()) {
            Yii::app()->user->logout();
            $this->redirect(array('site/logoutFlash', 'flash' => 'Your account expired on the ' . date('jS F Y', $this->user->expire) . '.'));
        }

        if ($this->isLoggedIn() && DbUserLoginLock::model()->isMultiLogin(Yii::app()->user->id)) {
            //user is logged in on 2 devices on any page
            Yii::app()->user->logout();
            $this->redirect(array('site/logoutFlash', 'flash' => 'You are logged in on another device. Please first logout of that device before logging on here.'));
        }

        if (!$this->maintenance) {
            if (in_array($url, $public)) {
                if (!in_array($url, $privatePublic) && $this->isLoggedIn()) {
                    //user is logged in on a public page
                    $this->reApplyFlashes();
                    $this->redirect(array('site/index'));
                }
                //user is not logged in and on a public page OR logged in on a mailLink page
                $this->layout = 'login';
            } else {
                if (!$this->isLoggedIn()) {
                    //user is not logged in on a private page
                    $this->redirect(array('site/login', 'returnUrl' => base64_encode(Yii::app()->request->requestUri)));
                } elseif (empty($this->user->terms) && $url != 'site/terms') {
                    //user is logged in but has not agreed to the terms and conditions
                    $this->redirect(array('site/terms', 'returnUrl' => base64_encode(Yii::app()->request->requestUri)));
                }
                //user is logged in on a private page
            }
        } elseif ($url != 'site/maintenance') {
            $this->redirect(array('site/maintenance'));
        }
    }

    protected function honeypot () {
        if (!empty($_POST) && isset($_POST['hpSec']) && !empty($_POST['name'])) {
            $this->redirect(array('site/access'));
        }
    }

    protected function performAjaxValidation ($models) {
        if (isset($_POST['ajax'])) {
            if (!empty($models)) {
                if (!is_array($models)) {
                    $models = array($models);
                }
                foreach ($models as $model) {
                    echo CActiveForm::validate($model);
                }
            }
            Yii::app()->end();
        }
    }

    public function reApplyFlashes () {
        if (!empty(Yii::app()->user)) {
            $flashes = Yii::app()->user->getFlashes();
            if (!empty($flashes)) {
                foreach ($flashes as $type => $messages) {
                    if (!empty($messages)) {
                        if (is_array($messages)) {
                            foreach ($messages as $message) {
                                Yii::app()->user->setFlash($type, $message);
                            }
                        } else {
                            Yii::app()->user->setFlash($type, $messages);
                        }
                    }
                }
            }
        }
    }

    public function renderPartial ($view, $data = null, $return = false, $processOutput = false) {
        if (strpos($view, 'snippets/')) {
            $data = (!empty($data) ? $data : array());
            $data = array_merge($data, array('mailer' => new YiiMailer()));
        }

        $output = parent::renderPartial($view, $data, $return, $processOutput);
        if ($return) {
            return $output;
        }
    }

    protected function registerFiles () {
        Yii::app()->clientScript->coreScriptPosition = CClientScript::POS_END;

        switch ($this->layout) {
            case 'login':
                //CSS
                $this->registerFile('css', 'https://fonts.googleapis.com/css?family=Montserrat:400', true);
                $this->registerFile('css', 'https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,700', true);
                $this->registerFile('css', 'https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.7/css/bootstrap.min.css');
                $this->registerFile('css', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.6.3/css/font-awesome.min.css', true);
                $this->registerFile('css', 'https://cdnjs.cloudflare.com/ajax/libs/animate.css/3.5.2/animate.min.css', true);
                $this->registerFile('css', 'https://cdnjs.cloudflare.com/ajax/libs/flat-ui/2.3.0/css/flat-ui.min.css');
                $this->registerFile('css', 'login');


                //JS
                Yii::app()->clientScript->registerCoreScript('jquery', CClientScript::POS_END);
                //Yii::app()->clientScript->registerCoreScript('jquery.ui', CClientScript::POS_END);
                $this->registerFile('js', 'https://cdnjs.cloudflare.com/ajax/libs/modernizr/2.8.3/modernizr.min.js');
                $this->registerFile('js', 'https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.13.0/moment.min.js');
                $this->registerFile('js', 'https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.7/js/bootstrap.min.js');
                $this->registerFile('js', 'https://cdnjs.cloudflare.com/ajax/libs/flat-ui/2.3.0/js/flat-ui.min.js');
                $this->registerFile('js', 'https://cdnjs.cloudflare.com/ajax/libs/wow/1.1.2/wow.min.js');
                $this->registerFile('js', 'login');
                break;

            case 'mail':
                //$this->registerFile('css','foundation');
                //$this->registerFile('css','foundation-emails');
                $this->registerFile('css', 'mail');
                break;

            case 'main':
            default:
                //CSS
                $this->registerFile('css', 'https://fonts.googleapis.com/css?family=Montserrat:400', true);
                $this->registerFile('css', 'https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,700', true);
                $this->registerFile('css', 'https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.7/css/bootstrap.min.css');
                $this->registerFile('css', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.6.3/css/font-awesome.min.css', true);
                $this->registerFile('css', 'https://cdnjs.cloudflare.com/ajax/libs/animate.css/3.5.2/animate.min.css', true);
                $this->registerFile('css', 'https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.43/css/bootstrap-datetimepicker.min.css', true);
                $this->registerFile('css', 'https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.12.1/css/bootstrap-select.min.css', true);
                $this->registerFile('css', 'https://cdnjs.cloudflare.com/ajax/libs/bootstrap-tour/0.11.0/css/bootstrap-tour.min.css', true);
                $this->registerFile('css', 'index');


                //JS
                Yii::app()->clientScript->registerCoreScript('jquery', CClientScript::POS_END);
                //Yii::app()->clientScript->registerCoreScript('jquery.ui', CClientScript::POS_END);
                //$this->registerFile('js', 'https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.11.2/jquery-ui.min.js');
                $this->registerFile('js', 'https://cdnjs.cloudflare.com/ajax/libs/modernizr/2.8.3/modernizr.min.js');
                $this->registerFile('js', 'https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.13.0/moment.min.js');
                $this->registerFile('js', 'https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.7/js/bootstrap.min.js');
                $this->registerFile('js', 'https://apis.google.com/js/api.js');
                $this->registerFile('js', 'https://cdnjs.cloudflare.com/ajax/libs/corejs-typeahead/0.11.1/typeahead.bundle.min.js');
                $this->registerFile('js', 'https://cdnjs.cloudflare.com/ajax/libs/handlebars.js/4.0.5/handlebars.min.js');
                $this->registerFile('js', 'https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.43/js/bootstrap-datetimepicker.min.js');
                $this->registerFile('js', 'https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.12.1/js/bootstrap-select.min.js');
                $this->registerFile('js', 'https://www.gstatic.com/charts/loader.js');
                //$this->registerFile('js', 'https://cdnjs.cloudflare.com/ajax/libs/bootstrap-tour/0.11.0/js/bootstrap-tour.min.js');
                //$this->registerFile('js', 'https://cdnjs.cloudflare.com/ajax/libs/html2canvas/0.5.0-beta4/html2canvas.min.js');
                $this->registerFile('js', 'https://cdnjs.cloudflare.com/ajax/libs/wow/1.1.2/wow.min.js');
                //$this->registerFile('js', 'ads');
                $this->registerFile('js', 'index');
                //$this->registerFile('js', 'tour');
                break;

            case 'pdf':
                $this->registerFile('css', 'https://fonts.googleapis.com/css?family=Montserrat:400');
                $this->registerFile('css', 'https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,700');
                $this->registerFile('css', 'pdf');
                break;
        }
    }

    public function registerFile ($type, $url, $defer = false) {
        switch ($type) {
            case 'css':
                if ($defer) {
                    if (strpos($url, "http") !== false) {
                        $this->deferredCss[] = $url;
                    } else {
                        $this->deferredCss[] = Yii::app()->baseUrl . '/css/' . $url . (YII_DEBUG ? '' : '.min') . '.css' . $this->getAssetVersion();
                    }
                } else {
                    if (strpos($url, "http") !== false) {
                        Yii::app()->clientScript->registerCssFile($url);
                    } else {
                        Yii::app()->clientScript->registerCssFile(Yii::app()->baseUrl . '/css/' . $url . (YII_DEBUG ? '' : '.min') . '.css' . $this->getAssetVersion());
                    }
                }
                break;

            case 'js':
                if (strpos($url, "http") !== false) {
                    Yii::app()->clientScript->registerScriptFile($url, CClientScript::POS_END);
                } else {
                    Yii::app()->clientScript->registerScriptFile(Yii::app()->baseUrl . '/js/' . $url . (YII_DEBUG ? '' : '.min') . '.js' . $this->getAssetVersion(), CClientScript::POS_END);
                }
                break;
        }
    }

    public function registerCarousel(){
        $this->registerFile('css', 'https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.9.0/slick.min.css');
        $this->registerFile('css', 'https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.9.0/slick-theme.min.css');
        $this->registerFile('js', 'https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.9.0/slick.min.js');
    }

    public function registerLayout ($layout) {
        $this->layout = $layout;
        Yii::app()->clientScript->reset();
        $this->registerFiles();
    }

    protected function registerMetaData () {
        if (empty($this->metaTitle)) {
            $action = $controller = $sep = '';

            if (Yii::app()->controller->id != 'site') {
                $controller = Yii::app()->controller->id;
                $sep = ' ';
            }

            if (Yii::app()->controller->action->id != 'index') {
                $action = Yii::app()->controller->action->id;
            }

            $this->metaTitle = Yii::app()->format->camelCase($action) . $sep . Yii::app()->format->camelCase($controller);
        }
        if (!empty($this->metaTitle)) {
            $this->metaTitle = $this->metaTitle . ' : ' . Yii::app()->name;
        } else {
            $this->metaTitle = Yii::app()->name;
        }
        $this->metaDesc = Yii::app()->name . ' : ' . $this->metaDesc;

        $this->pageTitle = substr($this->metaTitle, 0, 69);
        Yii::app()->clientScript->registerMetaTag(substr($this->metaDesc, 0, 160), 'description', null, array('lang' => 'en'));
    }

    protected function setUser ($id = null) {
        if (empty($id) && !empty(Yii::app()->user->id)) {
            $id = Yii::app()->user->id;
        }
        if (!empty($id)) {
            $this->user = DbUser::Model()->findByPk($id);
            DbUserActivity::model()->add();
        }

        if (empty($this->user)) {
            $this->user = new DbUser();
        }
    }

    protected function shortenUrl ($longurl) {
        // Bit.ly
        $url = "http://api.bit.ly/shorten?version=2.0.1&longUrl=$longurl&login=rm86&apiKey=R_daad42d22c06442bb42a17246052904e&format=json&history=1";

        $s = curl_init();
        curl_setopt($s, CURLOPT_URL, $url);
        curl_setopt($s, CURLOPT_HEADER, false);
        curl_setopt($s, CURLOPT_RETURNTRANSFER, 1);
        $result = curl_exec($s);
        curl_close($s);

        $obj = json_decode($result, true);
        if (!empty($obj["results"]["$longurl"]["shortUrl"])) {
            return $obj["results"]["$longurl"]["shortUrl"];
        }

        return '';
    }

    protected function triggerError () {
        //$this->redirect(array('site/error'));
        $this->actionError();
    }

    ////////////////////////////////////////
    /// ASSET VERSION
    ////////////////////////////////////////
    public function getAssetVersion () {
        if (empty($this->assetVersion)) {
            $this->setAssetVersion();
        }

        return '?v=' . $this->assetVersion;
    }

    protected function setAssetVersion () {
        $var = DbVar::model()->findByAttributes(array(
            'name' => 'asset-version-control',
            'type' => 'system',
        ));
        if (empty($var) && !empty($var->data) && !empty($var->data['time'])) {
            $this->assetVersion = $var->data['time'];
        } else {
            $this->updateAssetVersion();
        }
    }

    protected function updateAssetVersion () {
        $attrs = array(
            'name' => 'asset-version-control',
            'type' => 'system',
        );
        $time = time();

        $var = DbVar::model()->findByAttributes($attrs);
        if (empty($var)) {
            $var = new DbVar();
            $var->attributes = $attrs;
        }

        $var->data = array('time' => $time);
        if ($var->save()) {
            $this->assetVersion = $time;
        }
    }

}