<?php

/**
 * This is the model class for table "db_bom".
 *
 * The followings are the available columns in table 'db_bom':
 * @property integer $id
 * @property string $asset_name
 * @property string $weighting
 * @property string $month_1
 * @property string $month_2
 * @property string $month_3
 * @property string $month_4
 * @property string $month_5
 * @property string $month_6
 * @property string $month_7
 * @property string $month_8
 * @property string $month_9
 * @property string $month_10
 * @property string $month_11
 * @property string $month_12
 * @property string $month_13
 * @property string $month_14
 * @property string $month_15
 * @property string $month_16
 * @property string $month_17
 * @property string $month_18
 * @property string $month_19
 * @property string $month_20
 * @property string $month_21
 * @property string $month_22
 * @property string $month_23
 * @property string $month_24
 */
class DbBom extends ActiveRecord {
	/**
	 * @return string the associated database table name
	 */
	public function tableName(){
		return 'db_bom';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules(){
		return array(
			array('asset_name', 'length', 'max'=>255),
			array('weighting, month_1, month_2, month_3, month_4, month_5, month_6, month_7, month_8, month_9, month_10, month_11, month_12, month_13, month_14, month_15, month_16, month_17, month_18, month_19, month_20, month_21, month_22, month_23, month_24', 'length', 'max'=>32),
			array('id, asset_name, weighting, month_1, month_2, month_3, month_4, month_5, month_6, month_7, month_8, month_9, month_10, month_11, month_12, month_13, month_14, month_15, month_16, month_17, month_18, month_19, month_20, month_21, month_22, month_23, month_24', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations(){
		return array(
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels(){
		return array(
			'id' => 'ID',
			'asset_name' => 'Name',
			'weighting' => 'Weighting',
			'month_1' => 'Month 1',
			'month_2' => 'Month 2',
			'month_3' => 'Month 3',
			'month_4' => 'Month 4',
			'month_5' => 'Month 5',
			'month_6' => 'Month 6',
			'month_7' => 'Month 7',
			'month_8' => 'Month 8',
			'month_9' => 'Month 9',
			'month_10' => 'Month 10',
			'month_11' => 'Month 11',
			'month_12' => 'Month 12',
			'month_13' => 'Month 13',
			'month_14' => 'Month 14',
			'month_15' => 'Month 15',
			'month_16' => 'Month 16',
			'month_17' => 'Month 17',
			'month_18' => 'Month 18',
			'month_19' => 'Month 19',
			'month_20' => 'Month 20',
			'month_21' => 'Month 21',
			'month_22' => 'Month 22',
			'month_23' => 'Month 23',
			'month_24' => 'Month 24',
		);
	}

	/**
	 * @param array $options formatSearch() options
     * @return CActiveDataProvider
	 */
    public function search ($options = null) {
		$criteria=new CDbCriteria;

		$criteria->compare('t.id',$this->id);
		$criteria->compare('t.asset_name',$this->asset_name,true);
		$criteria->compare('t.weighting',$this->weighting,true);
		$criteria->compare('t.month_1',$this->month_1,true);
		$criteria->compare('t.month_2',$this->month_2,true);
		$criteria->compare('t.month_3',$this->month_3,true);
		$criteria->compare('t.month_4',$this->month_4,true);
		$criteria->compare('t.month_5',$this->month_5,true);
		$criteria->compare('t.month_6',$this->month_6,true);
		$criteria->compare('t.month_7',$this->month_7,true);
		$criteria->compare('t.month_8',$this->month_8,true);
		$criteria->compare('t.month_9',$this->month_9,true);
		$criteria->compare('t.month_10',$this->month_10,true);
		$criteria->compare('t.month_11',$this->month_11,true);
		$criteria->compare('t.month_12',$this->month_12,true);
		$criteria->compare('t.month_13',$this->month_13,true);
		$criteria->compare('t.month_14',$this->month_14,true);
		$criteria->compare('t.month_15',$this->month_15,true);
		$criteria->compare('t.month_16',$this->month_16,true);
		$criteria->compare('t.month_17',$this->month_17,true);
		$criteria->compare('t.month_18',$this->month_18,true);
		$criteria->compare('t.month_19',$this->month_19,true);
		$criteria->compare('t.month_20',$this->month_20,true);
		$criteria->compare('t.month_21',$this->month_21,true);
		$criteria->compare('t.month_22',$this->month_22,true);
		$criteria->compare('t.month_23',$this->month_23,true);
		$criteria->compare('t.month_24',$this->month_24,true);

		$options['sortDefault'] = 't.id ASC';

        $searchAttrs = array();
        $criteria = $this->addSearchTerms($criteria, $searchAttrs);

        return $this->formatSearch($this, $criteria, $options);
	}

	/**
	 * @param string $className active record class name.
	 * @return DbBom the static model class
	 */
	public static function model($className=__CLASS__){
		return parent::model($className);
	}

    /*
     * filter the attributes to an array of just attributes that contain trade data
     * @param array $options - see $default
     * @return array
     */
    public function listData () {
        $list = array();

        foreach ($this->attributes as $attr => $val) {
            if (strpos($attr, 'month_') !== false) {
                $list[$attr] = $val;
            }
        }

        return $list;
    }

    public static function monthTitle($attr){
        $month = false;
        if (strpos($attr, 'month_') !== false) {
            $addMonth = str_replace('month_', '', $attr) - 1;
            $month = $start = strtotime('Jan 2017');
            if($addMonth > 0){
                $month = strtotime('+ '.$addMonth.' month', $start);
            }
            $month = date('M Y', $month);
        }

        return $month;
    }
}
