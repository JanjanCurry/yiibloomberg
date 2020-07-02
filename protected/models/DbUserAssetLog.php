<?php

/**
 * This is the model class for table "db_user_asset_log".
 *
 * The followings are the available columns in table 'db_user_asset_log':
 * @property integer $id
 * @property integer $created
 * @property integer $updated
 * @property integer $userId
 * @property string  $market
 * @property string  $code
 * @property string  $action
 */
class DbUserAssetLog extends ActiveRecord {

    /**
     * @return string the associated database table name
     */
    public function tableName () {
        return 'db_user_asset_log';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules () {
        return array(
            array('userId, market, code, action', 'required'),

            array('created, updated, userId', 'numerical', 'integerOnly' => true),
            array('market, code, action', 'length', 'max' => 10),

            array('updated', 'default', 'value' => time(), 'setOnEmpty' => false),
            array('created', 'default', 'value' => time(), 'setOnEmpty' => false, 'on' => 'insert'),

            array('id, created, updated, userId, market, code, action', 'safe', 'on' => 'search'),
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
            'controller' => 'Controller',
            'action' => 'Action',
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
        $criteria->compare('t.market', $this->market);
        $criteria->compare('t.code', $this->code);
        $criteria->compare('t.action', $this->action);
        $criteria->compare('t.updated', $this->updated);
        $criteria->compare('t.created', $this->created);

        $options['sortDefault'] = 't.created DESC, t.market ASC, t.code ASC, t.action ASC';

        $searchAttrs = array(
            't.market',
            't.code',
            't.action',
            'user.fName',
            'user.sName',
        );
        $criteria = $this->addSearchTerms($criteria, $searchAttrs);

        return $this->formatSearch($this, $criteria, $options);
    }

    /**
     * @param string $className active record class name.
     * @return DbUserLog the static model class
     */
    public static function model ($className = __CLASS__) {
        return parent::model($className);
    }

    public function add($data){
        $model = new DbUserAssetLog();
        $model->attributes = $data;

        return $model->save();
    }

    public function setDefaults () {
        if (empty($this->userId)) {
            $this->userId = Yii::app()->user->id;
        }
    }
}
