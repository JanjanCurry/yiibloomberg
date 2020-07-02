<?php class MarketWidget extends ChartWidget {

    //models
    public $item;

    public $mom = array();
    public $qoq = array();
    public $yoy = array();
    public $percentage = array();
    public $correlations = array();

    //form vars
    public $market;
    public $marketCompare;
    public $variant;

    //processing vars
    protected $fieldList = array(
        'period',
        'item',
        'compare',
        'chartId',
        'chartType',
        'variant',
        'startTime',
        'endTime',
        'view',
    );

    /*
     * Init chart processing. Auto-ran by Yii
     */
    public function run () {
        $this->session = 'time';
        parent::run();

        switch($this->market){
            case 'com':
                $this->market = 'commodity';
                break;
            case 'cur':
                $this->market = 'currency';
                break;
            case 'equ':
                $this->market = 'equity';
                break;
        }
        $this->type = $this->market;
        $this->createUrl();

        $this->period = 'month';

        if ($this->init) {
            if ($this->editable) {
                Yii::app()->controller->registerFile('js', 'chart/market');
            }

            if (!empty($this->item)) {
                $this->showEmpty = true;
            }
        }

        if (!$this->validatePremium()) {
            if ($this->init && $this->view == 'base') {
                $this->requireEditing = true;
                $this->showEmpty = true;
            } else {
                return false;
            }
        }

        if(!empty($this->item)){
            if(is_array($this->item)){
                if(!empty($this->item[0])) {
                    $this->name = $this->item[0]->name;
                }
            }else{
                $this->name = $this->item->name;
            }
        }

        switch ($this->view) {

            case 'data':
                $this->results = array(
                    'chartId' => $this->chartId,
                    'data' => $this->render('market/data', null, true),
                );
                break;

            case 'csv':
                $this->runReport();
                $this->addCsv();
                break;

            case 'pdf':
                $this->runReport();
                $this->pdf = $this->render('chart/pdf', $this->results, true);
                break;

            case 'sparkline':
                $this->compare = null;
                $this->report = 'sparkline';
                $this->results = $this->render('market/group-sparkline', null, true);
                /*$this->runSpark();
                if(!empty($this->results)) {
                    $this->results = $this->render('market/sparkline', [
                        'label' => $this->item[0]->name,
                        'group' => $this->getListLabel('listMarketPlural', $this->market),
                        'url' => $this->url,
                        'values' => $this->results,
                    ], true);
                }*/
                break;

            case 'dash-change':
                $this->results = $this->render('market/dash-yoy', $this->data, true);
                break;

            case 'dash-outlook':
                $this->results = $this->render('market/dash-outlook', $this->data, true);
                break;

            case 'favorites':
            case 'group':
                $this->results = $this->render('market/' . $this->view, $this->data, !$this->init);
                break;

            default:
                $this->runReport();
        }

    }

    /*
     * add a single column to $this->chartData
     * @param DbCommodities|DbCurrencies|DbEquities $item
     */
    protected function addColumn ($item, $data = null) {
        $col = array();
        if (!empty($item)) {
            if (is_object($item)) {
                $col = array(
                    //google charts
                    'id' => $item->code,
                    'type' => 'number',
                    'label' => $item->name . ' - ' . $data->getListLabel('listType', $data->Type),

                    //additional
                    'market' => $item->name,
                    'color' => $this->getColor(null, $data->Type),
                    'assetType' => $data->Type,
                    'confidence' => $data->confidence,
                    //'correlations' => $data->Correlations,
                    //'correlations-top' => (!empty($data->Correlations) ? $data->getTopCorrelations() : null),
                    //'rsq' => $data->RSQ,
                    'source' => (!empty($data->source) ? $data->source->description : ''),
                    'variant' => $data->Variant,
                    'name' => $item->name,
                );

                if(!empty($data->Correlations)){
                    $this->correlations[] = [];
                    $this->correlations[] = [];
                    $this->correlations[] = [
                        'rsq' => $data->RSQ,
                        'correlations' => $data->Correlations,
                        'correlations-top' => $data->getTopCorrelations(),
                    ];
                }

                $this->chartData['cols'][] = $col;

                //toggle dashed line for forecasts
                $this->chartData['cols'][] = array(
                    'id' => $col['id'] . '-forecast',
                    'type' => 'boolean',
                    'role' => 'certainty',
                    'color' => $col['color'],
                );
            }else{
                $col = array(
                    //google charts
                    'id' => $item['id'],
                    'type' => 'number',
                    'label' => $item['label'],

                    //additional
                    'market' => $this->market,
                    'color' => $item['color'],
                );

                $this->chartData['cols'][] = $col;
            }
        }
    }

    /*
     * Generate csv compatible array of the results
     * @return boolean
     */
    protected function addCsv () {
        $csvData = $ignore = array();
        if (!empty($this->results['chartData']['cols'])) {
            foreach ($this->results['chartData']['cols'] as $key => $colData) {
                if (empty($colData['role'])) {
                    $indicator = '';
                    if ($colData['label'] == 'Period') {
                        $csvData[$key] = array(
                            $this->getListLabel('listType', $this->market),
                            'Type',
                            'Units',
                            'Correlations',
                            'R-Squared',
                            'Source',
                        );
                    } else {
                        $label = $colData['label'];

                        $csvData[$key] = array(
                            $label,
                            $colData['assetType'],
                            $this->getVariant($colData['variant']),
                            $colData['correlations'],
                            $colData['rsq'],
                            $colData['source']
                        );
                    }
                } else {
                    $ignore[] = $key;
                }
            }
        }

        if (!empty($csvData)) {
            foreach ($this->results['chartData']['rows'] as $temp) {
                $row = array();
                foreach ($temp as $rowData) {
                    foreach ($rowData as $key => $colData) {
                        if (!in_array($key, $ignore)) {
                            if (!empty($colData['v']) && is_numeric($colData['v'])) {
                                $csvData[$key][] = number_format($colData['v'], 4);
                            } else {
                                $csvData[$key][] = $colData['v'];
                            }
                        }
                    }
                }
            }
        }
        $csvData[] = array('');
        $csvData[] = array('Data from ' . $this->getForecastDate() . ' onward is forecasted');
        $this->csvData = $csvData;
    }

    /*
     * add rows to chart data
     * @param array $rows - ActiveRecordMarketData->listData()
     */
    protected function addRows ($rows) {
        if (!empty($rows)) {
            $forecast = true;
            $forecastDate = $this->getForecastDate();
            foreach ($rows as $attr => $val) {
                if ($this->period != 'month') {
                    $attr = 'M' . $attr;
                }

                $parts = DbAllMrkComResults::model()->listAttrParts($attr);
                if (!empty($parts)) {
                    $date = null;
                    switch ($this->period) {
                        case 'month':
                            if (!empty($parts['month']) && is_numeric($parts['month']) && !empty($parts['year']) && is_numeric($parts['year'])) {
                                $date = date('M Y', strtotime($parts['year'] . '-' . $parts['month'] . '-1'));
                            }
                            break;

                        case 'annual':
                            if (!empty($parts['year']) && is_numeric($parts['year'])) {
                                $date = $parts['year'];
                            }
                            break;

                        case 'quarter':
                            if (!empty($parts['quarter']) && in_array($parts['quarter'], array('Q1', 'Q2', 'Q3', 'Q4'))) {
                                $date = $parts['quarter'] . ' ' . $parts['year'];
                            } else if (!empty($parts['month']) && in_array($parts['month'], array('Q1', 'Q2', 'Q3', 'Q4'))) {
                                $date = $parts['month'] . ' ' . $parts['year'];
                            }
                            break;
                    }

                    if (!empty($date)) {
                        if (empty($this->chartData['rows'][$date])) {
                            $this->chartData['rows'][$date] = array();
                        }
                        $this->chartData['rows'][$date][] = $val;

                        $this->chartData['rows'][$date][] = $forecast;
                        if ($date == $forecastDate) {
                            $forecast = false;
                        }
                    }
                }
            }
        }
    }

    /*
     * render a text sources of db results
     * @return string
     */
    public function addSource () {
        $return = [];
        if (!empty($this->chartData['cols'])) {
            foreach ($this->chartData['cols'] as $col) {
                if (!empty($col['source']) && !in_array($col['source'], $return)) {
                    $return[] = $col['source'];
                }
            }
        }

        return $return;
    }

    /*
     * render a html table of db results
     * @return string
     */
    public function addTable () {
        $html = '';
        if (!empty($this->chartData['rows'])) {
            $html = $this->render('market/table', array(
                'id' => $this->chartId,
                'data' => $this->chartData,
                'forecastDate' => $this->getForecastDate(),
            ), true);
        }

        return $html;
    }

    /*
     * render chart title html
     * @return string
     */
    public function addTitle () {
        $html = '';


        $items = array();
        if (!empty($this->item)) {
            if (count($this->item) > 1) {
                $this->showCompare = false;
            }
            foreach ($this->item as $item) {
                $items[] = $item->name;
            }
        }

        if (!empty($this->compare)) {
            if (count($this->compare) > 1) {
                $this->showCompare = false;
            }
            foreach ($this->compare as $compare) {
                $items[] = $compare->name;
            }
        }

        $title = implode('/', $items);

        if ($this->view == 'pdf') {
            $html = $title;
        } else if (!empty($title)) {
            $html = $this->render('market/title', array(
                'title' => $title,
            ), true);
        }

        return $html;

    }

    protected function calcChange ($item, $model) {
        //only run for Mid data
        if ($model->Type != 'Mid') {
            return null;
        }

        if ($this->period == 'month') {
            $rows = $model->listData(array(
                'period' => 'month',
                'start' => date('Ym', strtotime('-1 month', $this->startTime)),
                'end' => date('Ym', $this->endTime),
            ));
            $data = array();
            if (!empty($rows)) {
                $prev = null;
                foreach ($rows as $attr => $val) {
                    $parts = ActiveRecordMarketData::model()->listAttrParts($attr);
                    $date = date('M Y', strtotime($parts['year'] . '-' . $parts['month'] . '-1'));
                    if (!empty($prev) && $prev != 0) {
                        $data[$date] = (($val - $prev) / $prev) * 100;
                    } else if (!is_null($prev)) {
                        $data[$date] = null;
                    }

                    $prev = $val;
                }
                //var_dump($data);exit;

                //add empty rows so that the calculation for  Mid appears after the Low row
                $this->mom[] = array();
                $this->mom[] = array();
                $this->mom[] = $data;
            }
        }

        $criteria = new CDbCriteria();
        $criteria->order = 'months ASC';
        $criteria->compare('Asset_ID', $item->code);

        switch ($item->market) {
            case 'com':
            case 'commodity':
                $criteria->compare('Type', 'com');
                break;
            case 'cur':
            case 'currency':
                $criteria->compare('Type', 'cur');
                break;
            case 'eqi':
            case 'equity':
                $criteria->compare('Type', 'equ');
                break;
        }

        $models = DbMarketPercent::model()->findAll($criteria);
        if(!empty($models)){
            $datas = array();
            foreach($models as $percentModel){
                $rows = $percentModel->listData(array(
                    'period' => 'month',
                    'start' => date('Ym', $this->startTime),
                    'end' => date('Ym', $this->endTime),
                ));

                if (!empty($rows)) {
                    $data = array();
                    $prev = null;
                    foreach ($rows as $attr => $val) {
                        $parts = ActiveRecordMarketData::model()->listAttrParts($attr);
                        $date = date('M Y', strtotime($parts['year'] . '-' . $parts['month'] . '-1'));;
                        $data[$date] = (($model->$attr - $val) / $val) * 100;
                    }
                    $datas[$percentModel->months] = $data;
                }
            }
            $this->percentage[] = array();
            $this->percentage[] = array();
            $this->percentage[] = $datas;
        }
    }

    /*
     * return a db row form the requested table
     * @param DbCommodities|DbCurrencies|DbEquities $item
     * @param string $type
     * @return ActiveRecord
     */
    protected function fetchModel ($item, $type = null) {
        $criteria = new CDbCriteria();
        $criteria->order = 'FIELD(t.type, "High", "Mid", "Low")';
        $criteria->compare('Asset_ID', $item->code);
        $criteria->compare('type', $type);

        switch ($item->market) {
            case 'com':
            case 'commodity':
                $model = new DbAllMrkComResults();
                break;
            case 'cur':
            case 'currency':
                $model = new DbAllMrkCurResults();
                break;
            case 'eqi':
            case 'equity':
                $model = new DbAllMrkEqiResults();
                break;
        }

        if (!empty($type)) {
            $model = $model->find($criteria);
        } else {
            $model = $model->findAll($criteria);
        }

        if (!empty($model)) {
            $this->variant = $model[0]->Variant;
        }

        return $model;
    }

    protected function getColor ($i, $type = null) {
        $grade = null;
        switch ($type) {
            case 'High':
                $grade = 300;
                break;
            case 'Low':
                $grade = 900;
                break;
            case 'Mid':
            default:
                $grade = 600;
                break;
        }

        //$i = ceil((count($this->chartData['cols'])) / 3) - 1;
        $i = ceil((count($this->chartData['cols'])) / 6) - 1;


        $colors = $this->listColors(array(
            'blue',
            'red',
        ));

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

    protected function getDefaultResults () {
        $list = parent::getDefaultResults();
        $list['axis']['y']['label'] = 'Value in ' . $this->getVariant($this->variant);

        //force dual y-axis when comparing
        if (!empty($this->compare)) {
            $defaultYAxis = $list['axis']['y'];
            $yAxis = $names = $variants = array();
            foreach ($this->chartData['cols'] as $col) {
                if ((!empty($col['variant']) && !in_array($col['variant'], $variants)) || (!empty($col['name']) && !in_array($col['name'], $names))) {
                    if (!in_array($col['variant'], $variants)) {
                        $variants[] = $col['variant'];
                    }
                    if (!in_array($col['name'], $names)) {
                        $names[] = $col['name'];
                    }
                    $yAxisTemp = $defaultYAxis;
                    $yAxisTemp['label'] = $col['name'] . ' - Value in ' . $this->getVariant($col['variant']);
                    $yAxisTemp['label'] = 'Value in ' . $this->getVariant($col['variant']);
                    $yAxis[] = $yAxisTemp;
                }
            }

            //daft hack to always force double axis when compare is used
            //if (count($variants) == 1) {
            if (!empty($this->chartData['cols'])) {
                foreach ($this->chartData['cols'] as $i => $col) {
                    if ($i >= 7) {
                        $list['chartData']['cols'][$i]['targetAxisIndex'] = 1;
                    }
                }
            }

            $list['axis']['y'] = $yAxis;
        }

        return $list;
    }

    public function getName(){
        if(!empty($this->name)){
            $name = $this->name;
        } else if(!empty($this->item)){
            if(!empty($this->item[0])){
                $this->name = $this->item[0]->name;
            }
        }

        return $name;
    }

    public function listMarket () {
        return ActiveRecordMarketData::model()->listMarket();
    }

    public function listMarketPlural () {
        return ActiveRecordMarketData::model()->listMarketPlural();
    }

    public function listType () {
        return ActiveRecordMarketData::model()->listType();
    }

    public function listVariant () {
        return ActiveRecordMarketData::model()->listVariant();
    }

    public function getVariant ($variant) {
        $temp = $this->getListLabel('listVariant', $variant);
        if (!empty($temp)) {
            $variant = $temp;
        }

        return $variant;
    }

    /*
     * run a default standard report
     */
    protected function runReport () {
        switch($this->report){
            case 'dash-change':
                $return = $this->dashChange('value');
                break;

            case 'dash-change-percent':
                $return = $this->dashChange('percent');
                break;

            case 'dash-outlook':
                $return = $this->dashOutlook();
                break;

            case 'sparkline':
                $return = $this->runSpark();
                break;

            case 'default':
            default:
                $return = $this->reportDefault();
                break;
        }

        return $return;
    }

    protected function reportDefault () {
        $data = array();
        if (!empty($this->item)) {
            $options = array(
                'period' => $this->period,
                'start' => date('Ym', $this->startTime),
                'end' => date('Ym', $this->endTime),
            );

            $title = array();
            foreach ($this->item as $item) {
                $title[] = $item->name;
                $models = $this->fetchModel($item);
                if (!empty($models)) {
                    if (is_array($models)) {
                        foreach ($models as $model) {
                            $this->addColumn($item, $model);
                            $results = $model->listData($options);
                            $this->addRows($results);
                            $this->calcChange($item, $model);
                        }
                    } else {
                        $this->addColumn($item, $models);
                        $results = $models->listData($options);
                        $this->addRows($results);
                        $this->calcChange($item, $models);
                    }
                }
            }

            if (!empty($this->compare)) {
                foreach ($this->compare as $item) {
                    $title[] = $item->name;
                    $models = $this->fetchModel($item);
                    if (!empty($models)) {
                        if (is_array($models)) {
                            foreach ($models as $model) {
                                $this->addColumn($item, $model);
                                $results = $model->listData($options);
                                $this->addRows($results);
                                $this->calcChange($item, $model);
                            }
                        } else {
                            $this->addColumn($item, $models);
                            $results = $models->listData($options);
                            $this->addRows($results);
                            $this->calcChange($item, $models);
                        }
                    }
                }
            }

            $title = implode(',', $title);

            //format results
            $this->formatRows();
            $data = $this->getDefaultResults();
            if ($this->showTable) {
                $data['table'] = $this->addTable();
            }
            $data['title'] = $this->addTitle();
            $data['source'] = $this->addSource();

        }
        $this->results = $data;
    }

    protected function dashOutlook(){
        $data = array();
        if (!empty($this->item)) {
            $options = array(
                'period' => $this->period,
            );

            $startTime = strtotime('-1 month', $this->startTime);

            $dates = [
                'current' => [
                    'start' => date('Ym', $startTime),
                    'end' => date('Ym', $this->endTime),
                ],
            ];

            $title = array();
            foreach ($this->item as $i => $item) {
                $title[] = $item->name;
                $models = $this->fetchModel($item);
                if (!empty($models)) {
                    if (is_array($models)) {
                        foreach ($models as $model) {
                            if($model->Type == 'Mid') {
                                $this->addColumn([
                                    'id' => 'current',
                                    'type' => 'number',
                                    'label' => $item->name,
                                    'color' => $this->getRandColor($i*2)
                                ]);

                                $options['start'] = $dates['current']['start'];
                                $options['end'] = $dates['current']['end'];
                                $results = $model->listData($options);
                                $prev = null;
                                foreach ($results as $attr => $val) {
                                    $parts = DbAllMrkComResults::model()->listAttrParts($attr);
                                    if (!empty($parts)) {
                                        $date = null;
                                        if (!empty($parts['month']) && is_numeric($parts['month']) && !empty($parts['year']) && is_numeric($parts['year'])) {
                                            $date = date('M', strtotime($parts['year'] . '-' . $parts['month'] . '-1'));
                                        }

                                        if (!empty($date)) {
                                            if (!empty($prev) && $prev != 0) {
                                                if (empty($this->chartData['rows'][$date])) {
                                                    $this->chartData['rows'][$date] = array();
                                                }
                                                $this->chartData['rows'][$date][] = (($val - $prev) / $prev) * 100;
                                            }
                                            $prev = $val;
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }

            $title = implode(',', $title);

            //format results
            $this->formatRows();
            $data = $this->getDefaultResults();
            $data['title'] = $html = $this->render('market/dash-outlook-title', array(
                'title' => $this->market.' % Change Outlook',
            ), true);

        }
        $this->results = $data;
    }

        protected function dashChange($type = 'value'){
            $data = array();
            if (!empty($this->item)) {
                $options = array(
                    'period' => $this->period,
                );

                $startTime = $this->startTime;
                if($type == 'percent'){
                    $startTime = strtotime('-1 month', $startTime);
                }

                $dates = [
                    'current' => [
                        'start' => date('Ym', $startTime),
                        'end' => date('Ym', $this->endTime),
                    ],
                    'prev' => [
                        'start' => date('Ym', strtotime('-1 year', $startTime)),
                        'end' => date('Ym', strtotime('-1 year', $this->endTime)),
                    ],
                ];

                $title = array();
                foreach ($this->item as $item) {
                    $title[] = $item->name;
                    $models = $this->fetchModel($item);
                    if (!empty($models)) {
                        if (is_array($models)) {
                            foreach ($models as $model) {
                                if($model->Type == 'Mid') {
                                    $this->addColumn([
                                        'id' => 'current',
                                        'type' => 'number',
                                        'label' => $item->name . ' - Forecast',
                                        'color' => $this->getRandColor(0)
                                    ]);
                                    $this->addColumn([
                                        'id' => 'yoy',
                                        'type' => 'number',
                                        'label' => $item->name . ' - Last Year',
                                        'color' => $this->getRandColor(6)
                                    ]);

                                    $options['start'] = $dates['current']['start'];
                                    $options['end'] = $dates['current']['end'];
                                    $results = $model->listData($options);
                                    $prev = null;
                                    foreach ($results as $attr => $val) {
                                        $parts = DbAllMrkComResults::model()->listAttrParts($attr);
                                        if (!empty($parts)) {
                                            $date = null;
                                            if (!empty($parts['month']) && is_numeric($parts['month']) && !empty($parts['year']) && is_numeric($parts['year'])) {
                                                $date = date('M', strtotime($parts['year'] . '-' . $parts['month'] . '-1'));
                                            }

                                            if (!empty($date)) {
                                                if($type == 'percent') {
                                                    if (!empty($prev) && $prev != 0) {
                                                        if (empty($this->chartData['rows'][$date])) {
                                                            $this->chartData['rows'][$date] = array();
                                                        }
                                                        $this->chartData['rows'][$date][] = (($val - $prev) / $prev) * 100;
                                                    }
                                                    $prev = $val;
                                                }else {
                                                    if (empty($this->chartData['rows'][$date])) {
                                                        $this->chartData['rows'][$date] = array();
                                                    }
                                                    $this->chartData['rows'][$date][] = $val;
                                                }
                                            }
                                        }
                                    }


                                    $options['start'] = $dates['prev']['start'];
                                    $options['end'] = $dates['prev']['end'];
                                    $results = $model->listData($options);
                                    $prev = null;
                                    foreach ($results as $attr => $val) {
                                        $parts = DbAllMrkComResults::model()->listAttrParts($attr);
                                        if (!empty($parts)) {
                                            $date = null;
                                            if (!empty($parts['month']) && is_numeric($parts['month']) && !empty($parts['year']) && is_numeric($parts['year'])) {
                                                $date = date('M', strtotime($parts['year'] . '-' . $parts['month'] . '-1'));
                                            }

                                            if (!empty($date)) {
                                                if($type == 'percent') {
                                                    if (!empty($prev) && $prev != 0) {
                                                        if (empty($this->chartData['rows'][$date])) {
                                                            $this->chartData['rows'][$date] = array();
                                                        }
                                                        $this->chartData['rows'][$date][] = (($val - $prev) / $prev) * 100;
                                                    }
                                                    $prev = $val;
                                                }else {
                                                    if (empty($this->chartData['rows'][$date])) {
                                                        $this->chartData['rows'][$date] = array();
                                                    }
                                                    $this->chartData['rows'][$date][] = $val;
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }

                $title = implode(',', $title);

                //format results
                $this->formatRows();
                $data = $this->getDefaultResults();
                $data['title'] = $html = $this->render('market/dash-yoy-title', array(
                    'title' => $item->name,
                ), true);

            }
            $this->results = $data;
        }

    protected function runSpark(){
        $data = array();
        if (!empty($this->item)) {
            $item = $this->item[0];
            $models = $this->fetchModel($item);
            if (!empty($models)) {
                if (is_array($models)) {
                    foreach ($models as $temp) {
                        if($temp->Type == 'Mid') {
                            $model = $temp;
                        }
                    }
                } else {
                    $model = $models;
                }

                if(!empty($model)){
                    $rows = $model->listData(array(
                        'period' => 'month',
                        'start' => date('Ym', strtotime('-1 month', $this->startTime)),
                        'end' => date('Ym', $this->endTime),
                    ));
                    if (!empty($rows)) {
                        $prev = null;
                        foreach ($rows as $attr => $val) {
                            $parts = ActiveRecordMarketData::model()->listAttrParts($attr);
                            $date = date('M Y', strtotime($parts['year'] . '-' . $parts['month'] . '-1'));
                            if (!empty($prev) && $prev != 0) {
                                $data[$date] = (($val - $prev) / $prev) * 100;
                            } else if (!is_null($prev)) {
                                $data[$date] = null;
                            }

                            $prev = $val;
                        }
                    }
                }
            }
        }

        $this->results = [
            'chartId' => $this->chartId,
            'html' => $this->render('market/sparkline', [
                'label' => $this->item[0]->name,
                'group' => $this->getListLabel('listMarketPlural', $this->market),
                'url' => $this->url,
                'values' => $data,
            ], true)
        ];
    }

    public function getForecastDate () {
        $date = time();
        $model = DbVar::model()->findByAttributes(['name' => 'dbDates']);
        if (!empty($model) && !empty($model->data) && !empty($model->data['forecastDate'])) {
            $date = strtotime($model->data['forecastDate']);
        }

        switch ($this->period) {
            default:
            case 'month':
                $forecastDate = date('M Y', $date);
                break;

            case 'annual':
                $forecastDate = date('Y', $date);
                break;

            case 'quarter':
                $forecastDate = 'Q' . (ceil(date('m', $date)) / 3) . ' ' . date('Y', $date);
                break;
        }

        return $forecastDate;
    }


    protected function createUrl () {
        //set public url
        $params = array();
        $page = $this->market . '/index';
        if (!empty($this->item)) {
            foreach ($this->item as $item) {
                if (is_object($item)) {
                    $params['id'] = $item->code;
                } else {
                    $params['id'] = $item;
                }
            }
        }

        $attrs = array(
            'endTime',
            'startTime',
        );
        foreach ($attrs as $key) {
            if (!empty($this->$key)) {
                if (is_array($this->$key)) {
                    $params[$key] = implode(',', $this->$key);
                } else {
                    $params[$key] = $this->$key;
                }
            }
        }

        if (!empty($this->compare)) {
            $params['compare'] = [];
            foreach ($this->compare as $compare) {
                if (is_object($compare)) {
                    $params['compare'][] = $compare->code;
                } else {
                    $params['compare'][] = $compare;
                }
            }
            $params['compare'] = implode(',', $params['compare']);
        }

        $this->url = Yii::app()->createAbsoluteUrl($page, $params);
    }

    /*
     * convert class vars to models
     * @return array
     */
    protected function setModels () {
        if (!empty($this->item)) {
            if (!is_array($this->item)) {
                $this->item = explode(',', $this->item);
            }
            if (!empty($this->item)) {
                $items = $this->item;
                $exists = $this->item = array();
                foreach ($items as $item) {
                    if (!in_array($item, $exists)) {
                        if (!is_object($item)) {
                            $item = $this->findModelByRef($item);
                        }
                        if (!empty($item)) {
                            $this->marketCompare = $this->market = $item->market;
                            $exists[] = $item->code;
                            $this->item[] = $item;
                        }
                    }
                }
            }
        }

        if (!empty($this->compare)) {
            if (!is_array($this->compare)) {
                $this->compare = explode(',', $this->compare);
            }
            if (!empty($this->compare)) {
                $items = $this->compare;
                $exists = $this->compare = array();
                foreach ($items as $item) {
                    if (!in_array($item, $exists)) {
                        if (!is_object($item)) {
                            $item = $this->findModelByRef($item);
                        }
                        if (!empty($item)) {
                            $this->marketCompare = $item->market;
                            $exists[] = $item->code;
                            $this->compare[] = $item;
                        }
                    }
                }
            }
        }
    }

    public function findModelByRef ($item) {
        $companyId = [0];
        $user = Yii::app()->controller->user;
        if(!empty($user->companyId)){
            $companyId[] = $user->companyId;
        }
        $criteria = new CDbCriteria();
        $criteria->compare('companyId', $companyId);
        $criteria->compare('code', $item);

        $model = DbCommodities::model()->find($criteria);
        if (empty($model)) {
            $model = DbCurrencies::model()->find($criteria);
            if (empty($model)) {
                $model = DbEquities::model()->find($criteria);
            }
        }

        return (!empty($model) ? $model : false);
    }

    protected function validatePremium () {
        $valid = true;

        if (!$this->init && empty($this->item)) {
            $valid = false;
            $this->error[] = 'Please select an asset';
        }

        if ($valid && !empty($this->item)) {
            if (!empty($this->item)) {
                $temp = array();
                foreach ($this->item as $item) {
                    if(empty($this->ignorePermissions)) {
                        if (!Yii::app()->user->checkToolAccess(DbUserService::convertTool($item->market), 'service-pro')) {
                            if (Yii::app()->user->checkToolAccess(DbUserService::convertTool($item->market), 'service-ess')) {
                                if ($item->access > 1) {
                                    $valid = false;
                                    $this->error[] = $item->name . ' is only available to Pro accounts';
                                }
                            } else if ($item->access > 0) {
                                $valid = false;
                                $this->error[] = $item->name . ' is only available to Pro accounts';
                            }
                        }
                    }

                    $model = $this->fetchModel($item);
                    if (!empty($model)) {
                        $temp[] = $item;
                    } else {
                        $this->error[] = 'Sorry, there is not enough data availablea for ' . $item->name;
                    }
                }
                $this->item = $temp;
            }

            if (empty($this->item)) {
                $valid = false;
            }
        }

        if ($valid && !empty($this->compare)) {
            if (!empty($this->compare)) {
                $temp = array();
                foreach ($this->compare as $item) {
                    if (!in_array($item, $this->item)) {
                        if(!$this->ignorePermissions) {
                            if (!Yii::app()->user->checkToolAccess(DbUserService::convertTool($item->market), 'service-pro')) {
                                if (Yii::app()->user->checkToolAccess(DbUserService::convertTool($item->market), 'service-ess')) {
                                    if ($item->access > 1) {
                                        $valid = false;
                                        $this->error[] = $item->name . ' is only available to Pro accounts';
                                    }
                                } else if ($item->access > 0) {
                                    $valid = false;
                                    $this->error[] = $item->name . ' is only available to Pro accounts';
                                }
                            }
                        }

                        $model = $this->fetchModel($item);
                        if (!empty($model)) {
                            $temp[] = $item;
                        } else {
                            $this->error[] = 'Sorry, there is not enough data available for ' . $item->name;
                        }
                    }
                }
                $this->compare = $temp;
            }
        }

        return $valid;
    }

    public function isFavorite () {
        $valid = false;
        $data = array();
        $attrs = DbUserFavorites::model()->listDataAttrs($this->market);

        if (!empty($attrs)) {
            foreach ($attrs as $attr) {
                if (isset($this->$attr)) {
                    if (is_object($this->$attr)) {
                        $data[$attr] = $this->$attr->code;
                    } else if (is_array($this->$attr)) {
                        $data[$attr] = array();
                        foreach ($this->$attr as $obj) {
                            if (is_object($obj)) {
                                $data[$attr][] = $obj->code;
                            } else {
                                $data[$attr][] = $obj;
                            }
                        }
                        $data[$attr] = implode(',', $data[$attr]);
                    } else {
                        $data[$attr] = $this->$attr;
                    }
                }
            }
        }

        if (!empty($data)) {
            $valid = DbUserFavorites::model()->isAssigned($this->user->id, $this->market, $data);
        }

        return $valid;
    }

    public function setCommodity ($model) {
        $this->item = $model;
    }

    public function setCurrency ($model) {
        $this->item = $model;
    }

    public function setEquity ($model) {
        $this->item = $model;
    }
}