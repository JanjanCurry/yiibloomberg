<?php

/**
 * This is the model class for table "db_partners".
 *
 * The followings are the available columns in table 'db_partners':
 * @property integer $id
 * @property integer $ncode3
 * @property string  $ccode3
 * @property string  $country
 * @property string  $aka
 * @property string  $color
 * @property integer $searchDef
 *
 * @property string $code
 * @property string $name
 *
 * @property DbPartners[] $children
 * @property DbPartners[] $parents
 * @property DbAllMonTtpResults[] $ttpResults
 */
class DbPartners extends ActiveRecord {

    protected function afterFind () {
        parent::afterFind();
        if (empty($this->color)) {
            $this->color = '000000';
        }
    }

    /**
     * @return string the associated database table name
     */
    public function tableName () {
        return 'db_partners';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules () {
        return array(
            array('ncode3, searchDef', 'numerical', 'integerOnly' => true),
            array('ccode3', 'length', 'max' => 3),
            array('country', 'length', 'max' => 255),
            array('aka', 'length', 'max' => 50),
            array('color', 'length', 'max' => 6),
            array('id, ncode3, ccode3, country, aka, color, searchDef', 'safe', 'on' => 'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations () {
        return array(
            'children' => array(self::HAS_MANY, 'DbPartners', array('childId' => 'id'), 'through' => 'childrenIdx'),
            'childrenIdx' => array(self::HAS_MANY, 'DbPartnerGroup', array('parentId' => 'id')),
            'parents' => array(self::HAS_MANY, 'DbPartners', array('parentId' => 'id'), 'through' => 'parentsIdx'),
            'parentsIdx' => array(self::HAS_MANY, 'DbPartnerGroup', array('childId' => 'id')),
            'ttpResults' => array(self::HAS_MANY, 'DbAllMonTtpResults', array('PISO3' => 'ccode3')),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels () {
        return array(
            'id' => 'ID',
            'country' => 'Country',
            'ccode3' => 'Country Code',
            'ncode3' => 'Ncode3',
            'aka' => 'Also Known As',
            'color' => 'Colour',
            'searchDef' => 'Search Default',
        );
    }

    /**
     * @param array $options formatSearch() options
     * @return CActiveDataProvider
     */
    public function search ($options = null) {
        $criteria = new CDbCriteria;

        $criteria->compare('t.id', $this->id);
        $criteria->compare('t.ncode3', $this->ncode3);
        $criteria->compare('t.ccode3', $this->ccode3, true);
        $criteria->compare('t.country', $this->country, true);
        $criteria->compare('t.aka', $this->aka, true);
        $criteria->compare('t.color', $this->color, true);
        $criteria->compare('t.searchDef', $this->searchDef);

        $options['sortDefault'] = 't.id ASC';

        $searchAttrs = array();
        $criteria = $this->addSearchTerms($criteria, $searchAttrs);

        return $this->formatSearch($this, $criteria, $options);
    }

    /**
     * @param string $className active record class name.
     * @return DbPartners the static model class
     */
    public static function model ($className = __CLASS__) {
        return parent::model($className);
    }

    public function getCode () {
        return $this->ccode3;
    }

    public function getName () {
        return $this->country;
    }

    public function getMergedTtp($reporter){
        $valid = false;
        $merged = new DbAllMonTtpResults();
        $merged->RINO3 = $reporter;
        $merged->PISO3 = $this->code;

        return $merged->getMerged();
    }

    public function getMergedTtf($reporter){
        $valid = false;
        $merged = new DbAllMonTTfResults();
        $merged->RINO3 = $reporter;
        $merged->PISO3 = $this->code;

        return $merged->getMerged();
    }
}
