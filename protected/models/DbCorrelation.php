<?php

/**
 * This is the model class for table "db_correlation".
 *
 * The followings are the available columns in table 'db_correlation':
 * @property string  $id
 * @property string  $fromAsset
 * @property string  $toAsset
 * @property string  $toAssetName
 * @property string  $corr
 * @property integer $history
 */
class DbCorrelation extends ActiveRecord {
    /**
     * @return string the associated database table name
     */
    public function tableName () {
        return 'db_correlation';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules () {
        return array(
            array('fromAsset, toAsset', 'length', 'max' => 20),
            array('corr', 'length', 'max' => 16),
            array('history', 'integerOnly' => true),
            array('id, fromAsset, toAsset, corr', 'safe', 'on' => 'search'),
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
            'fromAsset' => 'From Asset',
            'toAsset' => 'To Asset',
            'corr' => 'Corr',
            'history' => 'History',
        );
    }

    /**
     * @param array $options formatSearch() options
     * @return CActiveDataProvider
     */
    public function search ($options = null) {
        $criteria = new CDbCriteria;

        $criteria->compare('t.id', $this->id, true);
        $criteria->compare('t.fromAsset', $this->fromAsset, true);
        $criteria->compare('t.toAsset', $this->toAsset, true);
        $criteria->compare('t.corr', $this->corr, true);
        $criteria->compare('t.history', $this->history);

        $options['sortDefault'] = 't.id ASC';

        $searchAttrs = array();
        $criteria = $this->addSearchTerms($criteria, $searchAttrs);

        return $this->formatSearch($this, $criteria, $options);
    }

    /**
     * @param string $className active record class name.
     * @return DbCorrelation the static model class
     */
    public static function model ($className = __CLASS__) {
        return parent::model($className);
    }
}
