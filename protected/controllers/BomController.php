<?php class BomController extends Controller {

    public function beforeAction ($action) {
        //$this->redirect(['site/index']);
        return parent::beforeAction($action);
    }

    public function actionChart(){
        $data = array(
            'valid' => false,
        );
        $options = array();

        $attrs = array(
            'chartId',
            'view',
        );
        foreach($attrs as $attr){
            if(!empty($_REQUEST[$attr])) {
                $options[$attr] = $_REQUEST[$attr];
            }
        }

        $chart = $this->widget('application.widgets.BomWidget', $options);

        $this->formatChartData($chart, $data);
    }

    public function actionIndex($id = null){
        $commodity = null;
        $chartOptions = $this->chartOptionDefaults();

        $this->render('index', array(
            'chartOptions' => $chartOptions,
        ));
    }

}