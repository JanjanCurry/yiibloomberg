<?php

/**
 * This is the model class for table "db_mail_trigger".
 *
 * The followings are the available columns in table 'db_mail_trigger':
 * @property integer $id
 * @property string $status
 * @property string $name
 * @property string $ref
 * @property string $group
 * @property string $viewFile
 * @property string $subject
 * @property string $sendTo
 * @property string $sendCc
 * @property string $sendBcc
 * @property string $sendFrom
 * @property string $attach
 * @property string $timeDelay
 * @property integer $marketing
 * @property integer $updated
 * @property integer $created
 */
class DbMailTrigger extends ActiveRecord
{

    protected function beforeValidate() {
        $this->setDefaults();
        return parent::beforeValidate();
    }

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'db_mail_trigger';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('status, name, ref, viewFile, subject', 'required'),

            array('marketing, updated, created', 'numerical', 'integerOnly'=>true),
            array('status, name, ref, group, viewFile', 'length', 'max'=>45),
            array('subject, sendFrom, timeDelay', 'length', 'max'=>255),
            array('sendCc, sendBcc, attach', 'safe'),

            array('updated', 'default', 'value' => time(), 'setOnEmpty' => false, 'on' => 'update'),
            array('updated, created', 'default', 'value' => time(), 'setOnEmpty' => false, 'on' => 'insert'),

            array('sendTo, sendCc, sendBcc, attach', 'safe'),
            array('id, status, name, ref, group, viewFile, subject, sendTo, sendCc, sendBcc, sendFrom, attach, timeDelay, marketing, updated, created', 'safe', 'on'=>'search'),
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
            'id' => 'ID',
            'status' => 'Status',
            'name' => 'Name',
            'ref' => 'Reference',
            'group' => 'Group',
            'viewFile' => 'View File',
            'subject' => 'Subject',
            'sendTo' => 'Send To',
            'sendCc' => 'Send Cc',
            'sendBcc' => 'Send Bcc',
            'sendFrom' => 'Send From',
            'attach' => 'Attachments',
            'timeDelay' => 'Time Delay',
            'marketing' => 'Contains Marketing',
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

        $criteria->compare('t.id',$this->id);
        $criteria->compare('t.status',$this->status);
        $criteria->compare('t.name',$this->name,true);
        $criteria->compare('t.ref',$this->ref);
        $criteria->compare('t.group',$this->group);
        $criteria->compare('t.viewFile',$this->viewFile);
        $criteria->compare('t.subject',$this->subject,true);
        $criteria->compare('t.sendTo',$this->sendTo,true);
        $criteria->compare('t.sendCc',$this->sendCc,true);
        $criteria->compare('t.sendBcc',$this->sendBcc,true);
        $criteria->compare('t.sendFrom',$this->sendFrom,true);
        $criteria->compare('t.attach',$this->attach,true);
        $criteria->compare('t.timeDelay',$this->timeDelay,true);
        $criteria->compare('t.marketing',$this->marketing);
        $criteria->compare('t.updated',$this->updated);
        $criteria->compare('t.created',$this->created);

        $options['sortDefault'] = 't.group ASC, t.name ASC, t.status ASC, t.ref ASC';

        $searchAttrs = array(
            't.name',
            't.ref',
            't.group',
            't.viewFile',
            't.subject',
            't.sendTo',
            't.sendCc',
            't.sendBcc',
            't.sendFrom',
        );
        $criteria = $this->addSearchTerms($criteria, $searchAttrs);

        return $this->formatSearch($this, $criteria, $options);
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return DbMailTrigger the static model class
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

    public function listGroup(){
        $list = array();
        $models = $this->findAll(array('group'=>'t.group'));
        if(!empty($models)){
            foreach ($models as $model){
                $list[$model->group] = $model->group;
            }
        }
        return $list;
    }

    public  function setDefaults(){
        if(empty($this->status)){
            $this->status = 'active';
        }

        $this->attach = str_replace(' ','',$this->attach);
        $this->viewFile = str_replace(' ','',$this->viewFile);
    }

}
