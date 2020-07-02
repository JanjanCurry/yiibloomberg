<?php

class SiteController extends Controller {

    public function actionAccess () {
        $this->render('access');
    }

    public function actionBeta () {
        $redirect = false;
        if (!empty($this->user->termsBeta)) {
            $redirect = true;
        }

        if (!empty($_POST['DbUser'])) {
            $this->user->attributes = $_POST['DbUser'];
            if (!empty($this->user->termsBeta) && $this->user->save()) {
                $redirect = true;
            } else {
                $this->user->addError('termsBeta', 'Please agree to the beta terms and conditions to proceed.');
            }
        }

        if ($redirect) {
            if (!empty($_GET['returnUrl'])) {
                $this->redirect(base64_decode($_GET['returnUrl']));
            } else {
                $this->redirect(array('site/index'));
            }
        }

        $this->registerLayout('login');
        $this->render('beta');
    }

    public function actionChartDateLimit () {
        $data = array(
            'min' => '1 Jan 2010',
            'max' => '1 Dec 2020',
        );

        if (!empty($_REQUEST['type'])) {
            $type = $_REQUEST['type'];

            $log = DbVar::model()->findByAttributes(array('name' => 'chart-date-limit'));
            if (!empty($log) && !empty($log->data) && !empty($log->data[$type])) {
                if (!empty($log->data[$type]['min'])) {
                    $data['min'] = $log->data[$type]['min'];
                }
                if (!empty($var->data[$type]['max'])) {
                    $data['max'] = $log->data[$type]['max'];
                }
            }

            if (!Yii::app()->user->checkToolAccess(DbUserService::convertTool($type), 'date-full')) {
                $data['max'] = '1 ' . date('M Y', strtotime('+12 months'));
            }
        }

        if (!empty($type)) {
            switch ($type) {
                case 'commodity':
                case 'currency':
                case 'equity':
                    $data['max'] = '1 ' . date('M Y', strtotime('+12 months'));
                    break;
            }
        }

        header('Content-Type: application/json');
        echo CJSON::encode($data);
    }

    public function actionDashboard () {
        echo $this->render('dashboard');
    }

    public function actionDownloadImage () {
        $return = [
            'valid' => false,
        ];
        $options = [
            'shortenUrl' => true,
            'reportType' => [],
            'legend' => null,
            'notes' => null,
            'source' => null,
            'title' => null,
        ];

        $filenameData = [];
        $parts = ['macro', 'indicator', 'reporter', 'commodity', 'currency', 'equity'];
        foreach ($parts as $part) {
            if (!empty($_POST[$part])) {
                $options['reportType'][] = $_POST[$part];
            }
        }

        $parts = ['legend', 'notes', 'source', 'title'];
        foreach ($parts as $part) {
            if (!empty($_POST[$part])) {
                $options[$part] = $_POST[$part];
            }
        }

        $model = new FormDownload();
        $result = $model->downloadImage($_POST['img'], $options);
        if (!empty($result)) {
            $return['filename'] = $result['filename'];
            $return['shortUrl'] = $result['shortUrl'];
            $return['url'] = $result['url'];
            $return['valid'] = true;
        }

        header('Content-Type: application/json');
        echo CJSON::encode($return);
    }

    public function actionDownloadAllImage () {
        $return = [
            'valid' => false,
        ];
        $options = [
            'shortenUrl' => true,
            'img' => [],
            'reportType' => [],
            'legend' => [],
            'notes' => [],
            'title' => [],
        ];

        $parts = array('type');
        foreach ($parts as $part) {
            if (!empty($_POST[$part])) {
                $filenameData[] = $_POST[$part];
            }
        }

        if (!empty($_POST['charts'])) {
            $parts = ['legend', 'notes', 'title', 'img'];
            foreach ($_POST['charts'] as $chart) {
                foreach ($parts as $part) {
                    if (!empty($chart[$part])) {
                        $options[$part][] = $chart[$part];
                    }
                }
            }
        }

        $model = new FormDownload();
        $result = $model->downloadImage($options['img'], $options);
        if (!empty($result)) {
            $return['filename'] = $result['filename'];
            $return['shortUrl'] = $result['shortUrl'];
            $return['url'] = $result['url'];
            $return['valid'] = true;
        }

        header('Content-Type: application/json');
        echo CJSON::encode($return);
    }

    public function actionFaq () {
        $this->render('faq');
    }

    public function actionFilterList () {
        $hide = $list = array();
        $valid = true;
        $opts = [
            'filter' => [],
            'indicator' => null,
            'macro' => null,
            'period' => null,
            'restrict' => [],
            'reporter' => null,
            'reports' => null,
            'returnPartner' => null,
            'type' => null,
            'strictHide' => null,
            'variant' => null,
        ];

        foreach ($opts as $key => $val) {
            if (!empty($_POST[$key])) {
                $opts[$key] = $_POST[$key];
            }
        }

        if (!empty($_POST['filter'])) {
            $opts['filter'] = (!is_array($_POST['filter']) ? explode(',', $_POST['filter']) : $_POST['filter']);
        }
        if (!empty($_POST['restrict'])) {
            $opts['restrict'] = (!is_array($_POST['restrict']) ? explode(',', $_POST['restrict']) : $_POST['restrict']);
        }

        $results = DbReports::model()->calcData($opts);


        header('Content-Type: application/json');
        echo CJSON::encode($results);
    }

    public function actionForgotPassword () {
        $login = new LoginForm();
        $login->scenario = 'reset';
        if (!empty($_POST['LoginForm'])) {
            $login->attributes = $_POST['LoginForm'];
            if ($login->validate() && $login->forgotPassword()) {
                $this->redirect(array('site/passwordSent'));
            }
        }
        $this->render('forgotPassword', array(
            'login' => $login,
        ));
    }

    public function actionHowTo () {
        $this->render('howTo');
    }

    public function actionIndex () {
        if (!empty($_POST['displayPref'])) {
            $this->user->addSerialised('preferences', array(
                'home-macro-fav-tab' => $_POST['macro-fav-tab'],
                'home-trade-fav-tab' => $_POST['trade-fav-tab'],
            ));
            $this->user->save();
            Yii::app()->end();
        }

        $this->registerFile('js', 'site/dashboard');
        $this->render('index');
    }

    public function actionLogin () {
        $login = new LoginForm;
        if (!empty($_POST['LoginForm'])) {
            $login->attributes = $_POST['LoginForm'];
            if ($login->validate() && $login->login()) {
                $user = DbUser::model()->findByPk(Yii::app()->user->id);
                if (!empty($user)) {
                    //Yii::app()->user->setFlash('info', 'Hello ' . $user->fullName);
                    if ($user->tourLogin == 1) {
                        //$user->tourLogin = 0;
                        $user->tour = 0;
                        $user->save();
                    }
                }

                Yii::app()->session['pageLoader'] = 1;
                if (!empty($_GET['returnUrl'])) {
                    $this->redirect(base64_decode($_GET['returnUrl']));
                } else {
                    $this->redirect(array('site/index'));
                }
            }
        }
        $this->render('login', array(
            'login' => $login,
        ));
    }

    public function actionLoginAdmin () {
        $login = new LoginFormAdmin();
        $login->scenario = 'login';
        if (!empty($_POST['LoginFormAdmin'])) {
            $login->attributes = $_POST['LoginFormAdmin'];
            if ($login->validate() && $login->login()) {
                $user = DbUser::model()->findByPk(Yii::app()->user->id);
                if (!empty($user)) {
                    //Yii::app()->user->setFlash('info', 'Hello ' . $user->fullName);
                    if ($user->tourLogin == 1) {
                        //$user->tourLogin = 0;
                        $user->tour = 0;
                        $user->save();
                    }
                }

                Yii::app()->session['pageLoader'] = 1;
                if (!empty($_GET['returnUrl'])) {
                    $this->redirect(base64_decode($_GET['returnUrl']));
                } else {
                    $this->redirect(array('site/index'));
                }
            }
        }
        $this->render('loginAdmin', array(
            'login' => $login,
        ));
    }

    public function actionLogout () {
        if ($this->isLoggedIn()) {
            DbUserLoginLock::model()->logout(Yii::app()->user->id);
            Yii::app()->user->logout();
        }
        $this->redirect(array('site/login'));
    }

    public function actionLogoutFlash () {
        $this->reApplyFlashes();
        if (!empty($_GET['flash'])) {
            Yii::app()->user->setFlash('danger', $_GET['flash']);
        }
        $this->redirect(array('site/login'));
    }

    public function actionMaintenance () {
        $this->render('maintenance');
    }

    public function actionMethodology () {
        $this->render('methodology');
    }

    public function actionPasswordReset ($hash) {
        //get user based on hash sting that was sent to them
        $user = DbUser::model()->findByAttributes(array(
            'hash' => $hash,
        ));
        if (empty($user)) {
            $this->redirect(array('site/login'));
        }
        $user->scenario = 'changePassword';
        if (!empty($_POST['DbUser'])) {
            $user->attributes = $_POST['DbUser'];
            //reset the user hash so it cant be re-used after saving
            if ($user->resetHash()) {
                //attempt to automatically login otherwise send to login page
                $login = new LoginForm;
                $login->username = $user->username;
                $login->password = $user->passwordNew;
                if ($login->validate() && $login->login()) {
                    $this->redirect(array('site/index'));
                }

                $this->redirect(array('site/login'));
            }
        }

        //reset password fields so encrypted string is not shown in form
        $user->password = null;
        $user->passwordConfirm = null;
        $this->render('changePassword', array(
            'user' => $user,
        ));
    }

    public function actionPasswordSent () {
        $login = new LoginForm();
        $this->render('forgotPasswordSent', array(
            'login' => $login,
        ));
    }

    public function actionUpgrade () {
        //$this->redirect(Yii::app()->params['url-pricing']);
        $this->render('premium');
    }

    public function actionSearch () {
        //$this->redirect(array('trade/countries'));
        Yii::app()->controller->registerFile('js', 'chart');
        $this->render('search');
    }

    public function actionShare () {
        $valid = false;
        $contact = new FormShare();

        $this->performAjaxValidation($contact);

        if (!empty($_POST['FormShare'])) {
            $contact->attributes = $_POST['FormShare'];
            $valid = $contact->send();
        }

        header('Content-Type: application/json');
        echo CJSON::encode(array(
            'valid' => $valid,
        ));
    }

    public function actionTerms () {
        if (!empty($this->user->terms)) {
            $this->redirect(array('site/index'));
        }

        if (!empty($_POST['DbUser'])) {
            $this->user->attributes = $_POST['DbUser'];
            if (!empty($this->user->terms) && $this->user->save()) {
                $this->redirect(array('site/index'));
            } else {
                $this->user->addError('terms', 'Please agree to the terms and conditions to proceed.');
            }
        }

        $this->render('terms');
    }

    public function actionUnsubscribe () {
        $login = new LoginForm();
        $login->scenario = 'reset';
        $valid = false;
        if (!empty($_POST['LoginForm'])) {
            $login->attributes = $_POST['LoginForm'];
            if ($login->validate()) {
                $login->unsubscribe();
                $valid = true;
            }
        }
        $this->render('unsubscribe', array(
            'login' => $login,
            'valid' => $valid,
        ));
    }

    public function actionTest () {
        unset(Yii::app()->session['chartPeriod']);
        unset(Yii::app()->session['chartStartTime']);
        unset(Yii::app()->session['chartEndTime']);

        /*$options = [
            'report' => 'default',
            'session' => 'set',
            'editable' => 1,
            'partner' => 'EUU',
            'reporter' => 'CAN',
            'indicator' => 'flow-partner-top10-iv',
            'reportValue' => 'IV',
            'reportType' => 'partner-top10',
            'period' => 'month',
            'endTime' => 'May 2020',
            'startTime' => 'Nov 2018',
            'col' => 'col-sm-12',
            'chartType' => 'line',
            'chartId' => 'chart-item-525602',
        ];
        $chart = $this->widget('application.widgets.TradeWidget', $options);
        Yii::app()->format->debug($chart, true);*/

        /*$users = DbUser::model()->findAll();
        foreach($users as $user){
            $user->setDefaultFavorites('dash');
        }*/

        //$m = new Maintenance();
        //$m->topAssets();
        exit;
    }
}