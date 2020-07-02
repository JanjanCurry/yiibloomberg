<?php class CurrencyController extends Controller {

    /*public function checkAccess ($authItemName, $tool = null) {
        if(parent::checkAccess($authItemName, $tool)){
            return true;
        }
        if(empty($this->user->termsBeta)){
            $this->redirect(array('site/beta', 'returnUrl' => base64_encode(Yii::app()->request->requestUri)));
        }
    }*/

    public function actionChart () {
        if(empty($_REQUEST['ignorePermissions'])) {
            $this->checkAccess('tool-cur');
        }
        $data = array(
            'type' => 'currency',
            'valid' => false,
        );
        $options = array(
            'assetLog' => 'view',
        );

        $attrs = array(
            'chartId',
            'chartType',
            'endTime',
            'startTime',
            'period',
            'ignorePermissions',
            'item',
            'compare',
            'view',
            'editable',
            'session',
            'report',
        );
        foreach ($attrs as $attr) {
            if (!empty($_REQUEST[$attr])) {
                $options[$attr] = $_REQUEST[$attr];
            }
        }

        $chart = $this->widget('application.widgets.MarketWidget', $options);

        $this->formatChartData($chart, $data);
    }

    public function actionDownloadCsv () {
        $this->checkAccess('tool-cur');
        $filename = '';
        $charts = array();
        $options = array(
            'view' => 'csv',
        );
        $attrs = array(
            'compare',
            'item',
        );

        if (!empty($_POST)) {
            foreach ($_POST as $key => $val) {
                if (!empty($val['startTime'])) {
                    if (empty($options['startTime']) || strtotime($val['startTime']) < strtotime($options['startTime'])) {
                        $options['startTime'] = $val['startTime'];
                    }
                }
                if (!empty($val['endTime'])) {
                    if (empty($options['endTime']) || strtotime($val['endTime']) > strtotime($options['endTime'])) {
                        $options['endTime'] = $val['endTime'];
                    }
                }
            }
        }

        if (!empty($_POST)) {
            foreach ($_POST as $key => $val) {
                if (strpos($key, 'chart-item') !== false) {
                    $temp = $options;
                    foreach ($attrs as $attr) {
                        if (!empty($val[$attr])) {
                            $temp[$attr] = $val[$attr];
                        }
                    }

                    $charts[] = $this->widget('application.widgets.MarketWidget', $temp);

                    if (!empty($filename)) {
                        $filename = '-Currencies';
                    } else {
                        $filename .= (!empty($val['item']) ? '-' . $val['item'] : '');
                    }
                }
            }
        }

        $model = new FormDownload();
        $return = $model->downloadCsv($charts,$filename);
        header('Content-Type: application/json');
        echo CJSON::encode($return);
    }

    public function actionDownloadPdf(){
        $this->checkAccess('tool-cur');
        $filename = '';
        $charts = array();
        $options = array(
            'view' => 'pdf',
        );
        $attrs = array(
            'compare',
            'item',
        );

        if(!empty($_POST)) {
            foreach ($_POST as $key => $val) {
                if(!empty($val['startTime'])){
                    if(empty($options['startTime']) || strtotime($val['startTime']) < strtotime($options['startTime'])){
                        $options['startTime'] = $val['startTime'];
                    }
                }
                if(!empty($val['endTime'])){
                    if(empty($options['endTime']) || strtotime($val['endTime']) > strtotime($options['endTime'])){
                        $options['endTime'] = $val['endTime'];
                    }
                }
            }
        }

        if(!empty($_POST)){
            foreach($_POST as $key => $val) {
                $temp = $options;
                foreach ($attrs as $attr) {
                    if (!empty($val[$attr])) {
                        $temp[$attr] = $val[$attr];
                    }
                }

                if (!empty($val['img'])) {
                    $image = new Image();
                    $image->createImage($val['img'], array(
                        'filenameData' => array(),
                        'legend' => (!empty($val['legend']) ? $val['legend'] : null),
                        'source' => (!empty($val['source']) ? $val['source'] : null),
                        //'notes' => (!empty($val['notes']) ? $val['notes'] : null),
                        //'title' => (!empty($val['title']) ? $val['title'] : null),
                    ));
                    $temp['image'] = $image;
                    $charts[] = $this->widget('application.widgets.MarketWidget', $temp);
                }

                if (!empty($filename)) {
                    $filename = '-Currencies';
                } else {
                    $filename .= (!empty($val['item']) ? '-' . $val['item'] : '');
                }
            }
        }

        $model = new FormDownload();
        $return = $model->downloadPdf($charts,$filename);
        header('Content-Type: application/json');
        echo CJSON::encode($return);
    }

    public function actionIndex ($id = null) {
        $this->checkAccess('tool-cur');
        $currency = null;
        $chartOptions = $this->chartOptionDefaults();
        $chartOptions['market'] = 'currency';
        if (!empty($id)) {
            $companyId = [0];
            $user = Yii::app()->controller->user;
            if(!empty($user->companyId)){
                $companyId[] = $user->companyId;
            }
            $criteria = new CDbCriteria();
            $criteria->compare('code', $id);
            $criteria->compare('companyId', $companyId);

            $currency = DbCurrencies::model()->find($criteria);
            if (!empty($currency)) {
                if($currency->access > 1){
                    $this->checkAccess('service-pro', 'cur');
                }
                $chartOptions['item'] = $id;
            }
        }

        $urlParams = array();
        if (!empty($_REQUEST)) {
            $attrs = array(
                'endTime',
                'startTime',
                'period',
                'item',
                'compare',
            );
            foreach ($attrs as $attr) {
                if (!empty($_REQUEST[$attr])) {

                    $chartOptions[$attr] = $_REQUEST[$attr];
                }
            }

            $attrs = array(
                'item',
                'endTime',
                'startTime',
                'compare',
            );
            foreach ($attrs as $attr) {
                if (!empty($_GET[$attr])) {
                    if($attr == 'item'){
                        $urlParams['id'] = $_GET[$attr];
                    } else {
                        $urlParams[$attr] = $_GET[$attr];
                    }
                }
            }
        }

        DbUserLog::model()->add($this->user->id, $urlParams);

        $this->render('index', array(
            'chartOptions' => $chartOptions,
            'currency' => $currency,
        ));
    }

}