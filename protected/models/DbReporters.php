<?php

/**
 * This is the model class for table "db_reporters".
 *
 * The followings are the available columns in table 'db_reporters':
 * @property integer $id
 * @property string  $country
 * @property string  $ccode
 * @property string  $ccode3
 * @property string  $ncode3
 * @property string  $aka
 * @property string  $color
 * @property integer $searchDef
 * @property integer $access
 * @property string $type
 */
class DbReporters extends ActiveRecord {

    protected function afterConstruct () {
        parent::afterConstruct();
        $this->formatCommaSeparated('type', 'array');
    }

    protected function afterFind () {
        parent::afterFind();
        if(empty($this->color)){
            $this->color = '000000';
        }
        $this->formatCommaSeparated('type', 'array');
    }

    protected function beforeValidate () {
        $this->formatCommaSeparated('type', 'string');

        return parent::beforeValidate();
    }

    protected function afterValidate () {
        parent::afterValidate();
        $this->formatCommaSeparated('type', 'array');
    }

    protected function beforeSave () {
        $this->formatCommaSeparated('type', 'string');

        return parent::beforeSave();
    }

    protected function afterSave () {
        parent::afterSave();
        $this->formatCommaSeparated('type', 'array');
    }

    /**
     * @return string the associated database table name
     */
    public function tableName () {
        return 'db_reporters';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules () {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('searchDef, access', 'numerical', 'integerOnly' => true),
            array('aka', 'length', 'max' => 50),
            array('country', 'length', 'max' => 33),
            array('type', 'length', 'max' => 45),
            array('color', 'length', 'max' => 6),
            array('ccode', 'length', 'max' => 2),
            array('ccode3, ncode3', 'length', 'max' => 3),

            array('id, country, ccode, ccode3, ncode3, aka, color, searchDef, access', 'safe', 'on' => 'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations () {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
            'ttResults' => array(self::HAS_ONE, 'DbAllMonTtResults', array('RISO3' => 'ccode3')),
            'ttccResults' => array(self::HAS_ONE, 'DbAllMonTtccResults', array('RISO3' => 'ccode3')),
        );
    }

    public function scopes () {
        return array(
            'countryReport' => array(
                'condition' => 't.type LIKE "%,countryReport,%"',
            ),
            'macro' => array(
                'condition' => 't.type LIKE "%,macro,%"',
            ),
            'trade' => array(
                'condition' => 't.type LIKE "%,trade,%"',
            ),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels () {
        return array(
            'id' => 'ID',
            'country' => 'Country',
            'ccode' => 'Ccode',
            'ccode3' => 'Country Code',
            'ncode3' => 'Ncode3',
            'aka' => 'Also Known As',
            'color' => 'Colour',
            'searchDef' => 'Search Default',
            'access' => 'Access',
            'type' => 'Type',
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

        $criteria->compare('id', $this->id);
        $criteria->compare('country', $this->country, true);
        $criteria->compare('ccode', $this->ccode, true);
        $criteria->compare('ccode3', $this->ccode3, true);
        $criteria->compare('ncode3', $this->ncode3, true);
        $criteria->compare('aka', $this->aka, true);
        $criteria->compare('color', $this->color);
        $criteria->compare('searchDef', $this->searchDef);
        $criteria->compare('access', $this->access);
        $criteria->compare('type', $this->type);

        $options['sortDefault'] = 'country ASC, ccode3 ASC';

        return $this->formatSearch($this, $criteria, $options);
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return DbReporters the static model class
     */
    public static function model ($className = __CLASS__) {
        return parent::model($className);
    }

    public function getCode(){
        return $this->ccode3;
    }

    public function getName(){
        return $this->country;
    }
}
