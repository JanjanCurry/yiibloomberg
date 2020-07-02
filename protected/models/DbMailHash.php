<?php

/**
 * This is the model class for table "db_mail_hash".
 *
 * The followings are the available columns in table 'db_mail_hash':
 * @property string $hash
 * @property integer $refId
 * @property string $refType
 * @property string $action
 * @property string $type
 * @property integer $expire
 * @property integer $updated
 * @property integer $created
 */
class DbMailHash extends ActiveRecord
{

    protected function beforeValidate(){
        $this->setDefaults();
        return parent::beforeValidate();
    }

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'db_mail_hash';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('hash, refId, refType, action', 'required'),

            array('refId, expire, updated, created', 'numerical', 'integerOnly'=>true),
            array('refType, action, type', 'length', 'max'=>255),

            array('hash','unique'),

            array('updated', 'default', 'value' => time(), 'setOnEmpty' => false, 'on' => 'update'),
            array('updated, created', 'default', 'value' => time(), 'setOnEmpty' => false, 'on' => 'insert'),

            array('hash, refId, refType, action, type, expire, updated, created', 'safe', 'on'=>'search'),
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
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'hash' => 'Hash',
            'refId' => 'Ref',
            'refType' => 'Ref Type',
            'action' => 'Action',
            'expire' => 'Expire',
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
    public function search($options = null)
    {
        $criteria = new CDbCriteria;

        $criteria->compare('hash',$this->hash);
        $criteria->compare('refId',$this->refId);
        $criteria->compare('refType',$this->refType);
        $criteria->compare('action',$this->action);
        $criteria->compare('type',$this->type);
        $criteria->compare('expire',$this->expire);
        $criteria->compare('updated',$this->updated);
        $criteria->compare('created',$this->created);

        return $this->formatSearch($this, $criteria, $options);
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return DbMailHash the static model class
     */
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    public static function add($ref,$action,$attrs=array()){
        $model = new DbMailHash();
        if(!empty($attrs)){
            $model->attributes = $attrs;
        }
        $model->refId = $ref->id;
        $model->refType = get_class($ref);
        $model->action = $action;

        $exists = DbMailHash::model()->findByAttributes(array(
            'refId' => $model->refId,
            'refType' => $model->refType,
            'action' => $model->action,
            'type' => $model->type,
        ));
        if(!empty($exists)){
            $model = $exists;
            $model->updated = time();
            if(!empty($attrs['expire'])){
                $model->expire = $attrs['expire'];
            }
        }

        if($model->save()) {
            return $model;
        }
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

            $exists = DbMailHash::model()->findByAttributes(array('hash'=>$this->hash));
            if(!empty($exists)){
                $this->hash = null;
                $count++;
                $this->generateHash($count);
            }
            return true;
        }
        return false;
    }

    public $_ref;

    public function getRef() {
        if (empty($this->_ref) && !empty($this->refType) && class_exists($this->refType)) {
            $model = new $this->refType;
            $this->_ref = $model->findByPk($this->refId);
        }

        return $this->_ref;
    }

    public function setDefaults(){
        $this->generateHash();
    }

    public function validateExpiry(){
        if(!empty($this->expire) && $this->expire < time()){
            return true;
        }
        return false;
    }
}
