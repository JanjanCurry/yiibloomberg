<?php

/**
 * This is the model class for table "db_user_login_lock".
 *
 * The followings are the available columns in table 'db_user_login_lock':
 * @property string  $hash
 * @property integer $userId
 * @property string  $ipAddress
 * @property integer $updated
 * @property integer $created
 */
class DbUserLoginLock extends ActiveRecord {

    protected function beforeValidate() {
        $this->setDefaults();
        return parent::beforeValidate();
    }

    /**
     * @return string the associated database table name
     */
    public function tableName () {
        return 'db_user_login_lock';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules () {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('hash, userId', 'required'),

            array('hash, userId', 'unique'),

            array('userId, updated, created', 'numerical', 'integerOnly' => true),
            array('hash, ipAddress', 'length', 'max' => 45),

            array('updated', 'default', 'value' => time(), 'setOnEmpty' => false, 'on' => 'update'),
            array('updated, created', 'default', 'value' => time(), 'setOnEmpty' => false, 'on' => 'insert'),

            array('hash, userId, ipAddress, updated, created', 'safe', 'on' => 'search'),
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
            'hash' => 'Hash',
            'userId' => 'User',
            'ipAddress' => 'Ip Address',
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

        $criteria->compare('hash', $this->hash);
        $criteria->compare('userId', $this->userId);
        $criteria->compare('ipAddress', $this->ipAddress);
        $criteria->compare('updated', $this->updated);
        $criteria->compare('created', $this->created);

        $options['sortDefault'] = 'updated DESC';

        return $this->formatSearch($this, $criteria, $options);
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return DbUserLoginLock the static model class
     */
    public static function model ($className = __CLASS__) {
        return parent::model($className);
    }

    public function generateHash ($count=0) {
        if($count > 250){
            return false;
        }
        $length = 15;
        if (empty($this->hash)) {
            $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#%^&*()_-=+";
            $salt = Yii::app()->params['privateSalt'];
            $str = substr(str_shuffle($chars), 0, $length);
            $encrypted = base64_encode(md5($salt . md5(hash('SHA512', $salt . $str) . hash('SHA512', $str) . $salt)));
            $this->hash = substr($encrypted, 0, $length);

            $exists = $this->findByAttributes(array('hash'=>$this->hash));
            if(!empty($exists)){
                $this->hash = null;
                $count++;
                $this->generateHash($count);
            }
            return true;
        }
        return false;
    }

    public function isMultiLogin($userId) {
        $valid = false;
        $useCookie = false;
        $hash = '';

        $lock = new DbUserLoginLock();
        $lock->userId = $userId;

        //check we can add cookies
        Yii::app()->request->cookies['cookieTest'] = new CHttpCookie('cookieTest', 'test');
        if (!empty(Yii::app()->request->cookies['cookieTest'])) {
            $useCookie = true;
            unset(Yii::app()->request->cookies['cookieTest']);
        }

        //get data saved in cookie/session
        if ($useCookie) {
            if (!empty(Yii::app()->request->cookies['ciLoginLock'])) {
                $hash = Yii::app()->request->cookies['ciLoginLock']->value;
            }
        } else {
            if (!empty(Yii::app()->session['ciLoginLock'])) {
                $hash = Yii::app()->session['ciLoginLock'];
            }
        }

        //create user hash
        if (empty($hash)) {
            $lock->setDefaults();
            $hash = $lock->hash;
        }

        //check if user is already logged in
        $model = $this->findByAttributes(array('userId' => $userId));
        if (!empty($model)) {
            $lock = $model;

            if ($lock->hash != $hash) {
                if ($lock->updated > strtotime('-30 min')) {
                    //user is logged in on another machine
                    $valid = true;
                } else {
                    //user is logged in on another machine but has been idle for 30+ min
                    $lock->hash = null;
                }
            }
        }

        if (!$valid) {
            //save session of current valid login attempt
            $lock->ipAddress = null;
            $lock->save();
            $hash = $lock->hash;
        }

        //save login record as a cookie or session based of what is available
        if ($useCookie) {
            Yii::app()->request->cookies['ciLoginLock'] = new CHttpCookie('ciLoginLock', $hash, array('expire' => strtotime('+1 hour')));
        } else {
            Yii::app()->session['ciLoginLock'] = $hash;
        }

        //override for developers, devs can sign in on multiple machines
        /*if(Yii::app()->user->checkAccess('login-multi')) {
            $valid = false;
        }*/
        if($valid){
            $user = DbUser::model()->findByPk($userId);
            if(!empty($user) && !empty($user->allowMultiLogin)){
                $valid = false;
            }
        }

        return $valid;
    }

    public function logout($userId){
        $models = $this->findAllByAttributes(array('userId' => $userId));
        if(!empty($models)){
            foreach ($models as $model){
                $model->delete();
            }
        }
    }

    public function setDefaults(){
        $this->generateHash();

        if(empty($this->ipAddress)){
            $this->ipAddress = $_SERVER['REMOTE_ADDR'];
        }
    }
}
