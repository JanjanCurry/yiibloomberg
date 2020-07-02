<?php class ActiveRecordTradeData extends ActiveRecord {

    /*
     * run report for the the top 10 indicator
     * @param DbReporters $reporter
     * @param array $options
     * @return array
     */
    public function calcTop ($reporter, $options = array()) {
        $list = array();
        $default = array(
            'limit' => 10
        );
        $options = Yii::app()->format->options($options, $default);
        $options['period'] = 'month';
        $months = $this->listData($options);
        if (!empty($months)) {
            $sum = implode(' + ', array_keys($months));
        }
        if (!empty($sum)) {
            $criteria = new CDbCriteria();
            $criteria->select = '*, (' . $sum . ') AS total';
            $criteria->order = 'total DESC';
            $criteria->limit = $options['limit'];
            $criteria->compare('RISO3', $reporter->ccode3);


            if (!empty($options['reportType']) && $options['reportType'] == 'sector') {
                $criteria->join = 'INNER JOIN  db_sectors AS s ON t.Commodity_Code = s.code';
                $criteria->addCondition('s.id > 0');
            } else {
                $criteria->join = 'INNER JOIN  db_partners AS p ON t.PISO3 = p.ccode3';
                $criteria->addCondition('p.id > 0');
            }

            $models = $this->findAll($criteria);
            if (!empty($models)) {
                $list = $models;
            }
        }

        return $list;
    }

    public function getQuarter ($month) {
        $quarters = $this->listQuarterMonths();
        foreach ($quarters as $key => $val) {
            if (in_array($month, $val)) {
                return $key;
            }
        }
    }

    /*
     * separate trade data columns names
     * @param string $attr
     * @return array
     */
    public function listAttrParts ($attr) {
        return array(
            'type' => substr($attr, 0, 2),
            'year' => substr($attr, 2, 4),
            'month' => substr($attr, 6, 7),
            'quarter' => $this->getQuarter(substr($attr, 6, 7)),
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
            'type' => array(),
            'year' => array(),
            'month' => array(),
            'quarter' => array(),
        );
        $options = Yii::app()->format->options($options, $default);
        $options['type'] = (is_array($options['type']) ? $options['type'] : array($options['type']));
        $options['year'] = (is_array($options['year']) ? $options['year'] : array($options['year']));
        $options['month'] = (is_array($options['month']) ? $options['month'] : array($options['month']));
        $options['quarter'] = (is_array($options['quarter']) ? $options['quarter'] : array($options['quarter']));
        $options['period'] = (!empty($options['period']) ? $options['period'] : 'month');

        foreach ($this->attributes as $attr => $val) {
            $parts = $this->listAttrParts($attr);
            if (!empty($parts['type']) && in_array($parts['type'], array('EV', 'IV', 'TT'))) {
                $valid = true;

                if ($valid && !empty($options['type']) && !in_array($parts['type'], $options['type'])) {
                    $valid = false;
                }
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
                if ($valid && !empty($options['quarter']) && !in_array($parts['quarter'], $options['quarter'])) {
                    $valid = false;
                }

                if ($options['period'] == 'quarter') {
                    $attr = $parts['type'] . $parts['year'] . $parts['quarter'];
                    if (!empty($list[$attr])) {
                        $val = $list[$attr] + $val;
                    }
                }

                if ($options['period'] == 'annual') {
                    $attr = $parts['type'] . $parts['year'];
                    if (!empty($list[$attr])) {
                        $val = $list[$attr] + $val;
                    }
                }

                if ($valid) {
                    $list[$attr] = $val;
                }
            }
        }

        return $list;
    }

    public function listIndicator () {
        return array(
            'Top 10' => array(
                'top10-partner-ev' => 'Top 10 Export Partners',
                'top10-partner-iv' => 'Top 10 Import Partners',

                'top10-sector-ev' => 'Top 10 Export Commodities',
                'top10-sector-iv' => 'Top 10 Import Commodities',
            ),

            'By Partner' => array(
                'trade-partner-ev' => 'Total Exports by Partner',
                'trade-partner-iv' => 'Total Imports by Partner',
                'trade-partner-tt' => 'Total Trade by Partner',
            ),

            'By Commodities' => array(
                'trade-sector-ev' => 'Total Exports by Commodities',
                'trade-sector-iv' => 'Total Imports by Commodities',
                'trade-sector-tt' => 'Total Trade by Commodities',
            ),

            'Total Trade' => array(
                'trade-none-ev' => 'Total Exports',
                'trade-none-iv' => 'Total Imports ',
                'trade-none-tt' => 'Total Trade',
            ),

            'Trade Corridor' => array(
                'flow-partner-sector-iv' => 'Total Imports By Partners & Commodity',
                'flow-partner-sector-ev' => 'Total Exports By Partners & Commodity',

                'flow-partner-top10-iv' => 'Total Imports By Partner & Top 10 Commodities',
                'flow-partner-top10-ev' => 'Total Exports By Partner & Top 10 Commodities',

                'flow-sector-partner-iv' => 'Total Imports By Commodities & Partner',
                'flow-sector-partner-ev' => 'Total Exports By Commodities & Partner',

                'flow-sector-top10-iv' => 'Total Imports By Commodity & Top 10 Partners',
                'flow-sector-top10-ev' => 'Total Exports By Commodity & Top 10 Partners',
            ),
        );
    }

    public function listIndicatorGroup () {
        return array(
            'top10-partner-ev' => 'ttp',
            'top10-partner-iv' => 'ttp',
            'top10-partner-ev' => 'ttcc',
            'top10-partner-iv' => 'ttcc',

            'trade-partner-ev' => 'ttp',
            'trade-partner-iv' => 'ttp',
            'trade-partner-tt' => 'ttp',

            'trade-sector-ev' => 'ttcc',
            'trade-sector-iv' => 'ttcc',
            'trade-sector-tt' => 'ttcc',

            'trade-none-ev' => 'tt',
            'trade-none-iv' => 'tt',
            'trade-none-tt' => 'tt',

            'flow-partner-sector-iv' => 'ttf-partner',
            'flow-partner-sector-ev' => 'ttf-partner',

            'flow-partner-top10-iv' => 'ttf-partner',
            'flow-partner-top10-ev' => 'ttf-partner',

            'flow-sector-partner-iv' => 'ttf-sector',
            'flow-sector-partner-ev' => 'ttf-sector',

            'flow-sector-top10-iv' => 'ttf-sector',
            'flow-sector-top10-ev' => 'ttf-sector',
        );
    }

    public function listQuarterMonths () {
        return array(
            'Q1' => array('01', '02', '03'),
            'Q2' => array('04', '05', '06'),
            'Q3' => array('07', '08', '09'),
            'Q4' => array('10', '11', '12'),
        );
    }

    public function listType () {
        return array(
            'EV' => 'Exports',
            'IV' => 'Imports',
            'TT' => 'Total Trade',
        );
    }

    public static function model ($className = __CLASS__) {
        return parent::model($className);
    }

}