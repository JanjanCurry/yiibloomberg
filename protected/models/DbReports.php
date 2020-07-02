<?php

/**
 * This is the model class for table "db_reports".
 *
 * The followings are the available columns in table 'db_reports':
 * @property integer $id
 * @property string  $group
 * @property string  $type
 * @property string  $code
 * @property string  $partner
 * @property integer $hasData
 * @property integer $access
 */
class DbReports extends ActiveRecord {

    protected function beforeValidate () {
        $this->setDefaults();

        return parent::beforeValidate();
    }

    /**
     * @return string the associated database table name
     */
    public function tableName () {
        return 'db_reports';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules () {
        return array(
            array('group, type, code', 'required'),
            array('hasData, access', 'numerical', 'integerOnly' => true),
            array('group, type, code, partner', 'length', 'max' => 20),
            array('id, group, type, code, partner, hasData, access', 'safe', 'on' => 'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations () {
        return array();
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels () {
        return array(
            'id' => 'ID',
            'group' => 'Group',
            'type' => 'Type',
            'code' => 'Code',
            'partner' => 'Partner',
            'hasData' => 'Has Data',
            'access' => 'Access',
        );
    }

    /**
     * @param array $options formatSearch() options
     * @return CActiveDataProvider
     */
    public function search ($options = null) {
        $criteria = new CDbCriteria;

        $criteria->compare('t.id', $this->id);
        $criteria->compare('t.group', $this->group);
        $criteria->compare('t.type', $this->type);
        $criteria->compare('t.code', $this->code);
        $criteria->compare('t.partner', $this->partner);
        $criteria->compare('t.hasData', $this->hasData);
        $criteria->compare('t.access', $this->access);

        $options['sortDefault'] = 't.group ASC, t.type ASC, t.code ASC, t.partner ASC';

        $searchAttrs = array();
        $criteria = $this->addSearchTerms($criteria, $searchAttrs);

        return $this->formatSearch($this, $criteria, $options);
    }

    /**
     * @param string $className active record class name.
     * @return DbReports the static model class
     */
    public static function model ($className = __CLASS__) {
        return parent::model($className);
    }

    public static function add ($attrs) {
        $valid = false;
        $model = new DbReports();
        $model->attributes = $attrs;
        if ($model->validate()) {
            $criteria = [
                'group' => $model->group,
                'type' => $model->type,
                'code' => $model->code,
            ];
            if (!empty($attrs['partner'])) {
                $criteria['partner'] = $attrs['partner'];
            }
            $exists = DbReports::model()->findByAttributes($criteria);
            if (!empty($exists)) {
                $exists->attributes = $model->attributes;
                $model = $exists;
            }

            $valid = $model->save();
        }


        return ($valid ? $model : false);
    }

    public function calcData ($opts) {
        $return = [
            'type' => '',

            'hide' => [],
            'list' => [],
            'valid' => false,
        ];
        $return = Yii::app()->format->options($opts, $return);

        switch ($opts['type']) {
            case 'commodity':
            case 'currency':
            case 'equity':
                $return['valid'] = true;
                $return = $this->calcMarket($return);
                break;

            case 'macro':
                $return['valid'] = true;
                $return = $this->calcMacro($return);
                break;

            case 'countryReport':
                $return['valid'] = true;
                $return = $this->calcCountryReport($return);
                break;

            case 'reporter':
            case 'reporter-trade':
                $return['valid'] = true;
                $return = $this->calcReporter($return);
                break;

            case 'reporter-macro':
                $return['valid'] = true;
                $return = $this->calcReporter($return);
                break;

            case 'partner':
            case 'sector':
            case 'partner-flow':
            case 'sector-flow':
            case 'trade':
                $return['valid'] = true;
                $return = $this->calcTrade($return);
                break;
        }

        if (!empty($return['filter'])) {
            foreach ($return['filter'] as $removeItem) {
                $filter = array_filter($return['list'], function ($item) use ($removeItem) {
                    if (stripos($item, $removeItem) === false) {
                        return true;
                    }

                    return false;
                });

                foreach ($filter as $removeItem) {
                    //$return['restrict'][] = $removeItem;
                    $return['hide'][] = $removeItem;
                }

            }
        }

        if (!empty($return['restrict'])) {
            foreach ($return['restrict'] as $removeItem) {
                if (($key = array_search($removeItem, $return['list'])) !== false) {
                    unset($return['list'][$key]);
                }
            }
            $return['list'] = array_values($return['list']);
        }


        return $return;
    }

    private function calcCountryReport ($opts) {
        $return = [
            'reporter' => '',
            'type' => '',
            'period' => '',
            'reports' => [],

            'hide' => [],
            'list' => [],
            'valid' => true,
        ];
        $return = Yii::app()->format->options($opts, $return);

        $list = [];
        $hasTrade = $hasMacro = false;
        $i = 0;

        if (!empty($return['reports'])) {
            foreach (ActiveRecordTradeData::model()->listIndicator() as $groups) {
                foreach ($return['reports'] as $report) {
                    if (array_key_exists($report, $groups)) {
                        $hasTrade = true;
                        $i++;
                    }
                }
            }
            if ($i < count($return['reports'])) {
                $hasMacro = true;
                $macros = DbMacroList::model()->findAllByAttributes(array('assetId' => $return['reports']));
                if (!empty($macros)) {
                    $temp = [];
                    foreach ($macros as $macro) {
                        $temp[] = $macro->assetId;
                    }
                    $macros = $temp;
                }
            }
        }

        $criteria = new CDbCriteria();
        $criteria->compare('type', ',countryReport,', true);
        if ($hasTrade) {
            $criteria->compare('type', ',trade,', true);
        }
        if ($hasMacro) {
            $criteria->compare('type', ',macro,', true);
        }
        $reporters = DbReporters::model()->findAll();
        if (!empty($reporters)) {
            foreach ($reporters as $reporter) {
                $valid = true;
                if ($reporter->searchDef != 1 && !Yii::app()->user->checkToolAccess('tra', 'country-full')) {
                    $valid = false;
                }

                if ($valid && !empty($macros)) {
                    $criteria = new CDbCriteria();
                    $criteria->compare('t.group', 'macro');
                    $criteria->compare('t.type', $return['period']);
                    $criteria->compare('t.code', $reporter->code);
                    $criteria->compare('t.partner', $macros);
                    $models = DbReports::model()->findAll($criteria);
                    if (!empty($models)) {
                        foreach ($models as $model) {
                            if (empty($model->hasData)) {
                                $valid = false;
                                if (!in_array($reporter->code, $return['hide'])) {
                                    $return['hide'][] = $reporter->code;
                                }
                            }
                        }
                    } else {
                        $valid = false;
                        if (!in_array($reporter->code, $return['hide'])) {
                            $return['hide'][] = $reporter->code;
                        }
                    }
                }

                if ($valid) {
                    $return['list'][] = $reporter->code;
                }
            }
        }


        return $return;
    }

    private function calcMacro ($opts) {
        $return = [
            'macro' => '',
            'period' => '',
            'reporter' => '',
            'type' => '',
            'variant' => '',

            'strictHide' => false,

            'hide' => [],
            'list' => [],
            'valid' => true,
        ];
        $return = Yii::app()->format->options($opts, $return);

        $criteria = new CDbCriteria();
        $criteria->compare('t.group', 'macro');
        $criteria->compare('t.type', $return['period']);
        $criteria->compare('t.partner', $return['macro']);

        if(!empty($return['reporter'])){
            if(!is_array($return['reporter'])){
                $return['reporter'] = array($return['reporter']);
            }
            foreach($return['reporter'] as $key => $val){
                if (strpos($val, ',') !== false) {
                    $val = explode(',', $val);
                }
                $criteria->compare('t.code', $val);
            }
        }

        $criteria->select = 'code, partner, hasData, access';
        if (empty($return['period'])) {
            $criteria->order = 'code ASC, hasData DESC';
            $criteria->group = 'code, partner';
        }

        $models = DbReports::model()->findAll($criteria);
        if (!empty($models)) {
            $list = [];
            foreach ($models as $model) {
                $code = $model->code;
                if (!empty($return['variant']) || !empty($return['returnPartner'])) {
                    $code = $model->partner;
                }
                $valid = false;
                if (Yii::app()->user->checkToolAccess('tra', 'service-pro')) {
                    $valid = true;
                } else if (Yii::app()->user->checkToolAccess('tra', 'service-ess') && $model->access <= 1) {
                    $valid = true;
                } else if ($model->access == 0) {
                    $valid = true;
                }
                if ($valid && !in_array($code, $list)) {
                    $list[] = $code;
                }

                if (empty($model->hasData) && !in_array($code, $return['hide'])) {
                    $return['hide'][] = $code;
                } else if (!empty($model->hasData) && in_array($code, $return['hide']) && !$return['strictHide']) {
                    if (($key = array_search($code, $return['hide'])) !== false) {
                        unset($return['hide'][$key]);
                    }
                }
            }

            if (!empty($list)) {
                if (!empty($return['variant'])) {
                    $macros = DbMacroList::model()->findAllByAttributes(array(
                        'assetId' => $list,
                        'variant' => $return['variant'],
                    ));
                    if (!empty($macros)) {
                        foreach ($macros as $macro) {
                            $return['list'][] = $macro->assetId;
                        }
                    }
                } else {
                    $return['list'] = $list;
                }
            }
        }

        return $return;
    }


    private function calcMarket ($opts) {
        $return = [
            'type' => '',

            'hide' => [],
            'list' => [],
            'valid' => true,
        ];
        $return = Yii::app()->format->options($opts, $return);

        $criteria = new CDbCriteria();
        $criteria->compare('t.group', $return['type']);
        $criteria->compare('t.type', 'asset');
        $criteria->select = 'code, hasData, access';

        $models = DbReports::model()->findAll($criteria);
        if (!empty($models)) {
            $tool = DbUserService::convertTool($return['type']);
            foreach ($models as $model) {
                if (Yii::app()->user->checkToolAccess($tool, 'service-pro')) {
                    $return['list'][] = $model->code;
                } else if (Yii::app()->user->checkToolAccess($tool, 'service-ess') && $model->access <= 1) {
                    $return['list'][] = $model->code;
                } else if ($model->access == 0) {
                    $return['list'][] = $model->code;
                }
                if (empty($model->hasData)) {
                    $return['hide'][] = $model->code;
                }
            }
        }

        return $return;
    }

    private function calcReporter ($opts) {
        $return = [
            'type' => '',

            'hide' => [],
            'list' => [],
            'valid' => true,
        ];
        $return = Yii::app()->format->options($opts, $return);

        $criteria = new CDbCriteria();
        $criteria->compare('t.group', 'account');
        $criteria->compare('t.type', 'asset');
        $criteria->select = 'code, hasData, access';

        $models = DbReports::model()->findAll($criteria);
        if (!empty($models)) {
            foreach ($models as $model) {
                if (Yii::app()->user->checkToolAccess('tra', 'country-full')) {
                    $return['list'][] = $model->code;
                } else if (Yii::app()->user->checkToolAccess('tra', 'service-ess') && $model->access <= 1) {
                    $return['list'][] = $model->code;
                } else if ($model->access == 0) {
                    $return['list'][] = $model->code;
                }
            }
        }

        return $return;
    }

    private function calcTrade ($opts) {
        $return = [
            'reporter' => '',
            'type' => '',

            'hide' => [],
            'list' => [],
            'valid' => true,
        ];
        $return = Yii::app()->format->options($opts, $return);

        $criteria = new CDbCriteria();
        $criteria->select = 'code, partner, hasData, access';
        $criteria->compare('t.group', 'trade');
        $criteria->compare('t.code', $return['reporter']);

        if (!empty($return['indicator'])) {
            $indicators = ActiveRecordTradeData::model()->listIndicatorGroup();
            if (array_key_exists($return['indicator'], $indicators)) {
                $return['type'] = $indicators[$return['indicator']];
            }
        }

        switch ($return['type']) {
            case 'partner':
            case 'ttp':
                $criteria->compare('t.type', 'ttp');
                break;
            case 'sector':
            case 'ttcc':
                $criteria->compare('t.type', 'ttcc');
                break;
            case 'partner-flow':
            case 'ttf-partner':
                $criteria->compare('t.type', 'ttf-partner');
                break;
            case 'sector-flow':
            case 'ttf-sector':
                $criteria->compare('t.type', 'ttf-sector');
                break;
            case 'trade':
            case 'tt':
                $criteria->compare('t.type', 'tt');
                break;
        }

        $models = DbReports::model()->findAll($criteria);
        if (!empty($models)) {
            $list = [];
            foreach ($models as $model) {
                $code = $model->partner;
                if ($return['type'] == 'trade' || empty($return['reporter'])) {
                    $code = $model->code;
                }
                $valid = false;
                if (Yii::app()->user->checkToolAccess('tra', 'service-pro')) {
                    $valid = true;
                } else if (Yii::app()->user->checkToolAccess('tra', 'service-ess') && $model->access <= 1) {
                    $valid = true;
                } else if ($model->access == 0) {
                    $valid = true;
                }
                if ($valid && !empty($model->hasData) && !in_array($code, $return['list'])) {
                    $return['list'][] = $code;
                }

                if (empty($model->hasData) && !in_array($code, $return['hide']) && !in_array($code, $return['list'])) {
                    $return['hide'][] = $code;
                } else if (!empty($model->hasData) && in_array($code, $return['hide'])) {
                    if (($key = array_search($code, $return['hide'])) !== false) {
                        unset($return['hide'][$key]);
                    }
                }
            }
        }

        return $return;
    }

    public function setDefaults () {
        if (empty($this->hasData)) {
            $this->hasData = 0;
        }
        if (empty($this->access)) {
            $this->hasData = 0;
        }
    }
}
