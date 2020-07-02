<?php

/**
 * This is the model class for table "all_mon_tt_results".
 *
 * The followings are the available columns in table 'all_mon_tt_results':
 * @property integer $SLNO
 * @property string  $RINO3
 * @property string  $RISO3
 */
class DbAllMonTtResults extends ActiveRecordTradeData {
    /**
     * @return string the associated database table name
     */
    public function tableName () {
        return 'ALL_MON_TT_RESULTS';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules () {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('RINO3, RISO3', 'required'),
            array('RINO3', 'length', 'max' => 6),
            array('RISO3', 'length', 'max' => 3),
            // The following rule is used by search().
            array('SLNO, RINO3, RISO3', 'safe', 'on' => 'search'),
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

        return $this->formatSearch($this, $criteria, $options);
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return AllMonTtResults the static model class
     */
    public static function model ($className = __CLASS__) {
        return parent::model($className);
    }

    public function sampleData () {
        $reporters = DbReporters::model()->findAll();
        foreach ($reporters as $reporter) {
            $exists = DbAllMonTtResults::model()->findByAttributes(array(
                'RISO3' => $reporter->ccode3,
            ));
            if(empty($exists)) {
                $model = new DbAllMonTtResults();
                $model->RINO3 = $reporter->ncode3;
                $model->RISO3 = $reporter->ccode3;
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
