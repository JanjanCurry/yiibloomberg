<?php

/**
 * This is the model class for table "db_user_activity".
 *
 * The followings are the available columns in table 'db_user_activity':
 * @property integer $userId
 * @property string  $ipAddress
 * @property integer $created
 */
class DbUserActivity extends ActiveRecord {
    /**
     * @return string the associated database table name
     */
    public function tableName () {
        return 'db_user_activity';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules () {
        return array(
            array('userId, ipAddress', 'required'),

            array('userId, created', 'numerical', 'integerOnly' => true),
            array('ipAddress', 'length', 'max' => 45),

            array('created', 'default', 'value' => time(), 'setOnEmpty' => false, 'on' => 'insert'),

            array('userId, ipAddress, created', 'safe', 'on' => 'search'),
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
    public function search ($options = null) {
        $criteria = new CDbCriteria;

        $criteria->compare('userId', $this->userId);
        $criteria->compare('ipAddress', $this->ipAddress, true);
        $criteria->compare('created', $this->created);

        return $this->formatSearch($this, $criteria, $options);
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return DbUserActivity the static model class
     */
    public static function model ($className = __CLASS__) {
        return parent::model($className);
    }

    public function add () {
        if (!empty(Yii::app()->user) && !empty(Yii::app()->user->id)) {
            $model = new DbUserActivity();
            $model->userId = Yii::app()->user->id;
            $model->ipAddress = $_SERVER['REMOTE_ADDR'];

            $criteria = new CDbCriteria();
            $criteria->compare('userId', $model->userId);
            $criteria->compare('ipAddress', $model->ipAddress);
            $criteria->compare('created', '>' . strtotime('- 3 hours'));
            $exists = DbUserActivity::model()->find($criteria);
            if (empty($exists)) {
                return $model->save();
            }
        }

        return false;
    }
}
