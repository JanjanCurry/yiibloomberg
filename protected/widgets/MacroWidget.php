<?php class MacroWidget extends ChartWidget {

    //models
    public $reporter;

    public $mom = array();
    public $qoq = array();
    public $yoy = array();

    //form vars
    public $macro;
    public $period = 'annual';
    public $basePeriod;
    public $variant;
    protected $type = 'macro';
    protected $fieldList = array(
        'reporter',
        'compare',
        'chartId',
        'chartType',
        'period',
        'variant',
        'macro',
        'startTime',
        'endTime',
        'view',
    );


    //processing vars
    public $notes = array('country' => array(), 'notes' => array());


    public function run () {
        parent::run();

        if ($this->init) {
            if ($this->editable) {
                Yii::app()->controller->registerFile('js', 'chart/macro');
            }

            if (!empty($this->reporter) && !empty($this->macro)) {
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
                    'data' => $this->render('macro/data', null, true),
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
                $this->runReport();
                if(!empty($this->mom)) {
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
                break;

            case 'dashboard-group':
                //$this->addTitle();
                $this->results = $this->render('macro/group', $this->data, true);
                break;

            case 'favorites':
            case 'group':
                $this->results = $this->render('macro/'.$this->view, $this->data, !$this->init);
                break;

            default:
                $this->runReport();
        }

    }

    /*
     * add a single column to $this->chartData
     * @param mixed $reporter - DbReporter
     * @param string $macro - DbAllAnnMacros / DbAllQtrMacros / DbAllMonMacros
     */
    protected function addColumn ($reporter, $macro = null) {
        $col = array();
        if (!empty($reporter)) {
            if (is_object($reporter)) {
                //change titles and colors if the chart is a macro comparison (i.e. using duplicate countries)
                $label = $reporter->country;
                $color = $reporter->color;
                if (!empty($this->compare) && !empty($macro)) {
                    $macroData = DbMacroList::model()->findByAttributes(array('assetId' => $macro->Asset_ID));
                    if (!empty($macroData)) {
                        $label = $macroData->assetName . ' / ' . $reporter->country;
                        $color = $macroData->color;
                    }
                }

                //set the chart data
                $col = array(
                    //google charts
                    'id' => $reporter->ccode3,
                    'type' => 'number',
                    //'label' => $reporter->country,
                    'label' => $label,

                    //additional
                    'country' => $reporter->country,
                    'color' => $color,
                    'source' => (!empty($macro) && !empty($macro->source) ? $macro->source->description : ''),
                    'macro' => $macro->Asset_ID,
                    'variant' => $macro->Variant,
                );

                //add notes if there are any
                if (!empty($this->notes['country']) && !empty($col['country'])) {
                    foreach ($this->notes['country'] as $key => $val) {
                        if (in_array($col['country'], $val)) {
                            $col['note'] = ' ^' . $key;
                        }
                    }
                }
            }else{
                $col = array(
                    //google charts
                    'id' => $reporter['id'],
                    'type' => 'number',
                    'label' => $reporter['label'],

                    //additional
                    'color' => $reporter['color'],
                );
            }

            $this->chartData['cols'][] = $col;
        }
    }

    /*
     * Generate csv compatible array of the results
     * @return boolean
     */
    protected function addCsv () {
        $csvData = array();
        if (!empty($this->results['chartData']['cols'])) {
            foreach ($this->results['chartData']['cols'] as $key => $colData) {
                $indicator = '';
                if ($colData['label'] == 'Period') {
                    $csvData[$key] = array(
                        'Country',
                        'Indicator',
                        'Units',
                        'Source',
                    );
                } else {
                    $label = $colData['label'];
                    if (!empty($this->notes['country']) && !empty($colData['country'])) {
                        foreach ($this->notes['country'] as $key2 => $val) {
                            if (in_array($colData['country'], $val)) {
                                $label .= ' ^' . $key2;
                            }
                        }
                    }

                    $csvData[$key] = array(
                        $label,
                        ActiveRecordMacroData::model()->getListLabel('listMacro', $colData['macro']),
                        ActiveRecordMacroData::model()->getListLabel('listVariant', $colData['variant']),
                        $colData['source']
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
        $condense = $data = array();
        if (!empty($rows)) {
            foreach ($rows as $attr => $val) {
                $date = '';

                switch ($this->period) {
                    case 'month':
                        $parts = DbAllMonMacros::model()->listAttrParts($attr);
                        break;

                    case 'annual':
                        $parts = DbAllAnnMacros::model()->listAttrParts($attr);
                        break;

                    case 'quarter':
                        $parts = DbAllQtrMacros::model()->listAttrParts($attr);
                        break;
                }

                if (!empty($parts)) {
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

            if (!empty($condense)) {
                foreach ($condense as $date => $vals) {
                    $total = 0;
                    foreach ($vals as $val) {
                        $total += $val;
                    }
                    $this->chartData['rows'][$date][] = $total;
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
            $html = $this->render('macro/table', array(
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
        $html = '';


        $countries = array();
        if (!empty($this->reporter)) {
            if (count($this->reporter) > 1) {
                $this->showCompare = false;
            }
            foreach ($this->reporter as $country) {
                $countries[] = $country->country;
            }
        }

        $title = implode('/', $countries) . ': ' . ActiveRecordMacroData::model()->getListLabel('listMacro', $this->macro);

        if (!empty($this->compare)) {
            foreach ($this->compare as $macro) {
                $title .= ' / ' . ActiveRecordMacroData::model()->getListLabel('listMacro', $macro);
            }
        }

        $this->name = $title;
        if($this->view == 'pdf'){
            $html = $title;
        } else if (!empty($title)) {
            $html = $this->render('macro/title', array(
                'title' => $title,
            ), true);
        }

        return $html;

    }

    protected function calcChange ($reporter, $model) {
        if ($this->period == 'month') {
            $rows = $model->listData(array(
                'type' => $this->macro,
                'period' => 'month',
                'start' => date('Ym', strtotime('-1 month', $this->startTime)),
                'end' => date('Ym', $this->endTime),
            ));

            $data = array();
            if (!empty($rows)) {
                $prev = null;
                foreach ($rows as $attr => $val) {
                    if (!empty($prev) && $prev != 0) {
                        $parts = DbAllMonMacros::model()->listAttrParts($attr);
                        $date = date('M Y', strtotime($parts['year'] . '-' . $parts['month'] . '-1'));
                        $data[$date] = (($val - $prev) / $prev) * 100;
                    }

                    $prev = $val;
                }
                $this->mom[] = $data;
            }
        }
    }

    /*
     * return a db row form the requested table
     * @param DbReporter $reporter
     * @param Asset_ID $macro
     * @return ActiveRecord
     */
    protected function fetchModel ($reporter, $macro = null) {
        if (empty($macro)) {
            $macro = $this->macro;
        }

        $model = null;
        $criteria = new CDbCriteria();
        $criteria->compare('RISO3', $reporter->ccode3);
        $criteria->compare('Asset_ID', $macro);
        //$criteria->compare('variant', $this->variant);

        switch ($this->period) {
            case 'month':
                $model = new DbAllMonMacros();
                break;

            case 'annual':
                $model = new DbAllAnnMacros();
                break;

            case 'quarter':
                $model = new DbAllQtrMacros();
                break;
        }

        if (!empty($model)) {
            $model = $model->find($criteria);
            if (!empty($model) && !empty($model->Variant)) {
                $this->variant = $model->Variant;
                if (!empty($model->note)) {
                    if (!empty($this->notes['country'][$model->note->id])) {
                        $this->notes['country'][$model->note->id][] = $model->reporter->country;
                    } else {
                        $this->notes['country'][$model->note->id] = array($model->reporter->country);
                        $this->notes['notes'][$model->note->id] = '^' . $model->note->id . ': ' . $model->note->note;
                    }
                }
            }
        }

        return $model;
    }

    protected function getDefaultResults () {
        $list = parent::getDefaultResults();
        $list['axis']['y']['label'] = 'Value in ' . $this->getListLabel('listVariant', $this->variant);

        return $list;
    }

    public function listMacro () {
        return ActiveRecordMacroData::model()->listMacro();
    }

    public function listVariant () {
        return ActiveRecordMacroData::model()->listVariant();
    }

    /*
     * run a default standard report
     */
    protected function runReport () {
        switch($this->report){
            case 'dash-change':
                $return = $this->reportChange();
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
        if (!empty($this->reporter)) {
            $options = array(
                'start' => date('Ym', $this->startTime),
                'end' => date('Ym', $this->endTime),
                'type' => $this->macro,
            );

            $title = array();
            foreach ($this->reporter as $reporter) {
                $title[] = $reporter->country;
                $model = $this->fetchModel($reporter, $this->macro);
                if (!empty($model)) {
                    $this->addColumn($reporter, $model);
                    $results = $model->listData($options);
                    $this->addRows($results);
                    $this->calcChange($reporter, $model);
                }
            }
            $title = implode(',', $title);

            if (!empty($this->compare)) {
                foreach ($this->compare as $macro) {
                    foreach ($this->reporter as $reporter) {

                        $model = $this->fetchModel($reporter, $macro);
                        if (!empty($model)) {
                            $this->addColumn($reporter, $model);
                            $results = $model->listData($options);
                            $this->addRows($results);
                            $this->calcChange($reporter, $model);
                        }
                    }
                }
            }

            //format results
            $this->formatRows();
            $data = $this->getDefaultResults();
            if($this->showTable) {
                $data['table'] = $this->addTable();
            }
            $data['title'] = $this->addTitle();

            $notes = array();
            if (!empty($this->notes['notes'])) {
                $data['notes'] = $this->notes['notes'];
                ksort($notes);
            }

        }
        $this->results = $data;
    }

    protected function reportChange () {
        $data = array();
        if (!empty($this->reporter)) {
            if($this->period == 'annual'){
                $options = array(
                    'start' => date('Ym', strtotime('-1 year', $this->startTime)),
                    'end' => date('Ym', $this->endTime),
                    'type' => $this->macro,
                );

                $this->addColumn([
                    'id' => 'gdp',
                    'type' => 'number',
                    'label' => date('Y', $this->endTime),
                    'color' => $this->getRandColor(0)
                ]);
            }else {
                $options = array(
                    'start' => date('Ym', strtotime('-1 month', $this->startTime)),
                    'end' => date('Ym', $this->endTime),
                    'type' => $this->macro,
                );

                $this->addColumn([
                    'id' => 'gdp',
                    'type' => 'number',
                    'label' => date('M Y', $this->endTime),
                    'color' => $this->getRandColor(0)
                ]);
            }

            $reorder = [
                'order' => [],
                'data' => [],
            ];
            foreach ($this->reporter as $reporter) {
                $model = $this->fetchModel($reporter, $this->macro);
                if (!empty($model)) {
                    $results = $model->listData($options);
                    $prev = null;
                    foreach ($results as $attr => $val) {
                        if($this->period == 'annual'){
                            $parts = DbAllAnnMacros::model()->listAttrParts($attr);
                        }else {
                            $parts = DbAllMonMacros::model()->listAttrParts($attr);
                        }
                        if (!empty($parts)) {
                            if (!empty($prev) && $prev != 0) {
                                $value = (($val - $prev) / $prev) * 100;
                                $reorder['order'][$reporter->name] = $value;
                                $reorder['data'][$reporter->name] = [
                                    'name' => $reporter->name,
                                    'value' => $value,
                                    'color' => '#'.$reporter->color,
                                ];
                                /*if (empty($this->chartData['rows'][$reporter->name])) {
                                    $this->chartData['rows'][$reporter->name] = array();
                                }
                                $this->chartData['rows'][$reporter->name][] = (($val - $prev) / $prev) * 100;
                                $this->chartData['rows'][$reporter->name][] = '#'.$reporter->color;*/
                            }
                            $prev = $val;
                        }
                    }
                }
            }
            if(!empty($reorder)){
                asort($reorder['order'], SORT_NUMERIC);
                $reorder['order'] = array_reverse($reorder['order'], true);
                foreach ($reorder['order'] as $key => $val){
                    $item = $reorder['data'][$key];
                    if (empty($this->chartData['rows'][$item['name']])) {
                        $this->chartData['rows'][$item['name']] = array();
                    }
                    $this->chartData['rows'][$item['name']][] = $item['value'];
                    $this->chartData['rows'][$item['name']][] = $item['color'];
                }
            }

            //format results
            $this->formatRows();
            $data = $this->getDefaultResults();
            $data['title'] = '<div class="chart-item-header"><span class="h4">G10 '.ActiveRecordMacroData::model()->getListLabel('listMacro', $this->macro).' % Change</span></div>'.$this->render('chart/legend', null, true);
        }
        $this->results = $data;
    }

    protected function setDefaults () {
        parent::setDefaults();
        //set public url
        $params = array();
        if (!empty($this->reporter)) {
            //$params['id'] = $this->reporter;
        }

        $attrs = array(
            'id',
            'endTime',
            'startTime',
            'period',
            'reporter',
            'macro',
            'compare',
        );
        foreach ($attrs as $key) {
            if($key == 'id') {
                $val = $this->reporter;
            }if (!empty($this->$key)) {
                $val = $this->$key;
            }
            if (!empty($val)) {
                if (is_array($val)) {
                    switch ($key) {
                        case 'compare':
                            $params[$key] = implode(',', $val);
                            break;
                    }
                } else if (is_object($val)) {
                    $params[$key] = $val->code;
                } else {
                    $params[$key] = $val;
                }
            }
        }

        $this->url = Yii::app()->createAbsoluteUrl('macro/index', $params);
    }

    /*
     * convert class vars to models
     * @return array
     */
    protected function setModels () {
        if (!empty($this->reporter)) {
            if (!is_array($this->reporter)) {
                $this->reporter = explode(',', $this->reporter);
            }
            if (!empty($this->reporter)) {
                $reporters = $this->reporter;
                $exists = $this->reporter = array();
                foreach ($reporters as $reporter) {
                    if (!in_array($reporter, $exists)) {
                        if (!is_object($reporter)) {
                            $reporter = DbReporters::model()->findByAttributes(array('ccode3' => $reporter));
                        }
                        if (!empty($reporter)) {
                            $exists[] = $reporter->ccode3;
                            $this->reporter[] = $reporter;
                        }
                    }
                }
            }
        }
    }

    protected function validatePremium () {
        $valid = true;

        if (!$this->init && empty($this->reporter)) {
            $valid = false;
            $this->error[] = 'Please select a country';
        }

        if (!$this->init && empty($this->macro)) {
            $valid = false;
            $this->error[] = 'Please select a macro';
        }

        if ($valid && in_array($this->macro, array('GCF')) && $this->period != 'annual') {
            $valid = false;
            $this->error[] = 'Gross Capital Formation is only available with annual data';
        }

        if ($valid && !empty($this->reporter)) {
            $temp = array();
            foreach ($this->reporter as $reporter) {
                $valid2 = true;
                if (empty($this->ignorePermissions) && !Yii::app()->user->checkToolAccess('tra', 'service-pro')) {
                    if($reporter->access == 1){
                        if(!Yii::app()->user->checkToolAccess('tra', 'service-ess')){
                            $valid2 = false;
                            $this->error[] = $reporter->country . ' is only available to Essentials accounts';
                        }
                    } else if($reporter->access == 2){
                        if(!Yii::app()->user->checkToolAccess('tra', 'service-pro')){
                            $valid2 = false;
                            $this->error[] = $reporter->country . ' is only available to Pro accounts';
                        }
                    }
                }

                if ($valid && !in_array('macro', $reporter->type)){
                    $valid = false;
                    $this->error[] = $this->reporter->country . ' is not available with trade data reports';
                }

                if ($valid2) {
                    $model = $this->fetchModel($reporter, $this->macro);
                    if(empty($model)) {
                        $this->basePeriod = $this->period;
                        foreach (array('month', 'quarter', 'annual') as $period) {
                            if($this->basePeriod != $period) {
                                $this->period = $period;
                                $model = $this->fetchModel($reporter, $this->macro);
                                if (!empty($model)) {
                                    break;
                                }
                            }
                        }
                    }


                    if (!empty($model)) {
                        $temp[] = $reporter;
                    } else {
                        $this->error[] = 'Sorry, there is not enough data available for ' . $reporter->country . ' / ' . ActiveRecordMacroData::model()->getListLabel('listMacro', $this->macro);
                    }

                    if (!empty($this->compare)) {
                        foreach ($this->compare as $compare) {
                            $model = $this->fetchModel($reporter, $compare);
                            if(empty($model)) {
                                $this->basePeriod = $this->period;
                                foreach (array('month', 'quarter', 'annual') as $period) {
                                    if($this->basePeriod != $period) {
                                        $this->period = $period;
                                        $model = $this->fetchModel($reporter, $compare);
                                        if (!empty($model)) {
                                            break;
                                        }
                                    }
                                }
                            }
                            if (empty($model)) {
                                $this->error[] = 'Sorry, there is not enough data available for ' . $reporter->country . ' / ' . ActiveRecordMacroData::model()->getListLabel('listMacro', $compare);
                            }
                        }
                    }

                    if(!empty($this->basePeriod)){
                        $this->setDefaults();
                    }
                }
            }

            if ($this->init) {
                if (empty($temp)) {
                    $valid = false;
                }
            } else {
                $this->reporter = $temp;
                if (empty($this->reporter)) {
                    $valid = false;
                }
            }
        }

        return $valid;
    }

    public function isFavorite () {
        $valid = false;
        $data = array();
        $attrs = DbUserFavorites::model()->listDataAttrs('macro');
        if (!empty($attrs)) {
            foreach ($attrs as $attr) {
                if (isset($this->$attr)) {
                    if (is_object($this->$attr)) {
                        if (get_class($this->$attr) == 'DbReporters' || get_class($this->$attr) == 'DbPartners') {
                            $data[$attr] = $this->$attr->ccode3;
                        } else if (get_class($this->$attr) == 'DbSectors') {
                            $data[$attr] = $this->$attr->code;
                        }
                    } else if (is_array($this->$attr)) {
                        $data[$attr] = array();
                        foreach ($this->$attr as $obj) {
                            if (is_object($obj)) {
                                if (get_class($obj) == 'DbReporters' || get_class($obj) == 'DbPartners') {
                                    $data[$attr][] = $obj->ccode3;
                                } else if (get_class($obj) == 'DbSectors') {
                                    $data[$attr][] = $obj->code;
                                }
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
            $valid = DbUserFavorites::model()->isAssigned($this->user->id, 'macro', $data);
        }

        return $valid;
    }
}