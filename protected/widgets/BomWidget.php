<?php class BomWidget extends ChartWidget {

    //models
    public $item;

    //processing vars
    protected $fieldList = array(
        'item',
        'chartId',
        'view',
    );

    /*
     * Init chart processing. Auto-ran by Yii
     */
    public function run () {
        parent::run();

        $this->period = 'month';

        switch ($this->view) {
            case 'data':
                $this->results = array(
                    'chartId' => $this->chartId,
                    'data' => $this->render('bom/data', null, true),
                );
                break;

            case 'group':
                $this->results = $this->render('bom/' . $this->view, $this->data, !$this->init);
                break;

            default:
                $this->runReport();
        }

    }

    /*
     * add a single column to $this->chartData
     * @param DbBom $item
     */
    protected function addColumn ($data) {
        //set the chart data
        $col = array(
            //google charts
            'id' => $data->id,
            'type' => 'number',
            'label' => $data->asset_name,

            //additional
            'color' => $this->getColor(),
            'weighting' => $data->weighting,
            'name' => $data->asset_name,
        );

        $this->chartData['cols'][] = $col;
    }

    /*
     * add rows to chart data
     * @param array $rows - ActiveRecordTradeData->listData()
     */
    protected function addRows ($rows) {
        if (!empty($rows)) {
            foreach ($rows as $attr => $val) {

                if (strpos($attr, 'month_') !== false) {
                    $date = ucfirst(str_replace('_', ' ', $attr));
                    $date = DbBom::monthTitle($attr);
                    if (!empty($date)) {
                        if (empty($this->chartData['rows'][$date])) {
                            $this->chartData['rows'][$date] = array();
                        }
                        $this->chartData['rows'][$date][] = $val;
                    }
                }
            }
        }
    }

    /*
     * render a html table of db results
     * @return string
     */
    public function addTable () {
        $html = '';
        if (!empty($this->chartData['rows'])) {
            $html = $this->render('bom/table', array(
                'id' => $this->chartId,
                'data' => $this->chartData,
            ), true);
        }

        return $html;
    }

    /*
     * render chart title html
     * @return string
     */
    public function addTitle () {
        $title = 'Supply Chain and Bill of Material Forecast';

        $html = $this->render('bom/title', array(
            'title' => $title,
        ), true);

        return $html;

    }

    /*
     * return a db row form the requested table
     * @param DbBom $item
     * @param string $type
     * @return ActiveRecord
     */
    protected function fetchModel () {
        $criteria = new CDbCriteria();
        $model = new DbBom();
        $model = $model->findAll($criteria);

        return $model;
    }

    protected function getColor () {
        $grade = 600;
        $color = null;

        //$i = ceil((count($this->chartData['cols'])) / 3) - 1;
        $i = ceil(count($this->chartData['cols'])) - 1;

        $colors = $this->listColors();

        $j = 0;
        foreach ($colors as $key => $data) {
            if ($j == $i) {
                $color = $data[$grade];
                break;
            }
            $j++;
        }

        return $color;
    }

    /*
     * run a default standard report
     */
    protected function runReport () {
        $data = array();

        $models = $this->fetchModel();
        if (!empty($models)) {
            if (is_array($models)) {
                foreach ($models as $model) {
                    $this->addColumn($model);
                    $results = $model->listData();
                    $this->addRows($results);
                }
            } else {
                $this->addColumn($models);
                $results = $models->listData();
                $this->addRows($results);
            }
        }

        $this->formatRows();
        $data = $this->getDefaultResults();
        if($this->showTable) {
            $data['table'] = $this->addTable();
        }
        $data['title'] = $this->addTitle();

        $this->results = $data;
    }

    /*
     * convert class vars to models
     * @return array
     */
    protected function setModels () {
        $this->item = new DbBom();

    }

}