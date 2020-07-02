<?php

/**
 * This is the model class for table "all_mon_macros".
 *
 * The followings are the available columns in table 'all_mon_macros':
 * @property integer $SLNO
 * @property string $Asset_ID
 * @property string $Asset_Name
 * @property string $Variant
 * @property integer $Note_ID
 * @property integer $Source_ID
 * @property integer $RINO3
 * @property string $RISO3
 */
class DbAllMonMacros extends ActiveRecord {
	/**
	 * @return string the associated database table name
	 */
	public function tableName(){
		return 'ALL_MON_MACROS';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules(){
		return array(
			array('Note_ID, Source_ID, RINO3', 'numerical', 'integerOnly'=>true),
			array('Asset_ID, Asset_Name, Variant', 'length', 'max'=>255),
			array('RISO3', 'length', 'max'=>3),
			array('SLNO, Asset_ID, Asset_Name, Variant, Note_ID, Source_ID, RINO3, RISO3', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations(){
		return array(
            'note' => array(self::BELONGS_TO, 'DbNote', 'Note_ID'),
            'reporter' => array(self::BELONGS_TO, 'DbReporters', array('RISO3' => 'ccode3')),
            'source' => array(self::BELONGS_TO, 'DbSource', 'Source_ID'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels(){
		return array(
			'SLNO' => 'Slno',
			'Asset_ID' => 'Asset',
			'Asset_Name' => 'Asset Name',
			'Variant' => 'Variant',
			'Note_ID' => 'Note',
			'Source_ID' => 'Source',
			'RINO3' => 'Rino3',
			'RISO3' => 'Riso3',
		);
	}

	/**
	 * @param array $options formatSearch() options
     * @return CActiveDataProvider
	 */
    public function search ($options = null) {
		$criteria=new CDbCriteria;

		$criteria->compare('t.SLNO',$this->SLNO);
		$criteria->compare('t.Asset_ID',$this->Asset_ID,true);
		$criteria->compare('t.Asset_Name',$this->Asset_Name,true);
		$criteria->compare('t.Variant',$this->Variant,true);
		$criteria->compare('t.Note_ID',$this->Note_ID);
		$criteria->compare('t.Source_ID',$this->Source_ID);
		$criteria->compare('t.RINO3',$this->RINO3);
		$criteria->compare('t.RISO3',$this->RISO3,true);

		$options['sortDefault'] = 't.SLNO ASC';

        $searchAttrs = array();
        $criteria = $this->addSearchTerms($criteria, $searchAttrs);

        return $this->formatSearch($this, $criteria, $options);
	}

	/**
	 * @param string $className active record class name.
	 * @return DbAllMonMacros the static model class
	 */
	public static function model($className=__CLASS__){
		return parent::model($className);
	}

    /*
     * separate trade data columns names
     * @param string $attr
     * @return array
     */
    public function listAttrParts($attr){
        return array(
            'year' => substr($attr, 1, 4),
            'month' => substr($attr, 5, 6),
        );
    }

    /*
     * filter the attributes to an array of just attributes that contain trade data
     * @param array $options - see $default
     * @return array
     */
    public function listData ($options = array()) {
        $list = array();

        $default = array(
            'month' => array(),
            'year' => array(),
        );
        $options = Yii::app()->format->options($options,$default);
        $options['month'] = (is_array($options['month']) ? $options['month'] : array($options['month']));
        $options['year'] = (is_array($options['year']) ? $options['year'] : array($options['year']));

        foreach ($this->attributes as $attr => $val) {
            $parts = $this->listAttrParts($attr);

            if (!empty($parts['year']) && is_numeric($parts['year']) && !empty($parts['month']) && is_numeric($parts['month'])) {
                $valid = true;

                if ($valid && !empty($options['month']) && !in_array($parts['month'],$options['month'])) {
                    $valid = false;
                }
                if ($valid && !empty($options['year']) && !in_array($parts['year'],$options['year'])) {
                    $valid = false;
                }
                if ($valid && !empty($options['start']) && $parts['year'].$parts['month'] < $options['start']) {
                    $valid = false;
                }
                if ($valid && !empty($options['end']) && $parts['year'].$parts['month'] > $options['end']) {
                    $valid = false;
                }

                if($valid){
                    $list[$attr] = $val;
                }
            }
        }

        return $list;
    }
}
