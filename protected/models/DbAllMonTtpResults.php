<?php

/**
 * This is the model class for table "all_mon_ttp_results".
 *
 * The followings are the available columns in table 'all_mon_ttp_results':
 * @property integer $SLNO
 * @property integer $RINO3
 * @property string  $RISO3
 * @property integer $PINO3
 * @property string  $PISO3
 */
class DbAllMonTtpResults extends ActiveRecordTradeData {
    /**
     * @return string the associated database table name
     */
    public function tableName () {
        return 'ALL_MON_TTP_RESULTS';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules () {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('RINO3, RISO3', 'required'),
            array('RINO3, PINO3', 'numerical', 'integerOnly' => true),
            array('RISO3, PISO3', 'length', 'max' => 3),
            // The following rule is used by search().
            array('SLNO, RINO3, RISO3, PINO3, PISO3', 'safe', 'on' => 'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations () {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
            'reporter' => array(self::BELONGS_TO, 'DbReporters', array('RISO3' => 'ccode3')),
            'partner' => array(self::BELONGS_TO, 'DbPartners', array('PISO3' => 'ccode3')),
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
            'PINO3' => 'Pino3',
            'PISO3' => 'Piso3',
        );
    }

    /**
     * Retrieves a list of models based on the current search/filter conditions.
     *
     * Typical usecase:
     * - Initialize the model fields with values from filter form.
     * - Execute this method to get CActiveDataProvider instance which will filter
     * models according to data in model fields.
     * - Pass data provider to CGridView, CListView or any similar widget.
     *
     * @return CActiveDataProvider the data provider that can return the models
     * based on the search/filter conditions.
     */
    public function search ($options = null) {
        $criteria = new CDbCriteria;

        $criteria->compare('SLNO', $this->SLNO);
        $criteria->compare('RINO3', $this->RINO3);
        $criteria->compare('RISO3', $this->RISO3, true);
        $criteria->compare('PINO3', $this->PINO3);
        $criteria->compare('PISO3', $this->PISO3, true);

        return $this->formatSearch($this, $criteria, $options);
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return AllMonTtpResults the static model class
     */
    public static function model ($className = __CLASS__) {
        return parent::model($className);
    }

    public function getMerged(){
        $valid = false;
        $merged = new DbAllMonTtpResults();
        $merged->RISO3 = $this->RISO3;
        $merged->PISO3 = $this->PISO3;

        $childIds = [];
        if(!empty($this->partner->children)){
            foreach ($this->partner->children as $child){
                $childIds[] = $child->code;
            }
        }

        if(!empty($childIds)){
            $criteria = new CDbCriteria();
            $criteria->compare('RISO3', $merged->RISO3);
            $criteria->compare('PISO3', $childIds);

            $select = [];
            foreach($this->attributes as $key => $val){
                $attr = str_replace(['EV', 'IV', 'TT'], '', $key);
                if(is_numeric($attr)){
                    $select[] = 'SUM('.$key.') as '.$key;
                }else{
                    $select[] = $key;
                }
            }
            $criteria->select = implode(', ', $select);

            $models = DbAllMonTtpResults::model()->findAll($criteria);
            if(!empty($models)){
                $valid = true;
                foreach ($models as $model){
                    $data = $model->listData();
                    foreach ($data as $key => $val){
                        if(empty($merged->$key)){
                            $merged->$key = 0;
                        }
                        $merged->$key += $val;
                    }
                }
            }
        }

        return ($valid ? $merged : false);
    }

    public function sampleData () {
        $reporters = DbReporters::model()->findAll();
        foreach ($reporters as $reporter) {
            $exists = DbAllMonTtpResults::model()->findByAttributes(array(
                'RISO3' => $reporter->ccode3,
                'PISO3' => 'WWW',
            ));
            if(empty($exists)) {
                $model = new DbAllMonTtpResults();
                $model->RINO3 = $reporter->ncode3;
                $model->RISO3 = $reporter->ccode3;
                $model->PINO3 = 0;
                $model->PISO3 = 'WWW';
                for ($y = 2010; $y < 2020; $y++) {
                    for ($m = 1; $m <= 12; $m++) {
                        $month = ($m < 10 ? '0' . $m : $m);
                        foreach (array('EV', 'IV', 'TT') as $type) {
                            $attr = $type . $y . $month;
                            $model->$attr = rand(1000000, 10000000);
                        }
                    }
                }
                $model->save();
            }

            foreach ($reporters as $partner) {
                if($reporter->ccode3 != $partner->ccode3) {
                    $exists = DbAllMonTtpResults::model()->findByAttributes(array(
                        'RISO3' => $reporter->ccode3,
                        'PISO3' => $partner->ccode3,
                    ));
                    if (empty($exists)) {
                        $model = new DbAllMonTtpResults();
                        $model->RINO3 = $reporter->ncode3;
                        $model->RISO3 = $reporter->ccode3;
                        $model->PINO3 = $partner->ncode3;
                        $model->PISO3 = $partner->ccode3;
                        for ($y = 2010; $y < 2020; $y++) {
                            for ($m = 1; $m <= 12; $m++) {
                                $month = ($m < 10 ? '0' . $m : $m);
                                foreach (array('EV', 'IV', 'TT') as $type) {
                                    $attr = $type . $y . $month;
                                    $model->$attr = rand(1000000, 10000000);
                                }
                            }
                        }
                        $model->save();
                    }
                }
            }
        }
    }
}
