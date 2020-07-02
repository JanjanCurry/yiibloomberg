<?php

/**
 * This is the model class for table "db_cheesy_compliment".
 *
 * The followings are the available columns in table 'db_cheesy_compliment':
 * @property integer $id
 * @property string $cheese
 */
class DbCheesyCompliment extends ActiveRecord {
	/**
	 * @return string the associated database table name
	 */
	public function tableName(){
		return 'db_cheesy_compliment';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules(){
		return array(
			array('cheese', 'length', 'max'=>255),
			array('id, cheese', 'safe', 'on'=>'search'),
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
			'id' => 'id',
			'cheese' => 'Cheese',
		);
	}

	/**
	 * @param array $options formatSearch() options
     * @return CActiveDataProvider
	 */
    public function search ($options = null) {
		$criteria=new CDbCriteria;

		$criteria->compare('t.id',$this->id);
		$criteria->compare('t.cheese',$this->cheese,true);

		$options['sortDefault'] = 't.id ASC';

        $searchAttrs = array();
        $criteria = $this->addSearchTerms($criteria, $searchAttrs);

        return $this->formatSearch($this, $criteria, $options);
	}

	/**
	 * @param string $className active record class name.
	 * @return DbCheesyCompliment the static model class
	 */
	public static function model($className=__CLASS__){
		return parent::model($className);
	}
}
