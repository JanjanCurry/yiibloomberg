<?php class TradeController extends Controller {

    public function actionChart () {
        if(empty($_REQUEST['ignorePermissions'])) {
            $this->checkAccess('tool-tra');
        }
        $data = array(
            'type' => 'trade',
            'valid' => false,
        );
        $options = array();

        $attrs = array(
            'chartId',
            'chartType',
            'compare',
            'editable',
            'endTime',
            'startTime',
            'reporter',
            'period',
            'ignorePermissions',
            'reportValue',
            'reportType',
            'indicator',
            'partner',
            'sector',
            'view',
            'col',
            'session',
            'report',
        );
        foreach ($attrs as $attr) {
            if (!empty($_REQUEST[$attr])) {
                $options[$attr] = $_REQUEST[$attr];
            }
        }

        $chart = $this->widget('application.widgets.TradeWidget', $options);

        $this->formatChartData($chart, $data);
    }

    public function actionCountry ($id) {
        $this->checkAccess('tool-tra');
        $country = DbReporters::model()->findByAttributes(array('ccode3' => $id));
        if (empty($country) || !in_array('trade', $country->type)) {
            $this->redirect(array('site/search'));
        }

        $valid = false;
        if ($country->access == 0
            || Yii::app()->user->checkToolAccess('tra', 'service-pro')
            || (Yii::app()->user->checkToolAccess('tra', 'service-ess') && $country->access == 1)) {
            $valid = true;
        }
        if (!$valid) {
            $this->checkAccess('service-pro', 'tra');
        }

        $chartOptions = $this->chartOptionDefaults();
        $chartOptions['reporter'] = $country;

        if (!empty($_REQUEST)) {
            $attrs = array(
                'compare',
                'endTime',
                'startTime',
                'reportValue',
                'reportType',
                'indicator',
                'partner',
                'sector',
            );
            foreach ($attrs as $attr) {
                if (!empty($_REQUEST[$attr])) {
                    $chartOptions[$attr] = $_REQUEST[$attr];
                }
            }
        }

        $this->render('country', array(
            'country' => $country,
            'chartOptions' => $chartOptions,
        ));
    }

    public function actionDownloadCsv () {
        $this->checkAccess('tool-tra');
        $filename = '';
        $charts = array();
        $options = array(
            'view' => 'csv',
        );
        $attrs = array(
            'compare',
            'reporter',
            'reportValue',
            'reportType',
            'indicator',
            'partner',
            'sector',
            'period',
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

                    $charts[] = $this->widget('application.widgets.TradeWidget', $temp);

                    if (!empty($filename)) {
                        $filename = '-Trade';
                        $filename .= (!empty($val['reporter']) ? '-' . $val['reporter'] : '');
                    } else {
                        $filename = (!empty($val['indicator']) ? '-' . $val['indicator'] : '');
                        $filename .= (!empty($val['reporter']) ? '-' . $val['reporter'] : '');
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
        $this->checkAccess('tool-tra');
        $filename = '';
        $charts = array();
        $options = array(
            'view' => 'pdf',
        );
        $attrs = array(
            'compare',
            'reporter',
            'reportValue',
            'reportType',
            'indicator',
            'partner',
            'sector',
            'period',
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
                        //'notes' => (!empty($val['notes']) ? $val['notes'] : null),
                        //'title' => (!empty($val['title']) ? $val['title'] : null),
                    ));
                    $temp['image'] = $image;
                    $charts[] = $this->widget('application.widgets.TradeWidget', $temp);
                }

                if (!empty($filename)) {
                    $filename = '-Trade';
                    $filename .= (!empty($val['reporter']) ? '-' . $val['reporter'] : '');
                } else {
                    $filename = (!empty($val['indicator']) ? '-' . $val['indicator'] : '');
                    $filename .= (!empty($val['reporter']) ? '-' . $val['reporter'] : '');
                }
            }
        }

        $model = new FormDownload();
        $return = $model->downloadPdf($charts,$filename);
        header('Content-Type: application/json');
        echo CJSON::encode($return);
    }

    public function actionIndex () {
        $this->checkAccess('tool-tra');
        $chartOptions = $this->chartOptionDefaults();
        $chartOptions['view'] = 'landing';


        if (!empty($_REQUEST)) {
            $attrs = array(
                'endTime',
                'startTime',
                'period',
                'reportValue',
                'reportType',
                'indicator',
                'partner',
                'sector',
            );

            foreach ($attrs as $attr) {
                if (!empty($_REQUEST[$attr])) {
                    $chartOptions[$attr] = $_REQUEST[$attr];
                }
            }
        }

        $this->render('index', array(
            'chartOptions' => $chartOptions,
        ));
    }

    public function actionCountries () {
        $this->redirect(array('site/search'));
        //$this->render('countries');
    }

}