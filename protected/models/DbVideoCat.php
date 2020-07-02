<?php

/**
 * This is the model class for table "db_video_cat".
 *
 * The followings are the available columns in table 'db_video_cat':
 * @property integer $id
 * @property integer $created
 * @property integer $updated
 * @property string  $name
 * @property integer $orderId
 */
class DbVideoCat extends ActiveRecord {
    /**
     * @return string the associated database table name
     */
    public function tableName () {
        return 'db_video_cat';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules () {
        return array(
            array('name', 'required'),

            array('created, updated, orderId', 'numerical', 'integerOnly' => true),
            array('name', 'length', 'max' => 255),
            array('name', 'unique'),

            array('updated', 'default', 'value' => time(), 'setOnEmpty' => false),
            array('created', 'default', 'value' => time(), 'setOnEmpty' => false, 'on' => 'insert'),

            array('id, created, updated, name, orderId', 'safe', 'on' => 'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations () {
        return array(
            'assigns' => array(self::HAS_MANY, 'DbVideoAssign', 'catId'),
            'videos' => array(self::HAS_MANY, 'DbVideo', array('videoId' => 'id'), 'through' => 'assigns'),
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
            'name' => 'Name',
            'orderId' => 'Sort Order',
        );
    }

    /**
     * @param array $options formatSearch() options
     * @return CActiveDataProvider
     */
    public function search ($options = null) {
        $criteria = new CDbCriteria;

        $criteria->compare('t.id', $this->id);
        $criteria->compare('t.created', $this->created);
        $criteria->compare('t.updated', $this->updated);
        $criteria->compare('t.name', $this->name, true);
        $criteria->compare('t.orderId', $this->orderId);

        $options['sortDefault'] = 't.id ASC';

        $searchAttrs = array();
        $criteria = $this->addSearchTerms($criteria, $searchAttrs);

        return $this->formatSearch($this, $criteria, $options);
    }

    /**
     * @param string $className active record class name.
     * @return DbVideoCat the static model class
     */
    public static function model ($className = __CLASS__) {
        return parent::model($className);
    }

    public function listCategory(){
        $list = [];
        $models = DbVideoCat::model()->findAll();
        if(!empty($models)){
            foreach($models as $model){
                $list[$model->id] = $model->name;
            }
        }

        return $list;
    }
}
