<?php

/**
 * This is the model class for table "db_message".
 *
 * The followings are the available columns in table 'db_message':
 * @property integer $id
 * @property integer $created
 * @property integer $updated
 * @property integer $userId
 * @property integer $ref
 * @property integer $status
 * @property string $subject
 * @property string  $message
 */
class DbMessage extends ActiveRecord {

    protected function beforeValidate () {
        $this->setDefaults();

        return parent::beforeValidate();
    }

    /**
     * @return string the associated database table name
     */
    public function tableName () {
        return 'db_message';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules () {
        return array(
            array('userId, ref, status, message', 'required'),

            array('created, updated, userId, ref, status', 'numerical', 'integerOnly' => true),
            array('subject', 'length', 'max'=>255),

            array('updated', 'default', 'value' => time(), 'setOnEmpty' => false),
            array('created', 'default', 'value' => time(), 'setOnEmpty' => false, 'on' => 'insert'),

            array('message', 'safe'),
            array('id, created, updated, userId, ref, status, subject, message', 'safe', 'on' => 'search'),
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
            'created' => 'Created',
            'updated' => 'Updated',
            'userId' => 'User',
            'ref' => 'Reference',
            'status' => 'Status',
            'subject' => 'Subject',
            'message' => 'Message',
        );
    }

    /**
     * @param array $options formatSearch() options
     * @return CActiveDataProvider
     */
    public function search ($options = null) {
        $criteria = new CDbCriteria();
        $criteria->with[] = 'user';

        $criteria->compare('t.id', $this->id);
        $criteria->compare('t.created', $this->created);
        $criteria->compare('t.updated', $this->updated);
        $criteria->compare('t.userId', $this->userId);
        $criteria->compare('t.ref', $this->ref);
        $criteria->compare('t.status', $this->status);
        $criteria->compare('t.message', $this->message, true);

        $options['sortDefault'] = 't.created DESC';

        $searchAttrs = array(
            't.message',
            't.ref',
            'user.fName',
            'user.sName',
            'user.email',
        );
        $criteria = $this->addSearchTerms($criteria, $searchAttrs);

        return $this->formatSearch($this, $criteria, $options);
    }

    /**
     * @param string $className active record class name.
     * @return DbMessage the static model class
     */
    public static function model ($className = __CLASS__) {
        return parent::model($className);
    }

    public function listStatus () {
        return [
            1 => 'Unseen',
            2 => 'Seen',
            3 => 'Read',
        ];
    }

    public function setDefaults () {
        if (empty($this->status)) {
            $this->status = 1;
        }

        if (empty($this->ref)) {
            $this->ref = $this->generateRef();
        }
    }
}
