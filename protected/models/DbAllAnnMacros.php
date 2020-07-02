<?php

/**
 * This is the model class for table "all_ann_macros".
 *
 * The followings are the available columns in table 'all_ann_macros':
 * @property integer $SLNO
 * @property string $Asset_ID
 * @property string $Asset_Name
 * @property string $Variant
 * @property integer $Note_ID
 * @property integer $Source_ID
 * @property integer $RINO3
 * @property string $RISO3
 */
class DbAllAnnMacros extends ActiveRecordMacroData {
	/**
	 * @return string the associated database table name
	 */
	public function tableName(){
		return 'ALL_ANN_MACROS';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules(){
		return array(
			array('RINO3, Note_ID, Source_ID', 'numerical', 'integerOnly'=>true),
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
			'Note_ID' => 'Note ID',
			'Source_ID' => 'Source ID',
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
		$criteria->compare('t.Asset_ID',$this->Asset_ID);
		$criteria->compare('t.Asset_Name',$this->Asset_Name,true);
		$criteria->compare('t.Variant',$this->Variant);
        $criteria->compare('t.Note_ID',$this->Note_ID);
        $criteria->compare('t.Source_ID',$this->Source_ID);
		$criteria->compare('t.RINO3',$this->RINO3);
		$criteria->compare('t.RISO3',$this->RISO3);

		$options['sortDefault'] = 't.SLNO ASC';

        $searchAttrs = array();
        $criteria = $this->addSearchTerms($criteria, $searchAttrs);

        return $this->formatSearch($this, $criteria, $options);
	}

	/**
	 * @param string $className active record class name.
	 * @return DbAllAnnMacros the static model class
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
            'year' => array(),
        );
        $options = Yii::app()->format->options($options,$default);
        $options['year'] = (is_array($options['year']) ? $options['year'] : array($options['year']));

        foreach ($this->attributes as $attr => $val) {
            $parts = $this->listAttrParts($attr);

            if (!empty($parts['year']) && is_numeric($parts['year'])) {
                $valid = true;

                if ($valid && !empty($options['year']) && !in_array($parts['year'],$options['year'])) {
                    $valid = false;
                }
                if ($valid && !empty($options['start']) && $parts['year'].'12' < $options['start']) {
                    $valid = false;
                }
                if ($valid && !empty($options['end']) && $parts['year'].'01' > $options['end']) {
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
