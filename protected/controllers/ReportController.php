<?php class ReportController extends Controller {

    /*public function checkAccess ($authItemName, $tool = null) {
        if(parent::checkAccess($authItemName, $tool)){
            return true;
        }
        if(empty($this->user->termsBeta)){
            $this->redirect(array('site/beta', 'returnUrl' => base64_encode(Yii::app()->request->requestUri)));
        }
    }*/

    public function actionCountries () {
        $this->checkAccess('tool-tra');
        $model = new FormReportCountry();
        $model->setDefaults();

        $this->registerFile('js','chart/edit');
        $this->render('countries', array(
            'model' => $model,
        ));
    }

    public function actionCountry ($id) {
        $this->checkAccess('tool-tra');
        $error = array();
        $resend = false;

        $country = DbReporters::model()->findByAttributes(array('ccode3' => $id));
        if (empty($country)) {
            $error[] = 'Country ' . $id . ' is not available';
        }

        //check user has access to this country
        $valid = false;
        if ($country->access == 0
            || Yii::app()->user->checkToolAccess('tra', 'service-pro')
            || (Yii::app()->user->checkToolAccess('tra', 'service-ess') && $country->access == 1)) {
            $valid = true;
        }
        if (!$valid) {
            $error[] = 'Please upgrade your account to see data for ' . $country->name;
        }

        $model = new FormReportCountry();
        $model->setDefaults();
        $model->reporter = $country;

        if(!empty($_POST)) {
            $attrs = array(
                'period',
                'startTime',
                'endTime',
                'format',
                'reports',
                'charts',
            );
            foreach($attrs as $attr){
                if(!empty($_POST[$attr])){
                    $model->$attr = $_POST[$attr];
                }
            }
        }

        //create file if it doesn't yet exist, otherwise serve existing version
        if (!empty($_POST['method']) && $_POST['method'] == 'exists') {
            if ($valid && !file_exists($model->file)) {
                $valid = false;
            }
            $valid = false;//temp override
        } else if ($valid) {
            $valid = $model->save();

            if (!$valid && empty($error)) {
                $error[] = 'Failed to generate report for ' . $country->name;
            }
        }

        header('Content-Type: application/json');
        echo CJSON::encode(array(
            'valid' => $valid,
            'resend' => $model->resend,
            'filename' => $model->filename,
            'url' => $model->url,
            'error' => $error,
        ));
    }

    public function actionMarkets () {
        $valid = false;
        if(Yii::app()->user->checkAccess('tool-com') || Yii::app()->user->checkAccess('tool-cur') || Yii::app()->user->checkAccess('tool-equ')){
            $valid = true;
        }
        if(!$valid){
            $this->redirect(array('site/upgrade'));
        }
        $model = new FormReportMarket();
        $model->setDefaults();

        $this->registerFile('js','chart/edit');
        $this->render('markets', array(
            'model' => $model,
        ));
    }

    public function actionMarket () {
        $error = array();
        $resend = false;

        if(Yii::app()->user->checkAccess('tool-com') || Yii::app()->user->checkAccess('tool-cur') || Yii::app()->user->checkAccess('tool-equ')){
            $valid = true;
        }
        if(!$valid){
            $error[] = 'Please upgrade your account to see data for ' . $country->name;
        }

        $model = new FormReportMarket();
        $model->setDefaults();

        if(!empty($_POST)) {
            $attrs = array(
                'assets',
                'charts',
                'format',
            );
            foreach($attrs as $attr){
                if(!empty($_POST[$attr])){
                    $model->$attr = $_POST[$attr];
                }
            }
        }

        //create file if it doesn't yet exist, otherwise serve existing version
        if ($valid) {
            $valid = $model->save();

            if (!$valid && empty($error)) {
                $error[] = 'Failed to generate report';
            }
        }

        header('Content-Type: application/json');
        echo CJSON::encode(array(
            'valid' => $valid,
            'resend' => $model->resend,
            'filename' => $model->filename,
            'url' => $model->url,
            'error' => $error,
        ));
    }

}