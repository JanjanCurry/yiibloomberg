<?php

/**
 * This is the model class for table "db_user_favorites".
 *
 * The followings are the available columns in table 'db_user_favorites':
 * @property integer $id
 * @property integer $userId
 * @property string  $type
 * @property integer $orderId
 * @property string  $data
 * @property integer $updated
 * @property integer $created
 */
class DbUserFavorites extends ActiveRecord {

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
        return 'db_user_favorites';
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
     * @return DbUserFavorites the static model class
     */
    public static function model ($className = __CLASS__) {
        return parent::model($className);
    }

    public function assign ($userId, $type, $data) {
        $valid = false;
        if (!$this->isAssigned($userId, $type, $data)) {
            $userAssign = new DbUserFavorites();
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
            if($valid){
                if(!empty($data['item'])){
                    if(is_array($data['item'])){
                        foreach ($data['item'] as $item){
                            DbUserAssetLog::model()->add([
                                'userId' => $userId,
                                'market' => $type,
                                'code' => $item,
                                'action' => 'fav',
                            ]);
                        }
                    }else{
                        DbUserAssetLog::model()->add([
                            'userId' => $userId,
                            'market' => $type,
                            'code' => $data['item'],
                            'action' => 'fav',
                        ]);
                    }
                }
                if(!empty($data['compare'])){
                    if(is_array($data['compare'])){
                        foreach ($data['compare'] as $item){
                            DbUserAssetLog::model()->add([
                                'userId' => $userId,
                                'market' => $type,
                                'code' => $item,
                                'action' => 'fav',
                            ]);
                        }
                    }else{
                        DbUserAssetLog::model()->add([
                            'userId' => $userId,
                            'market' => $type,
                            'code' => $data['compare'],
                            'action' => 'fav',
                        ]);
                    }
                }
            }
        }

        return $valid;
    }

    public function getName(){
        $return = '';
        $parts = array();

        switch ($this->type){
            case 'commodity':
            case 'currency':
            case 'equity':
                if(!empty($this->data[$this->type])){
                    $parts[] = $this->data[$this->type];
                }
                break;

            case 'macro':
                if(!empty($this->data['reporter'])){
                    $parts[] = $this->data['reporter'];
                }
                if(!empty($this->data['macro'])){
                    $parts[] = ActiveRecordMacroData::model()->getListLabel('listMacro',$this->data['macro']);
                }
                if(!empty($this->data['compare']) && is_array($this->data['compare'])){
                    foreach($this->data['compare'] as $key => $val){
                        $parts[] = ActiveRecordMacroData::model()->getListLabel('listMacro',$val);
                    }
                }
                break;

            case 'trade':
                if(!empty($this->data['indicator'])){
                    $parts[] = ActiveRecordTradeData::model()->getListLabel('listIndicator',$this->data['indicator']);
                }
                if(!empty($this->data['reporter'])){
                    $parts[] = $this->data['reporter'];
                }
                if(!empty($this->data['partner'])){
                    $parts[] = 'w/ '.$this->data['partner'];
                }
                if(!empty($this->data['sector'])){
                    $parts[] = 'w/ '.$this->data['sector'];
                }
                break;
        }
        if(!empty($parts)){
            $return = implode(', ', $parts);
        }
        return $return;
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
        $favorites = DbUserFavorites::model()->findAllByAttributes(array(
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

    public function listDataAttrs($type = null){
        if(empty($type)){
            $type = $this->type;
        }
        $list = array();
        switch($type){
            case 'commodity';
                $list = array(
                    'item',
                    'compare',
                );
                break;

            case 'currency';
                $list = array(
                    'item',
                    'compare',
                );
                break;

            case 'equity';
                $list = array(
                    'item',
                    'compare',
                );
                break;

            case 'macro';
                $list = array(
                    'reporter',
                    'macro',
                    'compare',
                    //'period',
                );
                break;

            case 'trade';
                $list = array(
                    'reporter',
                    'partner',
                    'sector',
                    'compare',
                    'indicator',
                );
                break;
        }
        return $list;
    }

    public function listType () {
        return array(
            'commodity' => 'Commodity',
            'currency' => 'Currency',
            'equity' => 'Equity',
            'macro' => 'Macro',
            'trade' => 'Trade',
        );
    }

    public function unassign ($userId, $type, $data) {
        $valid = false;
        $favorites = DbUserFavorites::model()->findAllByAttributes(array(
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
        return DbUserFavorites::model()->deleteAllByAttributes(array(
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
