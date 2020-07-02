<?php

/**
 * This is the model class for table "db_company".
 *
 * The followings are the available columns in table 'db_company':
 * @property integer $id
 * @property string $name
 * @property integer $updated
 * @property integer $created
 */
class DbCompany extends ActiveRecord {
	/**
	 * @return string the associated database table name
	 */
	public function tableName(){
		return 'db_company';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules(){
		return array(
			array('name', 'required'),
            array('name', 'unique'),

            array('updated, created', 'numerical', 'integerOnly'=>true),
			array('name', 'length', 'max'=>255),

            array('updated', 'default', 'value' => time(), 'setOnEmpty' => false),
            array('created', 'default', 'value' => time(), 'setOnEmpty' => false, 'on' => 'insert'),

			array('id, name, updated, created', 'safe', 'on'=>'search'),
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
			'name' => 'Name',
			'updated' => 'Updated',
			'created' => 'Created',
		);
	}

	/**
	 * @param array $options formatSearch() options
     * @return CActiveDataProvider
	 */
    public function search ($options = null) {
		$criteria=new CDbCriteria;

		$criteria->compare('t.id',$this->id);
		$criteria->compare('t.name',$this->name,true);
		$criteria->compare('t.updated',$this->updated);
		$criteria->compare('t.created',$this->created);

		$options['sortDefault'] = 't.id ASC';

        $searchAttrs = array();
        $criteria = $this->addSearchTerms($criteria, $searchAttrs);

        return $this->formatSearch($this, $criteria, $options);
	}

	/**
	 * @param string $className active record class name.
	 * @return DbCompany the static model class
	 */
	public static function model($className=__CLASS__){
		return parent::model($className);
	}

    public static function add($name){
        $attrs = [
            'name' => $name,
        ];
        $model = DbCompany::model()->findByAttributes($attrs);
        if(empty($model)){
            $model = new DbCompany();
            $model->name = $name;
        }

        return ($model->save() ? $model : false);
    }

    public function calcAccounts($name){
        $criteria = new CDbCriteria();
        $criteria->with[] = 'companyModel';
        $criteria->compare('t.status','active');
        $criteria->compare('companyModel.name',$name,true);
	    $count = DbUser::model()->count($criteria);
	    return (empty($count) ? 0 : $count);
    }
}
