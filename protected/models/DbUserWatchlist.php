<?php

/**
 * This is the model class for table "db_user_watchlist".
 *
 * The followings are the available columns in table 'db_user_watchlist':
 * @property integer $id
 * @property integer $userId
 * @property string $refType
 * @property integer $refId
 * @property string $type
 * @property integer $orderId
 * @property integer $updated
 * @property integer $created
 */
class DbUserWatchlist extends ActiveRecord
{

    public $maxOrder;

    protected function beforeValidate () {
        $this->updateOrderId();
        return parent::beforeValidate();
    }

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'db_user_watchlist';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('userId, refType, refId, type', 'required'),

			array('userId, refId, orderId, updated, created', 'numerical', 'integerOnly'=>true),
			array('refType, type', 'length', 'max'=>45),

            array('updated', 'default', 'value' => time(), 'setOnEmpty' => false, 'on' => 'update'),
            array('updated, created', 'default', 'value' => time(), 'setOnEmpty' => false, 'on' => 'insert'),

			array('id, userId, refType, refId, type, orderId, updated, created', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
            'user' => array(self::BELONGS_TO, 'DbUser', 'userId'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'userId' => 'User',
			'refType' => 'Ref Type',
			'refId' => 'Ref',
			'type' => 'Type',
            'orderId' => 'Sort Order',
			'updated' => 'Updated',
			'created' => 'Created',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 *
	 * Typical usecase:
	 * - Initialize the model fields with values from filter form.
	 * - Execute this method to get CActiveDataProvider instance which will filter
	 * models according to data in model fields.
	 * - Pass data provider to CGridView, CListView or any similar widget.
	 *
	 * @return CActiveDataProvider the data provider that can return the models
	 * based on the search/filter conditions.
	 */
    public function search ($options = null) {
        $criteria = new CDbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('userId',$this->userId);
		$criteria->compare('refType',$this->refType);
		$criteria->compare('refId',$this->refId);
		$criteria->compare('type',$this->type);
        $criteria->compare('orderId',$this->orderId);
		$criteria->compare('updated',$this->updated);
		$criteria->compare('created',$this->created);

        return $this->formatSearch($this, $criteria, $options);
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return DbUserWatchlist the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

    public static function assign($reference, $userId, $type) {
        $exists = DbUserWatchlist::Model()->findByAttributes(array(
            'refType' => get_class($reference),
            'refId' => $reference->pkVal,
            'userId' => $userId,
            'type' => $type,
        ));
        if (empty($exists)) {
            $userAssign = new DbUserWatchlist();
            $userAssign->refType = get_class($reference);
            $userAssign->refId = $reference->pkVal;
            $userAssign->userId = $userId;
            $userAssign->type = $type;
            if (!$userAssign->save()) {
                return false;
            }
        }

        return true;
    }

    public function getRef() {
        $ref = false;
        if (!empty($this->refType) && !empty($this->refId) && class_exists($this->refType)) {
            $model = new $this->refType;
            $ref = $model->findByPk($this->refId);
        }

        return $ref;
    }

    public static function isAssigned($refId, $type, $userId=null) {
        $valid = true;

        if(empty($userId) && !empty(Yii::app()->user) && !empty(Yii::app()->user->id)){
            $userId = Yii::app()->user->id;
        }

        if(empty($refId) || empty($type) || empty($userId)){
            $valid = false;
        }

        if($valid){
            $ref = DbUserWatchlist::model()->getListLabel('listTypeModel',$type);
            if(!empty($ref) && class_exists($ref)){
                $ref = new $ref;
                $ref = $ref->findByPk($refId);
                if(!empty($ref)){
                    $exists = DbUserWatchlist::model()->findByAttributes(array(
                        'userId' => Yii::app()->user->id,
                        'refId' => $ref->pkVal,
                        'refType' => get_class($ref),
                        'type' => $type,
                    ));

                    if(!empty($exists)){
                        return true;
                    }
                }
            }
        }

        return false;
    }

    public function listType() {
        return array(
            'country' => 'Country',
        );
    }

    public function listTypeModel() {
        return array(
            'country' => 'DbReporters',
        );
    }

    public static function unassign($reference, $userId, $type) {
        $return = DbUserWatchlist::model()->deleteAllByAttributes(array(
            'refType' => get_class($reference),
            'refId' => $reference->pkVal,
            'userId' => $userId,
            'type' => $type,
        ));

        return $return;
    }

    public static function unassignAll($reference, $type) {
        return DbUserWatchlist::model()->deleteAllByAttributes(array(
            'refType' => get_class($reference),
            'refId' => $reference->pkVal,
            'type' => $type,
        ));
    }

    public static function updateByType($refId, $type, $action='toggle', $userId=null) {
        $valid = true;

        $action = (!empty($action) ? $action : 'toggle');

        if(empty($userId) && !empty(Yii::app()->user) && !empty(Yii::app()->user->id)){
            $userId = Yii::app()->user->id;
        }

        if(empty($refId) || empty($type) || empty($action) || empty($userId)){
            $valid = false;
        }

        if($valid){
            $ref = DbUserWatchlist::model()->getListLabel('listTypeModel',$type);
            if(!empty($ref) && class_exists($ref)){
                $ref = new $ref;
                $ref = $ref->findByPk($refId);
                if(!empty($ref)){
                    $exists = DbUserWatchlist::model()->findByAttributes(array(
                        'userId' => Yii::app()->user->id,
                        'refId' => $ref->pkVal,
                        'refType' => get_class($ref),
                        'type' => $type,
                    ));

                    if(!empty($exists)){
                        if(in_array($action, array('remove','toggle')) && DbUserWatchlist::unassign($ref,$userId,$type)){
                            return 'unassign';
                        }
                    }else{
                        if(in_array($action, array('add','toggle')) && DbUserWatchlist::assign($ref,$userId,$type)){
                            return 'assign';
                        }
                    }
                }
            }
        }

        return false;
    }

    public function updateOrderId() {
        if (empty($this->orderId)) {
            $criteria = new CDbCriteria;
            $criteria->compare('userId', $this->userId);
            $criteria->compare('type', $this->type);
            $criteria->select = 'max(orderId) AS maxOrder';
            $item = $this->find($criteria);
            if (!empty($item)) {
                $this->orderId = $item->maxOrder + 1;
            }else {
                //$this->orderId must be greater that zero
                $this->orderId = 1;
            }
        }
    }
}
