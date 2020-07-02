<?php

/**
 * This is the model class for table "db_user_dash".
 *
 * The followings are the available columns in table 'db_user_dash':
 * @property integer $id
 * @property integer $userId
 * @property string  $type
 * @property integer $orderId
 * @property string  $data
 * @property integer $updated
 * @property integer $created
 */
class DbUserDash extends ActiveRecord {

    public $maxOrder;

    protected function afterConstruct () {
        $this->formatSerialised('data', 'array');

        return parent::afterConstruct();
    }

    protected function afterFind () {
        parent::afterFind();
        $this->formatSerialised('data', 'array');
    }

    protected function beforeValidate () {
        $this->updateOrderId();
        $this->formatSerialised('data', 'string');

        return parent::beforeValidate();
    }

    protected function afterValidate () {
        parent::afterValidate();
        $this->formatSerialised('data', 'array');
    }

    protected function beforeSave () {
        $this->formatSerialised('data', 'string');

        return parent::beforeSave();
    }

    protected function afterSave () {
        parent::afterSave();
        $this->formatSerialised('data', 'array');
    }

    /**
     * @return string the associated database table name
     */
    public function tableName () {
        return 'db_user_dash';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules () {
        return array(
            array('userId, type', 'required'),

            array('userId, orderId, updated, created', 'numerical', 'integerOnly' => true),
            array('type', 'length', 'max' => 45),

            array('updated', 'default', 'value' => time(), 'setOnEmpty' => false, 'on' => 'update'),
            array('updated, created', 'default', 'value' => time(), 'setOnEmpty' => false, 'on' => 'insert'),

            array('data', 'safe'),
            array('id, userId, type, orderId, data, updated, created', 'safe', 'on' => 'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations () {
        return array(
            'user' => array(self::BELONGS_TO, 'DbUser', 'userId'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels () {
        return array(
            'id' => 'ID',
            'userId' => 'User',
            'type' => 'Type',
            'orderId' => 'Sort Order',
            'data' => 'Data',
            'updated' => 'Updated',
            'created' => 'Created',
        );
    }

    /**
     * @param array $options formatSearch() options
     * @return CActiveDataProvider
     */
    public function search ($options = null) {
        $criteria = new CDbCriteria;

        $criteria->compare('t.id', $this->id);
        $criteria->compare('t.userId', $this->userId);
        $criteria->compare('t.type', $this->type, true);
        $criteria->compare('t.orderId', $this->orderId);
        $criteria->compare('t.data', $this->data, true);
        $criteria->compare('t.updated', $this->updated);
        $criteria->compare('t.created', $this->created);

        $options['sortDefault'] = 't.id ASC';

        $searchAttrs = array();
        $criteria = $this->addSearchTerms($criteria, $searchAttrs);

        return $this->formatSearch($this, $criteria, $options);
    }

    /**
     * @param string $className active record class name.
     * @return DbUserDash the static model class
     */
    public static function model ($className = __CLASS__) {
        return parent::model($className);
    }

    public function assign ($userId, $type, $data) {
        $valid = false;
        if (!$this->isAssigned($userId, $type, $data)) {
            $userAssign = new DbUserDash();
            $userAssign->userId = $userId;
            $userAssign->type = $type;

            $temp = array();
            foreach($this->listDataAttrs($type) as $attr){
                if(isset($data[$attr])){
                    $temp[$attr] = $data[$attr];
                }
            }
            $userAssign->data = $temp;
            $valid = $userAssign->save();

        }

        return $valid;
    }

    public function getUrl(){
        $params = array();
        if(!empty($this->data)){
            foreach($this->data as $key => $val){
                if(!empty($val)){
                    $params[$key] = $val;
                }
            }
        }
        return Yii::app()->createUrl($this->type.'/index', $this->data);
    }

    public function isAssigned ($userId, $type, $data) {
        $favorites = DbUserDash::model()->findAllByAttributes(array(
            'userId' => $userId,
            'type' => $type,
        ));

        if(!empty($favorites)){
            $attrs = $this->listDataAttrs($type);
            foreach($favorites as $favorite){
                $exists = 0;
                foreach($attrs as $attr){
                    if(!empty($favorite->data[$attr]) && !empty($data[$attr]) && $favorite->data[$attr] == $data[$attr]){
                        $exists++;
                    }else if(empty($favorite->data[$attr]) && empty($data[$attr])){
                        $exists++;
                    }
                }

                if($exists == count($attrs)){
                    return true;
                }
            }
        }

        return false;
    }

    public function listDataAttrs($type){
        $list = [];
        switch($type){
            case 'outlook':
            case 'spark':
            case 'yoy':
                $list = [
                    'item',
                    'market',
                ];
                break;

            case 'g10':
                $list = [
                    'macro',
                ];
                break;
        }

        return $list;
    }

    public function unassign ($userId, $type, $data) {
        $valid = false;
        $favorites = DbUserDash::model()->findAllByAttributes(array(
            'userId' => $userId,
            'type' => $type,
        ));
        if(!empty($favorites)){
            $attrs = $this->listDataAttrs($type);
            foreach($favorites as $favorite){
                $exists = 0;
                foreach($attrs as $attr){
                    if(!empty($favorite->data[$attr]) && !empty($data[$attr]) && $favorite->data[$attr] == $data[$attr]){
                        $exists++;
                    }else if(empty($favorite->data[$attr]) && empty($data[$attr])){
                        $exists++;
                    }
                }

                if($exists == count($attrs)){
                    $valid = $favorite->delete();
                }
            }
        }

        return $valid;
    }

    public function unassignAll ($userId, $type) {
        return DbUserDash::model()->deleteAllByAttributes(array(
            'userId' => $userId,
            'type' => $type,
        ));
    }

    public function updateOrderId () {
        if (empty($this->orderId)) {
            $criteria = new CDbCriteria;
            $criteria->compare('userId', $this->userId);
            $criteria->compare('type', $this->type);
            $criteria->select = 'max(orderId) AS maxOrder';
            $item = $this->find($criteria);
            if (!empty($item)) {
                $this->orderId = $item->maxOrder + 1;
            } else {
                //$this->orderId must be greater that zero
                $this->orderId = 1;
            }
        }
    }
}
