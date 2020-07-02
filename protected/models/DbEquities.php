<?php

/**
 * This is the model class for table "db_equities".
 *
 * The followings are the available columns in table 'db_equities':
 * @property integer $id
 * @property string  $code
 * @property string  $name
 * @property string  $aka
 * @property integer  $access
 * @property integer $searchDef
 * @property integer $companyId
 */
class DbEquities extends ActiveRecord {
    /**
     * @return string the associated database table name
     */
    public function tableName () {
        return 'db_equities';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules () {
        return array(
            array('code, name', 'required'),
            array('access, searchDef, companyId', 'integerOnly' => true),
            array('code', 'length', 'max' => 45),
            array('name, aka', 'length', 'max' => 255),
            array('id, code, name, aka, searchDef', 'safe', 'on' => 'search'),
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
            'code' => 'Code',
            'name' => 'Name',
            'aka' => 'Aka',
            'access' => 'Access Level',
            'searchDef' => 'Search Default',
            'companyId' => 'Company ID',
        );
    }

    /**
     * @param array $options formatSearch() options
     * @return CActiveDataProvider
     */
    public function search ($options = null) {
        $criteria = new CDbCriteria;

        $criteria->compare('t.id', $this->id);
        $criteria->compare('t.code', $this->code, true);
        $criteria->compare('t.name', $this->name, true);
        $criteria->compare('t.aka', $this->aka, true);
        $criteria->compare('t.access', $this->access);
        $criteria->compare('t.searchDef', $this->searchDef);
        $criteria->compare('t.companyId', $this->companyId);

        $options['sortDefault'] = 't.id ASC';

        $searchAttrs = array();
        $criteria = $this->addSearchTerms($criteria, $searchAttrs);

        return $this->formatSearch($this, $criteria, $options);
    }

    /**
     * @param string $className active record class name.
     * @return DbEquities the static model class
     */
    public static function model ($className = __CLASS__) {
        return parent::model($className);
    }

    public function getMarket(){
        return 'equity';
    }
}
