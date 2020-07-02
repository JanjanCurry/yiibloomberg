<?php

/**
 * This is the model class for table "all_mrk_eqi_results".
 *
 * The followings are the available columns in table 'all_mrk_eqi_results':
 * @property integer    $SLNO
 * @property string     $Asset_ID
 * @property string     $Asset_Name
 * @property string     $Type
 * @property string     $Variant
 * @property string     $Correlations
 * @property string     $RSQ
 * @property integer    $Source_ID
 *
 * @property DbEquities $equity
 */
class DbAllMrkEqiResults extends ActiveRecordMarketData {
    /**
     * @return string the associated database table name
     */
    public function tableName () {
        return 'ALL_MRK_EQI_RESULTS';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules () {
        return array(
            array('Source_ID', 'numerical', 'integerOnly' => true),
            array('Asset_ID, Asset_Name, Type, Variant', 'length', 'max' => 255),
            array('SLNO, Asset_ID, Asset_Name, Type, Variant, Correlations, RSQ, Source_ID', 'safe', 'on' => 'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations () {
        return array(
            'equity' => array(self::BELONGS_TO, 'DbEquities', array('Asset_ID' => 'code')),
            'source' => array(self::BELONGS_TO, 'DbSource', 'Source_ID'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels () {
        return array(
            'SLNO' => 'Slno',
            'Asset_ID' => 'Asset',
            'Asset_Name' => 'Asset Name',
            'Type' => 'Type',
            'Variant' => 'Variant',
            'Correlations' => 'Correlations',
            'RSQ' => 'Rsq',
            'Source_ID' => 'Source',
        );
    }

    /**
     * @param array $options formatSearch() options
     * @return CActiveDataProvider
     */
    public function search ($options = null) {
        $criteria = new CDbCriteria;

        $criteria->compare('t.SLNO', $this->SLNO);
        $criteria->compare('t.Asset_ID', $this->Asset_ID, true);
        $criteria->compare('t.Asset_Name', $this->Asset_Name, true);
        $criteria->compare('t.Type', $this->Type, true);
        $criteria->compare('t.Variant', $this->Variant, true);
        $criteria->compare('t.Correlations', $this->Correlations, true);
        $criteria->compare('t.RSQ', $this->RSQ, true);
        $criteria->compare('t.Source_ID', $this->Source_ID);

        $options['sortDefault'] = 't.SLNO ASC';

        $searchAttrs = array();
        $criteria = $this->addSearchTerms($criteria, $searchAttrs);

        return $this->formatSearch($this, $criteria, $options);
    }

    /**
     * @param string $className active record class name.
     * @return DbAllMrkEqiResults the static model class
     */
    public static function model ($className = __CLASS__) {
        return parent::model($className);
    }

    public function getMarket () {
        return 'equity';
    }
}
