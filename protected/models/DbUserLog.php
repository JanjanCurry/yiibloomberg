<?php

/**
 * This is the model class for table "db_user_log".
 *
 * The followings are the available columns in table 'db_user_log':
 * @property integer $id
 * @property integer $userId
 * @property string  $controller
 * @property string  $action
 * @property string  $data
 * @property integer $updated
 * @property integer $created
 */
class DbUserLog extends ActiveRecord {

    protected function afterConstruct () {
        parent::afterConstruct();
        $this->formatSerialised('data', 'array');
    }

    protected function afterFind () {
        parent::afterFind();
        $this->formatSerialised('data', 'array');
    }

    protected function beforeValidate () {
        $this->setDefaults();
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
        return 'db_user_log';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules () {
        return array(
            array('userId, controller, action', 'required'),

            array('userId, updated, created', 'numerical', 'integerOnly' => true),
            array('controller, action', 'length', 'max' => 45),

            array('updated', 'default', 'value' => time(), 'setOnEmpty' => false),
            array('created', 'default', 'value' => time(), 'setOnEmpty' => false, 'on' => 'insert'),

            array('data', 'safe'),
            array('id, userId, controller, action, data, updated, created', 'safe', 'on' => 'search'),
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
        $criteria->compare('t.controller', $this->controller, true);
        $criteria->compare('t.action', $this->action, true);
        $criteria->compare('t.data', $this->data, true);
        $criteria->compare('t.updated', $this->updated);
        $criteria->compare('t.created', $this->created);

        if(!Yii::app()->user->checkAccess('dev')){
            $criteria->with[] = 'user';
            $criteria->addNotInCondition('user.type', ['dev']);
        }

        $options['sortDefault'] = 't.created DESC, t.controller ASC, t.action ASC';

        $searchAttrs = array(
            't.controller',
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

    public function add ($userId, $params = []) {
        $model = new DbUserLog();
        $model->userId = $userId;
        $model->data = $params;

        //return (!YII_DEBUG && $model->save() ? $model : false);
        return ($model->save() ? $model : false);
    }

    public function getUrl(){
        $url = Yii::app()->createUrl($this->controller.'/'.$this->action, $this->data);

        return $url;
    }

    public function setDefaults () {
        if (empty($this->controller)) {
            $this->controller = Yii::app()->controller->id;
        }

        if (empty($this->action)) {
            $this->action = Yii::app()->controller->action->id;
        }
    }
}
