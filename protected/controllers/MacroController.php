<?php class MacroController extends Controller {

    public function actionChart () {
        if(empty($_REQUEST['ignorePermissions'])) {
            $this->checkAccess('tool-tra');
        }
        $data = array(
            'type' => 'macro',
            'valid' => false,
        );
        $options = array();

        $attrs = array(
            'chartId',
            'chartType',
            'compare',
            'endTime',
            'startTime',
            'reporter',
            'period',
            'ignorePermissions',
            'macro',
            'editable',
            'view',
            'session',
            'report',
        );
        foreach ($attrs as $attr) {
            if (!empty($_REQUEST[$attr])) {
                $options[$attr] = $_REQUEST[$attr];
            }
        }

        $chart = $this->widget('application.widgets.MacroWidget', $options);

        $this->formatChartData($chart, $data);
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
            'period',
            'macro',
            'notes',
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

                    $charts[] = $this->widget('application.widgets.MacroWidget', $temp);

                    if (!empty($filename)) {
                        $filename = '-Macros';
                    } else {
                        $filename = (!empty($val['macro']) ? '-' . $val['macro'] : '');
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
            'period',
            'macro',
            'notes',
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
                    $charts[] = $this->widget('application.widgets.MacroWidget', $temp);
                }

                if (!empty($filename)) {
                    $filename = '-Macros';
                } else {
                    $filename = (!empty($val['macro']) ? '-' . $val['macro'] : '');
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

        if (!empty($_REQUEST)) {
            $attrs = array(
                'endTime',
                'startTime',
                'period',
                'macro',
                'compare',
                'reporter',
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

}