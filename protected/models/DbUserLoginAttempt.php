<?php

/**
 * This is the model class for table "db_user_login_attempt".
 *
 * The followings are the available columns in table 'db_user_login_attempt':
 * @property integer $userId
 * @property integer $success
 * @property string  $ipAddress
 * @property integer $created
 */
class DbUserLoginAttempt extends ActiveRecord {
    /**
     * @return string the associated database table name
     */
    public function tableName () {
        return 'db_user_login_attempt';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules () {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('userId, success', 'required'),

            array('userId, success, created', 'numerical', 'integerOnly' => true),
            array('ipAddress', 'length', 'max' => 45),
            
            array('created', 'default', 'value' => time(), 'setOnEmpty' => false, 'on' => 'insert'),

            // The following rule is used by search().
            array('userId, success, ipAddress, created', 'safe', 'on' => 'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations () {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
            'user' => array(self::BELONGS_TO, 'DbUser', 'userId'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels () {
        return array(
            'userId' => 'User',
            'success' => 'Success',
            'ipAddress' => 'IP Address',
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
    public function search($options=null){
        $criteria=new CDbCriteria;

        $criteria->compare('userId', $this->userId);
        $criteria->compare('success', $this->success);
        $criteria->compare('ipAddress', $this->ipAddress);
        $criteria->compare('created', $this->created);

        $options['sortDefault'] = 'created DESC';

        return $this->formatSearch($this, $criteria, $options);
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return DbUserLoginAttempt the static model class
     */
    public static function model ($className = __CLASS__) {
        return parent::model($className);
    }

    public function add($userId,$success){
        $model = new DbUserLoginAttempt();
        $model->userId = $userId;
        $model->success = $success;
        $model->ipAddress = $_SERVER['REMOTE_ADDR'];
        return $model->save();
    }

    public function calcAttempts($duration = '-5 min',$userId = null){
        $return = 0;
        $criteria = new CDbCriteria();
        $criteria->order = 'created DESC';
        $criteria->compare('success',1);
        $criteria->compare('userId',$userId);
        $criteria->compare('ipAddress',$_SERVER['REMOTE_ADDR']);
        $latestSuccess = $this->find($criteria);
        if(empty($latestSuccess) || empty($latestSuccess->created) || $latestSuccess->created < strtotime($duration)){
            $criteria = new CDbCriteria();
            $criteria->order = 'created DESC';
            $criteria->compare('success',0);
            $criteria->compare('ipAddress',$_SERVER['REMOTE_ADDR']);
            $criteria->compare('created','>'.strtotime($duration));
            $count = $this->count($criteria);
            if(!empty($count)){
                $return = $count;
            }
        }

        return $return;
    }

}
