<?php

/**
 * This is the model class for table "db_partner_group".
 *
 * The followings are the available columns in table 'db_partner_group':
 * @property integer $id
 * @property integer $parentId
 * @property integer $childId
 */
class DbPartnerGroup extends ActiveRecord {
    /**
     * @return string the associated database table name
     */
    public function tableName () {
        return 'db_partner_group';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules () {
        return array(
            array('parentId, childId', 'required'),
            array('parentId, childId', 'numerical', 'integerOnly' => true),
            array('parentId, childId', 'customValidation'),
            array('id, parentId, childId', 'safe', 'on' => 'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations () {
        return array(
            'parent' => array(self::HAS_ONE, 'DbPartners', array('id' => 'parentId')),
            'child' => array(self::HAS_ONE, 'DbPartners', array('id' => 'childId')),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels () {
        return array(
            'id' => 'ID',
            'parentId' => 'Parent',
            'childId' => 'Child',
        );
    }

    /**
     * @param array $options formatSearch() options
     * @return CActiveDataProvider
     */
    public function search ($options = null) {
        $criteria = new CDbCriteria;

        $criteria->compare('t.id', $this->id);
        $criteria->compare('t.parentId', $this->parentId);
        $criteria->compare('t.childId', $this->childId);

        $options['sortDefault'] = 't.id ASC';

        $searchAttrs = array();
        $criteria = $this->addSearchTerms($criteria, $searchAttrs);

        return $this->formatSearch($this, $criteria, $options);
    }

    /**
     * @param string $className active record class name.
     * @return DbPartnerGroup the static model class
     */
    public static function model ($className = __CLASS__) {
        return parent::model($className);
    }

    public function customValidation(){
        if($this->childId == $this->parentId){
            $this->addError('childId', 'Partner can\'t be a child of itself');
        }
    }
}
