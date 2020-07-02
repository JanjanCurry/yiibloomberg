<?php class DbVar extends ActiveRecord {

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


    public function tableName () {
        return 'db_var';
    }

    public function rules () {
        return array(
            array('updated, created', 'numerical', 'integerOnly' => true),
            array('name, type', 'length', 'max' => 255),
            array('updated', 'default', 'value' => time(), 'setOnEmpty' => false, 'on' => 'update'),
            array('updated,created', 'default', 'value' => time(), 'setOnEmpty' => false, 'on' => 'insert'),
            array('data', 'safe'),
            array('id, name, type, data, updated, created', 'safe', 'on' => 'search'),
        );
    }

    public function relations () {
        return array();
    }

    public function attributeLabels () {
        return array(
            'id' => 'ID',
            'name' => 'Name',
            'type' => 'Type',
            'data' => 'Data',
            'updated' => 'Updated',
            'created' => 'Created',
        );
    }


    public function search ($options = null) {
        $criteria = new CDbCriteria;

        $criteria->compare('id', $this->id);
        $criteria->compare('name', $this->name);
        $criteria->compare('type', $this->type);
        $criteria->compare('data', $this->data);
        $criteria->compare('updated', $this->updated);
        $criteria->compare('created', $this->created);

        return $this->formatSearch($this, $criteria, $options);
    }

    public static function model ($className = __CLASS__) {
        return parent::model($className);
    }

    public static function add($name, $type, $data){
        $attrs = [
            'name' => $name,
            'type' => $type,
        ];
        $model = DbVar::model()->findByAttributes($attrs);
        if(empty($model)){
           $model = new DbVar();
           $model->name = $name;
           $model->type = $type;
        }
        $model->data = $data;

        return ($model->save() ? $model : false);
    }

    public function setDefaults () {
        if (empty($this->type) && !empty($this->name)) {
            $this->type = $this->name;
        } else if (empty($this->name) && !empty($this->type)) {
            $this->name = $this->type;
        }
    }

}
