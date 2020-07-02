<?php

/**
 * This is the model class for table "db_source".
 *
 * The followings are the available columns in table 'db_source':
 * @property integer $id
 * @property string $description
 */
class DbSource extends ActiveRecord {
	/**
	 * @return string the associated database table name
	 */
	public function tableName(){
		return 'db_source';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules(){
		return array(
			array('description', 'required'),
			array('description', 'length', 'max'=>255),
			array('id, description', 'safe', 'on'=>'search'),
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
			'description' => 'Description',
		);
	}

	/**
	 * @param array $options formatSearch() options
     * @return CActiveDataProvider
	 */
    public function search ($options = null) {
		$criteria=new CDbCriteria;

		$criteria->compare('t.id',$this->id);
		$criteria->compare('t.description',$this->description,true);

		$options['sortDefault'] = 't.id ASC';

        $searchAttrs = array();
        $criteria = $this->addSearchTerms($criteria, $searchAttrs);

        return $this->formatSearch($this, $criteria, $options);
	}

	/**
	 * @param string $className active record class name.
	 * @return DbSource the static model class
	 */
	public static function model($className=__CLASS__){
		return parent::model($className);
	}
}
