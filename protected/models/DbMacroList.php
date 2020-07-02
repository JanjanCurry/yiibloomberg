<?php

/**
 * This is the model class for table "db_macro_list".
 *
 * The followings are the available columns in table 'db_macro_list':
 * @property integer $id
 * @property string  $category
 * @property string  $assetId
 * @property string  $assetName
 * @property string  $variant
 * @property integer $orderId
 * @property string  $color
 */
class DbMacroList extends ActiveRecord {
    /**
     * @return string the associated database table name
     */
    public function tableName () {
        return 'db_macro_list';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules () {
        return array(
            array('orderId', 'numerical', 'integerOnly' => true),
            array('category, assetName', 'length', 'max' => 45),
            array('assetId, variant', 'length', 'max' => 20),
            array('color', 'length', 'max' => 6),
            array('id, category, assetId, assetName, variant, orderId, color', 'safe', 'on' => 'search'),
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
            'category' => 'Category',
            'assetId' => 'Asset',
            'assetName' => 'Asset Name',
            'variant' => 'Variant',
            'orderId' => 'Order',
            'color' => 'color',
        );
    }

    /**
     * @param array $options formatSearch() options
     * @return CActiveDataProvider
     */
    public function search ($options = null) {
        $criteria = new CDbCriteria;

        $criteria->compare('t.id', $this->id);
        $criteria->compare('t.category', $this->category, true);
        $criteria->compare('t.assetId', $this->assetId);
        $criteria->compare('t.assetName', $this->assetName, true);
        $criteria->compare('t.variant', $this->variant);
        $criteria->compare('t.orderId', $this->orderId);
        $criteria->compare('t.color', $this->color);

        $options['sortDefault'] = 't.category ASC, t.orderId ASC, t.assetName ASC';

        $searchAttrs = array();
        $criteria = $this->addSearchTerms($criteria, $searchAttrs);

        return $this->formatSearch($this, $criteria, $options);
    }

    /**
     * @param string $className active record class name.
     * @return DbMacroList the static model class
     */
    public static function model ($className = __CLASS__) {
        return parent::model($className);
    }

    public function getCode(){
        return $this->assetId;
    }

    public function getName(){
        return $this->assetName;
    }
}
