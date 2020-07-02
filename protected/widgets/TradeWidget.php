<?php class TradeWidget extends ChartWidget {

    public $watchlistItem;

    public $mom = array();
    public $qoq = array();
    public $yoy = array();

    public $reporter;
    public $partner;
    public $sector;
    public $compare;
    public $indicator;

    public $reportType = 'trade';
    public $reportValue = 'TT';
    public $reportIndicator;

    protected $type = 'trade';
    protected $fieldList = array(
        'reporter',
        'period',
        'partner',
        'sector',
        'compare',
        'indicator',
        'chartId',
        'chartType',
        'reportType',
        'reportValue',
        'reportIndicator',
        'startTime',
        'endTime',
        'view',
        'col',
        'editable',
        'showChart',
        'showLegend',
        'showModal',
        'showTable',
        'url',
    );


    public function run () {
        parent::run();

        if ($this->init) {
            if ($this->editable) {
                Yii::app()->controller->registerFile('js', 'chart/trade');
            }

            if (!empty($this->reporter) && !empty($this->indicator)) {
                $this->showEmpty = true;
            }
        }

        if (!$this->validatePremium()) {
            if ($this->init && $this->view == 'group') {
                $this->requireEditing = true;
                $this->showEmpty = true;
            } else {
                return false;
            }
        }


        switch ($this->view) {

            case 'data':
                $this->results = array(
                    'chartId' => $this->chartId,
                    'data' => $this->render('trade/data', null, true),
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
                if(!in_array($this->reportIndicator, ['flow', 'top10'])) {
                    $this->compare = null;
                    $this->runReport();
                    if (!empty($this->mom)) {
                        $values = [];
                        $i = 0;
                        foreach ($this->mom[0] as $key => $val) {
                            $values[$key] = $val;
                            if($i == 1){
                                break;
                            }
                            $i++;
                        }
                        $this->results = $this->render('chart/sparkline', [
                            'label' => $this->name,
                            'values' => $values,
                        ], true);
                    }
                }
                break;

            case 'dashboard-group':
                if($this->report != 'yoy-change' || !in_array($this->reportIndicator, ['flow', 'top10'])) {
                    $this->addTitle();
                    $this->results = $this->render('trade/group', $this->data, true);
                }
                break;

            case 'landing':
            case 'favorites':
            case 'group':
                $this->results = $this->render('trade/' . $this->view, $this->data, !$this->init);
                break;

            default:
                $this->runReport();
        }
    }

    /*
     * add a single column to $this->chartData
     * @param mixed $data - DbReporter / array
     * @param string $type
     */
    protected function addColumn ($data, $type = 'number', $reporter = null) {
        $col = array();
        if (!empty($data)) {
            if (is_object($data)) {
                if (get_class($data) == 'DbReporters') {
                    $col = array(
                        'id' => $data->code,
                        'label' => $data->country,
                        'color' => $data->color,
                        'type' => $type,
                    );
                }

                if (get_class($data) == 'DbPartners') {
                    $col = array(
                        'id' => $data->code,
                        //'label' => (!empty($reporter) ? $data->country . ' (' . $reporter->country . ')' : $data->country),
                        'label' => $data->country,
                        'color' => $data->color,
                        'type' => $type,
                    );
                }

                if (get_class($data) == 'DbSectors') {
                    $col = array(
                        'id' => $data->code,
                        //'label' => (!empty($reporter) ? $data->name . ' (' . $reporter->country . ')' : $data->name),
                        'label' => (!empty($this->compare) && !empty($reporter) ? $reporter->name : $data->name),
                        'color' => (!empty($reporter) && !empty($this->compare) ? $reporter->color : $this->getRandColor(count($this->chartData['cols']) - 1)),
                        'type' => $type,
                    );
                }
            } else if (is_array($data)) {
                $col = array(
                    'id' => (!empty($data['id']) ? $data['id'] : ''),
                    'label' => (!empty($data['label']) ? $data['label'] : ''),
                    'color' => (!empty($data['color']) ? $data['color'] : ''),
                    'type' => $type,
                );
            }
        }
        if (!empty($col)) {
            if (!empty($reporter)) {
                $col['table'] = array(
                    'label' => $reporter->country,
                    'color' => $reporter->color,
                );
            }
            $this->chartData['cols'][] = $col;
        }
    }

    protected function addCsv () {
        $csvData = array();
        if (!empty($this->results['chartData']['cols'])) {
            foreach ($this->results['chartData']['cols'] as $key => $colData) {
                $indicator = '';
                if ($colData['label'] == 'Period') {
                    $csvData[$key] = array(
                        'Country',
                        'Indicator',
                        'Partner / Sector',
                    );
                } else {
                    $reporter = $colData['label'];
                    $partner = 'None';
                    if (!empty($this->reportIndicator)) {
                        $partner = $this->reporter->country;
                    } else if (!empty($this->partner) || !empty($this->sector)) {
                        $reporter = $this->reporter->country;
                        $partner = $colData['label'];
                    }

                    $csvData[$key] = array(
                        $reporter,
                        ActiveRecordTradeData::model()->getListLabel('listType', $this->reportValue),
                        $partner,
                    );
                }
            }
        }

        if (!empty($csvData)) {
            foreach ($this->results['chartData']['rows'] as $temp) {
                $row = array();
                foreach ($temp as $rowData) {
                    foreach ($rowData as $key => $colData) {
                        $csvData[$key][] = $colData['v'];
                    }
                }
            }
        }

        $this->csvData = $csvData;
    }

    /*
     * add rows to chart data
     * @param array $rows - ActiveRecordTradeData->listData()
     */
    protected function addRows ($rows) {
        $data = array();
        if (!empty($rows)) {
            foreach ($rows as $attr => $val) {
                if (strpos($attr, 'MA') !== false) {
                    $parts = DbAllMonTtfResults::model()->listAttrParts($attr);
                    $parts['type'] = $this->reportValue;
                } else {
                    $parts = ActiveRecordTradeData::model()->listAttrParts($attr);
                }

                if (!empty($parts['type']) && array_key_exists($parts['type'], ActiveRecordTradeData::model()->listType())) {
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
                    }
                }
            }
        }
    }

    /*
     * render a html table to chartData
     */
    public function addTable () {
        $html = '';
        if (!empty($this->chartData['rows'])) {
            $indicator = ActiveRecordTradeData::model()->getListLabel('listType', $this->reportValue);
            if (!empty($this->indicator)) {
                foreach ($this->listIndicator() as $key => $val) {
                    if (array_key_exists($this->indicator, $val)) {
                        $indicator = $val[$this->indicator];
                        break;
                    }
                }
            }

            $html = $this->render('trade/table', array(
                'id' => $this->chartId,
                'data' => $this->chartData,
                'indicator' => $indicator,
            ), true);
        }

        return $html;
    }

    public function addTitle ($title = null) {
        $html = '';

        $compare = empty($this->reportIndicator) || $this->reportIndicator != 'top10';
        if (!empty($this->partner)) {
            $compare = false;
        }
        if (!empty($this->sector) && count($this->sector) > 1) {
            $compare = false;
        }

        if (empty($title)) {
            if ($this->reportIndicator == 'flow') {
                $title = $this->reporter->country . ': ' . ActiveRecordTradeData::model()->getListLabel('listIndicator', $this->indicator);

                if (!empty($this->partner)) {
                    $title .= ': ';
                    foreach ($this->partner as $i => $partner) {
                        if ($i > 0) {
                            $title .= ', ';
                        }
                        $title .= $partner->country;
                    }
                }
                if (!empty($this->sector)) {
                    $title .= ' / ';
                    foreach ($this->sector as $i => $sector) {
                        if ($i > 0) {
                            $title .= ', ';
                        }
                        $title .= $sector->name;
                    }
                }

            } else if (!empty($this->indicator) && strpos($this->indicator, 'top10-') !== false) {
                $title = $this->reporter->country;
                $temp = $this->listIndicator();
                if (!empty($temp['Top 10']) && !empty($temp['Top 10'][$this->indicator])) {
                    $title .= ': ' . $temp['Top 10'][$this->indicator];
                }
            } else if (!empty($this->partner)) {
                $title = $this->reporter->country . ' ' . ActiveRecordTradeData::model()->getListLabel('listType', $this->reportValue) . ': ';
                foreach ($this->partner as $i => $partner) {
                    if ($i > 0) {
                        $title .= ', ';
                    }
                    $title .= $partner->country;
                }
            } else if (!empty($this->sector)) {
                $title = $this->reporter->country . ' ' . ActiveRecordTradeData::model()->getListLabel('listType', $this->reportValue) . ': ';
                foreach ($this->sector as $i => $sector) {
                    if ($i > 0) {
                        $title .= ', ';
                    }
                    $title .= $sector->name;
                }
            } else {
                $title = $this->reporter->country . ': ' . ActiveRecordTradeData::model()->getListLabel('listType', $this->reportValue);
            }
        }

        $this->name = $title;
        if ($this->view == 'pdf') {
            $html = $title;
        } else if (!empty($title)) {
            $html = $this->render('trade/title', array(
                'title' => $title,
                'editable' => $this->editable,
                'compare' => $compare,
            ), true);
        }

        return $html;

    }

    protected function calcChange ($reporter, $model) {
        if ($this->period == 'month') {
            $rows = $model->listData(array(
                'type' => $this->reportValue,
                'period' => 'month',
                'start' => date('Ym', strtotime('-1 month', $this->startTime)),
                'end' => date('Ym', $this->endTime),
            ));
            $data = array();
            if (!empty($rows)) {
                $prev = null;
                foreach ($rows as $attr => $val) {
                    if (!empty($prev) && $prev != 0) {
                        $parts = ActiveRecordTradeData::model()->listAttrParts($attr);
                        if (!empty($parts['type']) && array_key_exists($parts['type'], ActiveRecordTradeData::model()->listType())) {
                            $date = date('M Y', strtotime($parts['year'] . '-' . $parts['month'] . '-1'));
                            $data[$date] = (($val - $prev) / $prev) * 100;
                        }
                    }

                    $prev = $val;
                }
                $this->mom[] = $data;
            }
        }

        if ($this->period == 'quarter') {
            $rows = $model->listData(array(
                'type' => $this->reportValue,
                'period' => 'quarter',
                'start' => date('Ym', strtotime('-3 month', $this->startTime)),
                'end' => date('Ym', $this->endTime),
            ));

            $data = array();
            if (!empty($rows)) {
                $prev = null;
                foreach ($rows as $attr => $val) {

                    if (!empty($prev) && $prev != 0) {
                        $parts = ActiveRecordTradeData::model()->listAttrParts($attr);
                        if (!empty($parts['type']) && array_key_exists($parts['type'], ActiveRecordTradeData::model()->listType())) {
                            $date = $parts['year'] . ' ' . $parts['month'];
                            $data[$date] = (($val - $prev) / $prev) * 100;
                        }
                    }

                    $prev = $val;
                }

                $this->qoq[] = $data;
            }
        }

        $rows = $model->listData(array(
            'type' => $this->reportValue,
            'period' => $this->period,
            'start' => date('Ym', strtotime('-12 month', $this->startTime)),
            'end' => date('Ym', $this->endTime),
        ));
        $data = array();

        if (!empty($rows)) {
            foreach ($rows as $attr => $val) {
                $parts = ActiveRecordTradeData::model()->listAttrParts($attr);

                if (!empty($parts['type']) && array_key_exists($parts['type'], ActiveRecordTradeData::model()->listType())) {
                    switch ($this->period) {
                        case 'month':
                            if (!empty($parts['month']) && is_numeric($parts['month']) && !empty($parts['year']) && is_numeric($parts['year'])) {
                                $date = date('M Y', strtotime($parts['year'] . '-' . $parts['month'] . '-1'));
                                $prev = $parts['type'] . ($parts['year'] - 1) . $parts['month'];
                            }
                            break;

                        case 'annual':
                            if (!empty($parts['year']) && is_numeric($parts['year'])) {
                                $date = $parts['year'];
                                $prev = $parts['type'] . ($parts['year'] - 1);
                            }
                            break;

                        case 'quarter':
                            if (!empty($parts['month']) && in_array($parts['month'], array('Q1', 'Q2', 'Q3', 'Q4'))) {
                                $date = $parts['year'] . ' ' . $parts['month'];
                                $prev = $parts['type'] . ($parts['year'] - 1) . $parts['month'];
                            }
                            break;
                    }

                    if (!empty($rows[$prev]) && $rows[$prev] != 0) {
                        $data[$date] = (($val - $rows[$prev]) / $rows[$prev]) * 100;
                    }
                }
            }

            $this->yoy[] = $data;
        }

    }

    /*
     * return a db row form the requested table
     * @param DbReporter $reporter
     * @param string $ref - PISO3 / Commodity_Code
     * @return ActiveRecord
     */
    protected function fetchModel ($reporter, $ref = null, $type = null) {
        $model = null;
        $criteria = new CDbCriteria();
        $partner = $sector = false;

        if (empty($type)) {
            $type = $this->reportType;
        }

        switch ($type) {
            case 'flow':
                $model = new DbAllMonTtfResults();
                $criteria->compare('TF', $this->reportValue);
                $partner = $sector = true;
                break;

            case 'partner-top10':
                $model = new DbAllMonTtfResults();
                $criteria->compare('TF', $this->reportValue);
                $partner = true;
                break;

            case 'sector-top10':
                $model = new DbAllMonTtfResults();
                $criteria->compare('TF', $this->reportValue);
                $sector = true;
                break;

            case 'partner':
                $model = new DbAllMonTtpResults();
                $partner = true;
                break;

            case 'sector':
                $model = new DbAllMonTtccResults();
                $sector = true;
                break;

            case 'trade':
                $model = new DbAllMonTtResults();
                break;
        }

        $criteria->compare('RISO3', $reporter->code);

        if ($partner && $this->reportIndicator != 'top10') {
            $temp = 'FAIL';
            if (!empty($ref['partner'])) {
                $temp = $ref['partner'];
            }
            $criteria->compare('PISO3', $temp);
        }

        if ($sector && $this->reportIndicator != 'top10') {
            $temp = 'FAIL';
            if (!empty($ref['sector'])) {
                $temp = $ref['sector'];
            }
            $criteria->compare('Commodity_Code', $temp);
        }

        $return = null;
        if (!empty($model)) {
            $return = $model->find($criteria);
        }

        if (empty($return) && $partner) {
            $model->RISO3 = $reporter->code;
            $model->PISO3 = 'FAIL';
            if (!empty($ref['partner'])) {
                $model->PISO3 = $ref['partner'];
            }
            if ($type == 'flow') {
                $model->Commodity_Code = 'FAIL';
                if (!empty($ref['sector'])) {
                    $model->Commodity_Code = $ref['sector'];
                }
            }
            if($model->hasAttribute('TF')) {
                $model->TF = $this->reportValue;
            }
            $return = $model->getMerged();
        }

        return $return;
    }

    protected function getDefaultResults () {
        $list = parent::getDefaultResults();
        $list['axis']['y']['label'] = 'Value in USD';
        $list['color'] = $this->reporter->color;

        return $list;
    }

    public function listIndicator () {
        return ActiveRecordTradeData::model()->listIndicator();
    }

    /*
     * run a default standard report
     */
    protected function runReport () {
        switch ($this->report) {
            case 'yoy-change':
                $data = $this->reportYoyChange();
                break;

            case 'default':
            default:
                $data = $this->reportDefault();
                break;
        }

        $this->formatRows();
        $data = $this->getDefaultResults();
        if ($this->showTable) {
            $data['table'] = $this->addTable();
        }
        $data['title'] = $this->addTitle();
        $data['url'] = $this->url;
        $data['showLegend'] = $this->showLegend;

        $this->results = $data;
    }

    protected function reportDefault () {
        $data = array();
        $reporter = $this->reporter;

        if (!empty($reporter)) {
            //set column data
            $title = $reporter->country;

            $groups = array(
                'partner' => array(),
                'sector' => array(),
            );

            if (!empty($this->partner)) {
                foreach ($this->partner as $partner) {
                    $groups['partner'][$partner->code] = $partner;
                }
            }

            if (!empty($this->sector)) {
                foreach ($this->sector as $sector) {
                    $groups['sector'][$sector->code] = $sector;
                }
            }

            $options = array(
                'type' => $this->reportValue,
                'period' => $this->period,
                'start' => date('Ym', $this->startTime),
                'end' => date('Ym', $this->endTime),
                'reportType' => $this->reportType,
            );

            if ($this->reportIndicator == 'flow') {
                if (strpos($this->reportType, 'top10') !== false) {
                    $this->chartType = 'column';
                    $top10 = DbAllMonTtfResults::model()->calcTop($this->reporter, array(
                        'type' => $this->reportValue,
                        'start' => date('Ym', $this->startTime),
                        'end' => date('Ym', $this->endTime),
                        'reportType' => str_replace('-top10', '', $this->reportType),
                        'partner' => array_keys($groups['partner']),
                        'sector' => array_keys($groups['sector']),
                    ));

                    if (!empty($top10)) {
                        foreach ($top10 as $i => $row) {
                            if (!empty($row->partner) && !empty($row->sector)) {
                                $color = $row->partner->color;
                                if (count($row->partner) == 1) {
                                    $color = $this->getRandColor($i);
                                }

                                $this->addColumn(array(
                                    'id' => $row->partner->code . '-' . $row->sector->code,
                                    //'label' => (!empty($reporter) ? $data->country . ' (' . $reporter->country . ')' : $data->country),
                                    'label' => $row->partner->country . ' / ' . $row->sector->name,
                                    'color' => $color,
                                ), 'number', $reporter);
                                $results = $row->listData($options);
                                $this->addRows($results);
                            }
                        }
                    }

                } else if (!empty($groups['partner']) && !empty($groups['sector'])) {
                    $i = 0;
                    foreach ($groups['partner'] as $partner => $partnerModel) {
                        foreach ($groups['sector'] as $sector => $sectorModel) {
                            $model = $this->fetchModel($reporter, array(
                                'partner' => $partner,
                                'sector' => $sector,
                            ), 'flow');

                            if (!empty($model)) {
                                $color = $this->getRandColor($i);
                                $i++;

                                $this->addColumn(array(
                                    'id' => $partner . '-' . $sector,
                                    //'label' => (!empty($reporter) ? $data->country . ' (' . $reporter->country . ')' : $data->country),
                                    'label' => $partnerModel->country . ' / ' . $sectorModel->name,
                                    //'color' => $partnerModel->color,
                                    'color' => $color,
                                ), 'number', $reporter);
                                $results = $model->listData($options);
                                $this->addRows($results);
                            }
                        }
                    }
                }

                //top 10
            } else if ($this->reportIndicator == 'top10') {
                $model = $this->fetchModel($reporter);
                if (!empty($model)) {
                    $this->chartType = 'column';
                    $top10 = $model->calcTop($this->reporter, $options);
                    if (!empty($top10)) {
                        foreach ($top10 as $i => $row) {
                            $subRef = null;
                            if ($this->reportType == 'partner') {
                                $subRef = $row->partner;
                            } else if ($this->reportType == 'sector') {
                                $subRef = $row->sector;
                            }
                            if (!empty($subRef)) {
                                $this->addColumn($subRef);
                                $results = $row->listData($options);
                                $this->addRows($results);
                            }
                        }
                    }
                }

                //partner & sector reports
            } else if (!empty($groups['partner']) || !empty($groups['sector'])) {
                foreach ($groups as $group => $groupsData) {
                    if (!empty($groupsData)) {
                        $refs = $groupsData;
                        foreach ($groupsData as $ref => $refModel) {
                            $model = $this->fetchModel($reporter, array($group => $ref));
                            if (!empty($model)) {
                                $this->addColumn($refModel, 'number', $reporter);
                                $results = $model->listData($options);
                                $this->addRows($results);
                                $this->calcChange($reporter, $model);
                            }
                        }
                    }
                }
            } else {
                $model = $this->fetchModel($reporter);
                if (!empty($model)) {
                    $this->addColumn($reporter);
                    $results = $model->listData($options);
                    $this->addRows($results);
                    $this->calcChange($reporter, $model);
                }
            }

            //add additional rows/cols for reporter comparisons
            if (!empty($this->compare)) {
                foreach ($this->compare as $compare) {
                    if (!empty($groups['partner']) || !empty($groups['sector'])) {
                        foreach ($groups as $group => $groupsData) {
                            if (!empty($groupsData)) {
                                $refs = $groupsData;
                                foreach ($groupsData as $ref => $refModel) {
                                    $model = $this->fetchModel($compare, array($group => $ref));
                                    if (!empty($model)) {
                                        $this->addColumn($refModel, 'number', $compare);
                                        $results = $model->listData($options);
                                        $this->addRows($results);
                                        $this->calcChange($compare, $model);
                                    }
                                }
                            }
                        }

                        /*foreach ($refs as $ref => $refModel) {
                            $model = $this->fetchModel($compare, $ref);
                            var_dump($model);
                            if (!empty($model)) {
                                $this->addColumn($refModel, 'number', $compare);
                                $results = $model->listData(array(
                                    'type' => $this->reportValue,
                                    'period' => $this->period,
                                    'start' => date('Ym', $this->startTime),
                                    'end' => date('Ym', $this->endTime),
                                ));
                                $this->addRows($results);
                            }
                        }*/
                    } else {
                        $model = $this->fetchModel($compare);
                        if (!empty($model)) {
                            $this->addColumn($compare);
                            $results = $model->listData($options);
                            $this->addRows($results);
                            $this->calcChange($compare, $model);
                            /*$this->addColumn($compare);
                            $results = $model->listData(array(
                                'type' => $this->reportValue,
                                'period' => $this->period,
                                'start' => date('Ym', $this->startTime),
                                'end' => date('Ym', $this->endTime),
                            ));
                            $this->addRows($results);
                            $this->calcChange($reporter, $model);*/
                        }
                    }
                }
            }
        }

        return $data;
    }

    protected function reportYoyChange () {
        $data = array();
        $reporter = $this->reporter;

        if (!empty($reporter)) {
            //set column data
            $title = $reporter->country;

            $groups = array(
                'partner' => array(),
                'sector' => array(),
            );

            if (!empty($this->partner)) {
                foreach ($this->partner as $partner) {
                    $groups['partner'][$partner->code] = $partner;
                }
            }

            if (!empty($this->sector)) {
                foreach ($this->sector as $sector) {
                    $groups['sector'][$sector->code] = $sector;
                }
            }

            $dates = [
                'current' => [
                    'start' => date('Ym', $this->startTime),
                    'end' => date('Ym', $this->endTime),
                ],
                'prev' => [
                    'start' => date('Ym', strtotime('-1 year', $this->startTime)),
                    'end' => date('Ym', strtotime('-1 year', $this->endTime)),
                ],
            ];

            $options = array(
                'type' => $this->reportValue,
                'period' => $this->period,
                'start' => date('Ym', $this->startTime),
                'end' => date('Ym', $this->endTime),
                'reportType' => $this->reportType,
            );
            if (!empty($groups['partner']) || !empty($groups['sector'])) {
                foreach ($groups as $group => $groupsData) {
                    if (!empty($groupsData)) {
                        $refs = $groupsData;
                        foreach ($groupsData as $ref => $refModel) {
                            $model = $this->fetchModel($reporter, array($group => $ref));
                            if (!empty($model)) {
                                $this->addColumn([
                                    'id' => 'current',
                                    'label' => $refModel->name.' Forecast',
                                    'color' => $this->getRandColor(0)
                                ]);
                                $this->addColumn([
                                    'id' => 'yoy',
                                    'label' => $refModel->name.' Last Year',
                                    'color' => $this->getRandColor(6)
                                ]);

                                $options['start'] = $dates['current']['start'];
                                $options['end'] = $dates['current']['end'];
                                $current = $model->listData($options);

                                $data = array();
                                if (!empty($current)) {
                                    $prev = null;
                                    foreach ($current as $attr => $val) {
                                        if (!empty($prev) && $prev != 0) {
                                            $parts = ActiveRecordTradeData::model()->listAttrParts($attr);
                                            if (!empty($parts['type']) && array_key_exists($parts['type'], ActiveRecordTradeData::model()->listType())) {
                                                $date = date('M Y', strtotime($parts['year'] . '-' . $parts['month'] . '-1'));
                                                $change = (($val - $prev) / $prev) * 100;
                                                $data[$date] = $change;

                                                if (empty($this->chartData['rows'][$date])) {
                                                    $this->chartData['rows'][$date] = array();
                                                }
                                                $this->chartData['rows'][$date][] = $change;
                                            }
                                        }

                                        $prev = $val;
                                    }


                                }

                                $options['start'] = $dates['prev']['start'];
                                $options['end'] = $dates['prev']['end'];
                                $yoy = $model->listData($options);

                                if (!empty($yoy)) {
                                    $prev = null;
                                    foreach ($yoy as $attr => $val) {
                                        if (!empty($prev) && $prev != 0) {
                                            $parts = ActiveRecordTradeData::model()->listAttrParts($attr);
                                            if (!empty($parts['type']) && array_key_exists($parts['type'], ActiveRecordTradeData::model()->listType())) {
                                                $date = date('M Y', strtotime('+1 year', strtotime($parts['year'] . '-' . $parts['month'] . '-1')));
                                                $change = (($val - $prev) / $prev) * 100;
                                                $data[$date] = $change;

                                                if (empty($this->chartData['rows'][$date])) {
                                                    $this->chartData['rows'][$date] = array();
                                                }
                                                $this->chartData['rows'][$date][] = $change;
                                            }
                                        }

                                        $prev = $val;
                                    }
                                }
                            }
                        }
                    }
                }
            } else {
                $model = $this->fetchModel($reporter);
                if (!empty($model)) {
                    $this->addColumn([
                        'id' => 'current',
                        'label' => $reporter->name.' Forecast',
                        'color' => $this->getRandColor(0)
                    ]);
                    $this->addColumn([
                        'id' => 'yoy',
                        'label' => $reporter->name.' Last Year',
                        'color' => $this->getRandColor(6)
                    ]);

                    $options['start'] = $dates['current']['start'];
                    $options['end'] = $dates['current']['end'];
                    $current = $model->listData($options);

                    $data = array();
                    if (!empty($current)) {
                        $prev = null;
                        foreach ($current as $attr => $val) {
                            if (!empty($prev) && $prev != 0) {
                                $parts = ActiveRecordTradeData::model()->listAttrParts($attr);
                                if (!empty($parts['type']) && array_key_exists($parts['type'], ActiveRecordTradeData::model()->listType())) {
                                    $date = date('M Y', strtotime($parts['year'] . '-' . $parts['month'] . '-1'));
                                    $change = (($val - $prev) / $prev) * 100;
                                    $data[$date] = $change;

                                    if (empty($this->chartData['rows'][$date])) {
                                        $this->chartData['rows'][$date] = array();
                                    }
                                    $this->chartData['rows'][$date][] = $change;
                                }
                            }

                            $prev = $val;
                        }


                    }

                    $options['start'] = $dates['prev']['start'];
                    $options['end'] = $dates['prev']['end'];
                    $yoy = $model->listData($options);

                    if (!empty($yoy)) {
                        $prev = null;
                        foreach ($yoy as $attr => $val) {
                            if (!empty($prev) && $prev != 0) {
                                $parts = ActiveRecordTradeData::model()->listAttrParts($attr);
                                if (!empty($parts['type']) && array_key_exists($parts['type'], ActiveRecordTradeData::model()->listType())) {
                                    $date = date('M Y', strtotime('+1 year', strtotime($parts['year'] . '-' . $parts['month'] . '-1')));
                                    $change = (($val - $prev) / $prev) * 100;
                                    $data[$date] = $change;

                                    if (empty($this->chartData['rows'][$date])) {
                                        $this->chartData['rows'][$date] = array();
                                    }
                                    $this->chartData['rows'][$date][] = $change;
                                }
                            }

                            $prev = $val;
                        }
                    }

                }
            }
        }

        return $data;
    }

    protected function setDefaults () {
        parent::setDefaults();

        if (!empty($this->indicator)) {
            $this->indicator = str_replace('_', '-', $this->indicator);

            $parts = explode('-', $this->indicator);
            if (!empty($parts)) {
                if (!empty($parts[0])) {
                    $this->reportIndicator = $parts[0];
                }
                if (!empty($parts[1]) && $parts[1] != 'none') {
                    $this->reportType = $parts[1];
                }
                if (!empty($parts[2])) {
                    $this->reportValue = strtoupper($parts[2]);
                }

                if ($this->reportIndicator == 'flow') {
                    if (!empty($parts[2])) {
                        $this->reportType .= '-' . $parts[2];
                    }
                    if (!empty($parts[3])) {
                        $this->reportValue = strtoupper($parts[3]);
                    }
                }
            }
        }


        //set public url
        $params = array();
        $page = 'trade/country';
        if (!empty($this->reporter)) {
            if (is_object($this->reporter)) {
                $params['id'] = $this->reporter->code;
            } else {
                $params['id'] = $this->reporter;
            }
        }

        if (!empty($this->indicator)) {
            $params['indicator'] = str_replace('-', '_', $this->indicator);
        }

        $attrs = array(
            'endTime',
            'startTime',
            //'reportValue',
            //'reportType',
            'partner',
            'sector',
            'compare',
        );
        foreach ($attrs as $key) {
            if (!empty($this->$key)) {
                if (is_array($this->$key)) {
                    switch ($key) {
                        case 'partner':
                        case 'compare':
                        case 'sector':
                            $params[$key] = implode(',', $this->$key);
                            break;
                    }
                } else if (is_object($this->$key)) {
                    switch ($key) {
                        case 'sector':
                        case 'partner':
                            $params[$key] = $this->$key->code;
                            break;
                    }
                } else {
                    $params[$key] = $this->$key;
                }
            }
        }

        $this->url = Yii::app()->createAbsoluteUrl($page, $params);
    }

    /*
     * convert class vars to models
     * @return array
     */
    protected function setModels () {
        if (!empty($this->reporter) && !is_object($this->reporter)) {
            $this->reporter = DbReporters::model()->findByAttributes(array('ccode3' => $this->reporter));
            if (!empty($this->user)) {
                $criteria = new CDbCriteria();
                $criteria->compare('userId', $this->user->id);
                $criteria->compare('type', 'country');
                $criteria->compare('refType', get_class($this->reporter));
                $criteria->compare('refId', $this->reporter->pkVal);
                $this->watchlistItem = DbUserWatchlist::model()->find($criteria);
            }
        }
        if (!empty($this->partner) && !is_object($this->partner)) {
            $partners = $this->partner;
            if (!is_array($partners)) {
                $partners = explode(',', $partners);
            }
            if (!empty($partners)) {
                $exists = $this->partner = array();
                foreach ($partners as $partner) {
                    if (!in_array($partner, $exists)) {
                        $partner = DbPartners::model()->findByAttributes(array('ccode3' => $partner));
                        if (!empty($partner)) {
                            $exists[] = $partner->ccode3;
                            $this->partner[] = $partner;
                        }
                    }
                }
            }
        }
        if (!empty($this->sector) && !is_object($this->sector)) {
            $sectors = $this->sector;
            if (!is_array($sectors)) {
                $sectors = explode(',', $sectors);
            }
            if (!empty($sectors)) {
                $exists = $this->sector = array();
                foreach ($sectors as $sector) {
                    if (!in_array($sector, $exists)) {
                        $sector = DbSectors::model()->findByAttributes(array('code' => $sector));
                        if (!empty($sector)) {
                            $exists[] = $sector->code;
                            $this->sector[] = $sector;
                        }
                    }
                }
            }
        }
        if (!empty($this->compare) && !is_object($this->compare)) {
            $reporters = $this->compare;
            if (!is_array($reporters)) {
                $reporters = explode(',', $reporters);
            }
            if (!empty($reporters)) {
                $exists = $this->compare = array();
                foreach ($reporters as $reporter) {
                    if (!in_array($reporter, $exists)) {
                        $reporter = DbReporters::model()->findByAttributes(array('ccode3' => $reporter));
                        if (!empty($reporter)) {
                            $exists[] = $reporter->code;
                            $this->compare[] = $reporter;
                        }
                    }
                }
            }
        }
    }

    protected function validatePremium () {
        $valid = true;

        if (empty($this->reporter) && !empty($this->indicator)) {
            $valid = false;
        }

        if ($valid && empty($this->ignorePermissions) && !empty($this->reporter) && !Yii::app()->user->checkToolAccess('tra', 'service-pro')) {
            if($reporter->access == 1){
                if(!Yii::app()->user->checkToolAccess('tra', 'service-ess')){
                    $valid = false;
                    $this->error[] = $reporter->country . ' is only available to Essentials accounts';
                }
            } else if($reporter->access == 2){
                if(!Yii::app()->user->checkToolAccess('tra', 'service-pro')){
                    $valid = false;
                    $this->error[] = $reporter->country . ' is only available to Pro accounts';
                }
            }

            if (!empty($this->compare)) {
                $temp = array();
                foreach ($this->compare as $compare) {
                    if($compare->access == 1){
                        if(!Yii::app()->user->checkToolAccess('tra', 'service-ess')){
                            $valid = false;
                            $this->error[] = $compare->country . ' is only available to Essentials accounts';
                        } else {
                            $temp[] = $compare;
                        }
                    } else if($compare->access == 2){
                        if(!Yii::app()->user->checkToolAccess('tra', 'service-pro')){
                            $valid = false;
                            $this->error[] = $compare->country . ' is only available to Pro accounts';
                        } else {
                            $temp[] = $compare;
                        }
                    }
                }
                $this->compare = $temp;
            }
        }


        if ($valid && !empty($this->reporter) && !in_array('trade', $this->reporter->type)) {
            $valid = false;
            $this->error[] = $this->reporter->country . ' is not available with trade data reports';
        }

        if (!empty($this->reporter) && (!empty($this->partner) || !empty($this->sector))) {
            $groups = array(
                'partner' => array(),
                'sector' => array(),
            );

            if (!empty($this->partner)) {
                foreach ($this->partner as $partner) {
                    $groups['partner'][] = $partner->code;
                }
            }

            if (!empty($this->sector)) {
                foreach ($this->sector as $sector) {
                    $groups['sector'][] = $sector->code;
                }
            }


            if ($this->reportIndicator == 'flow' && !empty($this->partner) && !empty($this->sector)) {
                $partners = $sectors = array();
                foreach ($this->partner as $partner) {
                    foreach ($this->sector as $sector) {
                        $model = $this->fetchModel($this->reporter, array(
                            'partner' => $partner->code,
                            'sector' => $sector->code,
                        ), 'flow');
                        if (!empty($model)) {
                            $partners[] = $partner;
                            $sectors[] = $sector;
                        } else {
                            $this->error[] = 'Sorry, there is not enough data available for ' . $this->reporter->country . ' and the ' . $partner->code . ' / ' . $sector->code;
                        }
                    }
                }
                if (empty($partners) || empty($sectors)) {
                    $valid = false;
                }
            } else {
                foreach ($groups as $group => $refs) {
                    if (!empty($refs)) {
                        foreach ($refs as $ref) {
                            $model = $this->fetchModel($this->reporter, array($group => $ref));
                            if (empty($model)) {
                                $valid = false;
                                $this->error[] = 'Sorry, there is not enough data available for ' . $this->reporter->country . ' and the ' . $this->reportType . ' ' . $ref;
                            }
                        }
                    }
                }
            }
        }

        if (empty($this->partner) && strpos($this->indicator, 'partner') !== false && substr($this->indicator, 0, 5) != 'top10') {
            $this->error[] = 'Report needs at least 1 partner country to be selected';
            $valid = false;
        }
        if (empty($this->sector) && strpos($this->indicator, 'sector') !== false && substr($this->indicator, 0, 5) != 'top10') {
            $this->error[] = 'Report needs at least 1 commodity to be selected';
            $valid = false;
        }

        return $valid;
    }

    public function isFavorite () {
        $valid = false;
        $data = array();
        $attrs = DbUserFavorites::model()->listDataAttrs('trade');
        if (!empty($attrs)) {
            foreach ($attrs as $attr) {
                if (isset($this->$attr)) {
                    if (is_object($this->$attr)) {
                        if (get_class($this->$attr) == 'DbReporters' || get_class($this->$attr) == 'DbPartners') {
                            $data[$attr] = $this->$attr->code;
                        } else if (get_class($this->$attr) == 'DbSectors') {
                            $data[$attr] = $this->$attr->code;
                        }
                    } else if (is_array($this->$attr)) {
                        foreach ($this->$attr as $obj) {
                            if (get_class($obj) == 'DbReporters' || get_class($obj) == 'DbPartners') {
                                $data[$attr][] = $obj->code;
                            } else if (get_class($obj) == 'DbSectors') {
                                $data[$attr][] = $obj->code;
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
            $valid = DbUserFavorites::model()->isAssigned($this->user->id, 'trade', $data);
        }

        return $valid;
    }
}

?>