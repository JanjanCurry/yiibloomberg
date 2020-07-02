<?php

/**
 * This is the model class for table "all_mon_ttf_results".
 *
 * The followings are the available columns in table 'ALL_MON_TTF_RESULTS':
 * @property integer $SLNO
 * @property integer $RINO3
 * @property string  $RISO3
 * @property string  $TF
 * @property string  $Commodity_Code
 * @property integer $PINO3
 * @property string  $PISO3
 */
class DbAllMonTTfResults extends ActiveRecordTradeData {
    /**
     * @return string the associated database table name
     */
    public function tableName () {
        return 'ALL_MON_TTF_RESULTS';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules () {
        return array(
            array('RINO3, RISO3, TF, Commodity_Code, PINO3, PISO3', 'required'),
            array('RINO3, PINO3', 'numerical', 'integerOnly' => true),
            array('RISO3, PISO3', 'length', 'max' => 3),
            array('TF', 'length', 'max' => 2),
            array('Commodity_Code', 'length', 'max' => 4),
            array('SLNO, RINO3, RISO3, TF, Commodity_Code, PINO3, PISO3', 'safe', 'on' => 'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations () {
        return array(
            'reporter' => array(self::BELONGS_TO, 'DbReporters', array('RISO3' => 'ccode3')),
            'partner' => array(self::BELONGS_TO, 'DbPartners', array('PISO3' => 'ccode3')),
            'sector' => array(self::BELONGS_TO, 'DbSectors', array('Commodity_Code' => 'code')),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels () {
        return array(
            'SLNO' => 'Slno',
            'RINO3' => 'Rino3',
            'RISO3' => 'Riso3',
            'TF' => 'Tf',
            'Commodity_Code' => 'Commodity Code',
            'PINO3' => 'Pino3',
            'PISO3' => 'Piso3',
        );
    }

    /**
     * @param array $options formatSearch() options
     * @return CActiveDataProvider
     */
    public function search ($options = null) {
        $criteria = new CDbCriteria;

        $criteria->compare('t.SLNO', $this->SLNO);
        $criteria->compare('t.RINO3', $this->RINO3);
        $criteria->compare('t.RISO3', $this->RISO3, true);
        $criteria->compare('t.TF', $this->TF, true);
        $criteria->compare('t.Commodity_Code', $this->Commodity_Code, true);
        $criteria->compare('t.PINO3', $this->PINO3);
        $criteria->compare('t.PISO3', $this->PISO3, true);

        $options['sortDefault'] = 't.SLNO ASC';

        $searchAttrs = array();
        $criteria = $this->addSearchTerms($criteria, $searchAttrs);

        return $this->formatSearch($this, $criteria, $options);
    }

    /**
     * @param string $className active record class name.
     * @return DbAllMonTtfResults the static model class
     */
    public static function model ($className = __CLASS__) {
        return parent::model($className);
    }

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

        $months = $this->listData($options);
        if (!empty($months)) {
            $sum = implode(' + ', array_keys($months));
        }
        if (!empty($sum)) {
            $criteria = new CDbCriteria();
            $criteria->select = '*, (' . $sum . ') AS total';
            $criteria->order = 'total DESC';
            $criteria->limit = $options['limit'];
            $criteria->compare('t.RISO3', $reporter->ccode3);
            $criteria->compare('t.TF', $options['type']);

            if (!empty($options['reportType']) && $options['reportType'] == 'sector') {
                $criteria->compare('t.Commodity_Code', $options['sector']);
                $criteria->join = 'INNER JOIN  db_sectors AS s ON t.Commodity_Code = s.code';
                $criteria->addCondition('s.id > 0');

                $models = $this->findAll($criteria);
            } else {
                $partners = [];
                if (!empty($options['partner'])) {
                    foreach ($options['partner'] as $val) {
                        $partner = DbPartners::model()->findByAttributes(['ccode3' => $val]);
                        if (!empty($partner)) {
                            if (!empty($partner->children)) {
                                foreach ($partner->children as $child) {
                                    $partners[] = '"' . $child->code . '"';
                                }
                            } else {
                                $partners[] = '"' . $partner->code . '"';
                            }
                        }
                    }
                }

                //                $criteria->compare('t.PISO3', $partners);
                //                $criteria->join = 'INNER JOIN  db_partners AS p ON t.PISO3 = p.ccode3';
                //                $criteria->addCondition('p.id > 0');

                $select = [];
                foreach ($this->attributes as $key => $val) {
                    $attr = str_replace('MA', '', $key);
                    if (is_numeric($attr)) {
                        $select[] = 'SUM(' . $key . ') as ' . $key;
                    } else {
                        $select[] = $key;
                    }
                }
                //$select[] = '(' . $sum . ') AS total';
                //$criteria->select = implode(', ', $select);
                //$criteria->group = 'Commodity_Code';

                if (!in_array($options['type'], ['IV', 'EV'])) {
                    exit;
                }

                $sub = '(
                SELECT ' . implode(', ', $select) . '  
                FROM ' . $this->tableName() . ' 
                WHERE RISO3 = "' . $reporter->ccode3 . '" AND TF = "' . $options['type'] . '" AND PISO3 IN(' . implode(',', $partners) . ')
                GROUP BY Commodity_Code
                ) AS subQuery';

                $sql = 'SELECT *, (' . $sum . ') AS total FROM ' . $sub . ' ORDER BY total DESC LIMIT ' . $options['limit'];

                $models = $this->findAllBySql($sql);
            }

            //$models = $this->findAll($criteria);
            if (!empty($models)) {
                if ($options['reportType'] == 'partner') {
                    foreach ($models as $i => $model) {
                        $models[$i]->PISO3 = $options['partner'][0];
                    }
                }
                $list = $models;
            }
        }
        //Yii::app()->format->debug($options);
        //Yii::app()->format->debug($criteria, true);

        return $list;
    }

    public function getMerged () {
        $valid = false;
        $merged = new DbAllMonTTfResults();
        $merged->TF = $this->TF;
        $merged->RISO3 = $this->RISO3;
        $merged->PISO3 = $this->PISO3;
        $merged->Commodity_Code = $this->Commodity_Code;

        $childIds = [];
        if (!empty($this->partner->children)) {
            foreach ($this->partner->children as $child) {
                $childIds[] = $child->code;
            }
        }

        if (!empty($childIds)) {
            $criteria = new CDbCriteria();
            $criteria->compare('TF', $merged->TF);
            $criteria->compare('RISO3', $merged->RISO3);
            $criteria->compare('PISO3', $childIds);
            $criteria->compare('Commodity_Code', $merged->Commodity_Code);

            $select = [];
            foreach ($this->attributes as $key => $val) {
                $attr = str_replace('MA', '', $key);
                if (is_numeric($attr)) {
                    $select[] = 'SUM(' . $key . ') as ' . $key;
                } else {
                    $select[] = $key;
                }
            }
            $criteria->select = implode(', ', $select);

            //Yii::app()->format->debug($criteria, true);
            $models = DbAllMonTTfResults::model()->findAll($criteria);
            //Yii::app()->format->debug($models, true);
            if (!empty($models)) {
                $valid = true;
                foreach ($models as $model) {
                    $data = $model->listData();
                    foreach ($data as $key => $val) {
                        if (empty($merged->$key)) {
                            $merged->$key = 0;
                        }
                        $merged->$key += $val;
                    }
                }
            }
        }

        return ($valid ? $merged : false);
    }

    /*
     * separate trade data columns names
     * @param string $attr
     * @return array
     */
    public function listAttrParts ($attr) {
        if (strpos($attr, 'MA') !== false) {
            return array(
                'type' => $this->TF,
                'year' => substr($attr, 2, 4),
                'month' => substr($attr, 6, 7),
                'quarter' => $this->getQuarter(substr($attr, 6, 7)),
            );
        }
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

            if (strpos($attr, 'MA') !== false && empty($parts['type']) && !empty($options['type'])) {
                $parts['type'] = $options['type'][0];
            }

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
}
