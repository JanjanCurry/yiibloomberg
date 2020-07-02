<?php

/**
 * This is the model class for table "db_note".
 *
 * The followings are the available columns in table 'db_note':
 * @property integer $id
 * @property string $note
 */
class DbNote extends ActiveRecord {
	/**
	 * @return string the associated database table name
	 */
	public function tableName(){
		return 'db_note';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules(){
		return array(
			array('note', 'required'),
			array('note', 'length', 'max'=>535),
			array('id, note', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations(){
		return array(
            'macroAnn' => array(self::HAS_ONE, 'DbAllAnnMacros', 'Note_ID'),
            'macroQtr' => array(self::HAS_ONE, 'DbAllQtrMacros', 'Note_ID'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels(){
		return array(
			'id' => 'id',
			'note' => 'Note',
		);
	}

	/**
	 * @param array $options formatSearch() options
     * @return CActiveDataProvider
	 */
    public function search ($options = null) {
		$criteria=new CDbCriteria;

		$criteria->compare('t.id',$this->id);
		$criteria->compare('t.note',$this->note,true);

		$options['sortDefault'] = 't.id ASC';

        $searchAttrs = array();
        $criteria = $this->addSearchTerms($criteria, $searchAttrs);

        return $this->formatSearch($this, $criteria, $options);
	}

	/**
	 * @param string $className active record class name.
	 * @return DbNote the static model class
	 */
	public static function model($className=__CLASS__){
		return parent::model($className);
	}
}
