<?php class ActiveRecordMarketData extends ActiveRecord {

    protected function afterFind () {
        parent::afterFind();
    }

    public static function model ($className = __CLASS__) {
        return parent::model($className);
    }

    public function getConfidence(){
        $rating = null;


        if(!empty($this->Correlations)) {
            $rating = 2;
            $useDefault = true;

            $config = DbVar::model()->findByAttributes(['name' => 'market-confidence']);
            if(!empty($config) && !empty($config->data) && !empty($config->data[$this->market])){
                $data = $config->data[$this->market];
                if(!empty($data['high']['limit']) && !empty($data['low']['limit'])){
                    $useDefault = false;
                    if($this->calcConfidence($data['high']['symbol'], $data['high']['limit'], $this->Correlations)){
                        $rating = 3;
                    } else if($this->calcConfidence($data['low']['symbol'], $data['low']['limit'], $this->Correlations)){
                        $rating = 1;
                    }
                }
            }

            if($useDefault){
                if ($this->Correlations >= 0.851) {
                    $rating = 3;
                } else if ($this->Correlations <= 0.7) {
                    $rating = 1;
                }
            }
        }

        return $rating;
    }

    private function calcConfidence($symbol, $limit, $data){
        $valid = false;
        switch($symbol){
            case '>':
                if($data > $limit){
                    $valid = true;
                }
                break;
            case '>=':
                if($data >= $limit){
                    $valid = true;
                }
                break;
            case '<':
                if($data <= $limit){
                    $valid = true;
                }
                break;
            case '<=':
                if($data <= $limit){
                    $valid = true;
                }
                break;
            case '=':
                if($data == $limit){
                    $valid = true;
                }
                break;
        }

        return $valid;
    }

    public function getTopCorrelations($orderCorr = true){
        $list = [];
        $hasNull = false;

        $criteria = new CDbCriteria();
        $criteria->compare('fromAsset', $this->Asset_ID);
        $criteria->compare('history', [0,3,6]);
        //$criteria->limit = 5;

        if($orderCorr){
            $criteria->order = 'history ASC, corr DESC';
        }else{
            $criteria->order = 'id ASC';
        }

        $models = DbCorrelation::model()->findAll($criteria);
        if(!empty($models)){
            foreach ($models as $model){
                if(empty($list[$model->history])){
                    $list[$model->history] = [];
                }
                if(count($list[$model->history]) < 5) {
                    $list[$model->history][$model->toAssetName] = $model->corr;
                }
                if(empty($model->corr)){
                    $hasNull = true;
                }
            }
        }

        if($hasNull && $orderCorr){
            $list = $this->getTopCorrelations(false);
        }

        return $list;
    }

    /*
     * separate trade data columns names
     * @param string $attr
     * @return array
     */
    public function listAttrParts($attr){
        return array(
            'year' => substr($attr, 1, 4),
            'month' => substr($attr, 5, 6),
            'quarter' => $this->getQuarter(substr($attr, 5, 6)),
        );
    }

    /*
     * filter the attributes to an array of just attributes that contain trade data
     * @param array $options - see $default
     * @return array
     */
    public function listData ($options = array()) {
        $list = array();

        $default = array(
            'year' => array(),
            'month' => array(),
            'quarter' => array(),
        );
        $options = Yii::app()->format->options($options,$default);
        $options['month'] = (is_array($options['month']) ? $options['month'] : array($options['month']));
        $options['year'] = (is_array($options['year']) ? $options['year'] : array($options['year']));
        $options['quarter'] = (is_array($options['quarter']) ? $options['quarter'] : array($options['quarter']));
        $options['period'] = (!empty($options['period']) ? $options['period'] : 'month');

        $years = $quarters = array();

        foreach ($this->attributes as $attr => $val) {
            $parts = $this->listAttrParts($attr);

            if (!empty($parts['year']) && is_numeric($parts['year']) && !empty($parts['month']) && is_numeric($parts['month'])) {
                $valid = true;

                if ($valid && !empty($options['year']) && !in_array($parts['year'], $options['year'])) {
                    $valid = false;
                }
                if ($valid && !empty($options['month']) && !in_array($parts['month'], $options['month'])) {
                    $valid = false;
                }
                if ($valid && !empty($options['start']) && $parts['year'] . $parts['month'] < $options['start']) {
                    $valid = false;
                }
                if ($valid && !empty($options['end']) && $parts['year'] . $parts['month'] > $options['end']) {
                    $valid = false;
                }
                if ($valid && !empty($options['quarter']) && !in_array($parts['quarter'],$options['quarter'])) {
                    $valid = false;
                }

                if($options['period'] == 'quarter'){
                    $attr = $parts['year'].$parts['quarter'];
                    if(!empty($val)) {
                        if (!empty($list[$attr])) {
                            $val = $list[$attr] + $val;
                            $valid = false;
                        }
                        if (empty($quarters[$attr])) {
                            $quarters[$attr] = 0;
                        }
                        $quarters[$attr]++;
                    }
                }

                if($options['period'] == 'annual'){
                    $attr = $parts['year'];
                    if(!empty($val)) {
                        if (!empty($list[$attr])) {
                            $val = $list[$attr] + $val;
                            $valid = false;
                        }

                        if (empty($years[$attr])) {
                            $years[$attr] = 0;
                        }
                        $years[$attr]++;
                    }
                }

                if ($valid) {
                    $list[$attr] = $val;
                }
            }
        }

        if(!empty($quarters)){
            foreach($quarters as $attr => $count){
                if($count > 0 && $count < 3){
                    $list[$attr] = null;
                }
            }
        }

        if(!empty($years)){
            foreach($years as $attr => $count){
                if($count > 0 && $count < 12){
                    $list[$attr] = null;
                }
            }
        }

        return $list;
    }

    public function listMarket () {
        return array(
            'commodity' => 'Commodity',
            'currency' => 'Currency',
            'equity' => 'Equity',
        );
    }

    public function listMarketPlural () {
        return array(
            'commodity' => 'Commodities',
            'currency' => 'Currencies',
            'equity' => 'Equities',
        );
    }

    public function listType () {
        return array(
            'High' => 'High',
            'Mid' => 'Base',
            'Low' => 'Low',
        );
    }

    public function listVariant () {
        return array(
            'Absolute' => 'Nominal Price',
            'Index' => 'Index points',
            'Percent' => '% Change',
            'USD' => 'USD',
            'UPL' => 'USD per Litre',
            'UPT' => 'USD per Tonne',
            'USD Per Tonne' => 'USD per Tonne',
        );
    }

}