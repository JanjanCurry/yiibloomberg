<?php

/**
 * This is the model class for table "db_mail_outbox".
 *
 * The followings are the available columns in table 'db_mail_outbox':
 * @property integer $id
 * @property string $status
 * @property string $sendTo
 * @property string $sendCc
 * @property string $sendBcc
 * @property string $sendFrom
 * @property string $subject
 * @property string $body
 * @property string $attach
 * @property integer $runTime
 * @property integer $sentTime
 * @property integer $addedById
 * @property integer $updated
 * @property integer $created
 */
class DbMailOutbox extends ActiveRecord
{

    protected function afterConstruct() {
        parent::afterConstruct();
        $this->formatSerialised('attach', 'array');
    }

    protected function afterFind() {
        parent::afterFind();
        $this->formatSerialised('attach', 'array');
    }

    protected function beforeValidate() {
        $this->setDefaults();
        $this->convertTime('runTime','integer');
        $this->convertTime('sentTime','integer');
        $this->formatSerialised('attach', 'string');
        return parent::beforeValidate();
    }

    protected function afterValidate() {
        parent::afterValidate();
        $this->formatSerialised('attach', 'array');
    }

    protected function beforeSave() {
        $this->formatSerialised('attach', 'string');
        return parent::beforeSave();
    }

    protected function afterSave() {
        parent::afterSave();
        $this->formatSerialised('attach', 'array');
    }

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'db_mail_outbox';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('status, sendTo, sendFrom, subject, body, runTime', 'required'),

            array('addedById, updated, created', 'numerical', 'integerOnly'=>true),
            array('status', 'length', 'max'=>45),
            array('sendTo, sendFrom, subject', 'length', 'max'=>255),
            array('sendCc, sendBcc, attach', 'safe'),

            array('updated', 'default', 'value' => time(), 'setOnEmpty' => false, 'on' => 'update'),
            array('updated, created', 'default', 'value' => time(), 'setOnEmpty' => false, 'on' => 'insert'),

            array('id, status, sendTo, sendCc, sendBcc, sendFrom, subject, body, attach, runTime, sentTime, addedById, updated, created', 'safe', 'on'=>'search'),
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
            'addedBy' => array(self::BELONGS_TO, 'DbUser', 'addedById'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'status' => 'Status',
            'sendTo' => 'To',
            'sendCc' => 'CC',
            'sendBcc' => 'BCC',
            'sendFrom' => 'From',
            'subject' => 'Subject',
            'body' => 'Content',
            'attach' => 'Attach',
            'runTime' => 'Send Time',
            'sentTime' => 'Sent Time',
            'addedById' => 'Added By',
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
    public function search($options = null) {

        $criteria=new CDbCriteria;

        $criteria->compare('id',$this->id);
        $criteria->compare('status',$this->status);
        $criteria->compare('sendTo',$this->sendTo,true);
        $criteria->compare('sendCc',$this->sendCc,true);
        $criteria->compare('sendBcc',$this->sendBcc,true);
        $criteria->compare('sendFrom',$this->sendFrom,true);
        $criteria->compare('subject',$this->subject,true);
        $criteria->compare('body',$this->body,true);
        $criteria->compare('attach',$this->attach,true);
        $criteria->compare('runTime',$this->runTime);
        $criteria->compare('sentTime',$this->sentTime);
        $criteria->compare('addedById',$this->addedById);
        $criteria->compare('updated',$this->updated);
        $criteria->compare('created',$this->created);

        $options['sortDefault'] = 't.runTime DESC';

        $searchAttrs = array(
            't.sendTo',
            't.sendCc',
            't.sendBcc',
            't.sendFrom',
            't.subject',
        );
        $criteria = $this->addSearchTerms($criteria, $searchAttrs);

        return $this->formatSearch($this, $criteria, $options);
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return DbMailOutbox the static model class
     */
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    public function gridEmails(){
        $html = '';
        $attrs = array(
            'sendTo' => 'to',
            'sendCc' => 'cc',
            'sendBcc' => 'bcc',
            'sendFrom' => 'from',
        );
        foreach($attrs as $attr => $label){
            if(!empty($this->$attr)){
                if(!empty($html)){
                    $html .= '<br />';
                }
                $html .= $label.': '.$this->$attr;
            }
        }
        return $html;
    }

    public function gridTimes(){
        $html = '';
        $attrs = array(
            'sentTime' => 'sent',
            'runTime' => 'send',
            //'created' => 'added',
        );
        foreach($attrs as $attr => $label){
            if(!empty($this->$attr)){
                if(!empty($html)){
                    $html .= '<br />';
                }
                $this->convertTime($attr);
                $html .= $label.': '.Yii::app()->format->datetime($this->$attr);
            }
        }
        return $html;
    }

    public function listStatus () {
        return array(
            'pending' => 'Pending',
            'sent' => 'Sent',
            'failed' => 'Failed',
        );
    }

    public  function setDefaults(){
        if(empty($this->sendFrom)){
            $this->sendFrom = 'no-reply@buy2let.scot';
        }

        if(empty($this->runTime)){
            $this->runTime = '+3 min';
        }

        if(empty($this->status)){
            $this->status = 'pending';
        }
    }
}
