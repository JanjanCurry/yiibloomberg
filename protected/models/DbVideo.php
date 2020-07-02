<?php

/**
 * This is the model class for table "db_video".
 *
 * The followings are the available columns in table 'db_video':
 * @property integer $id
 * @property integer $created
 * @property integer $updated
 * @property string  $video
 * @property string  $title
 * @property string  $description
 */
class DbVideo extends ActiveRecord {

    private $_catIds;

    /**
     * @return string the associated database table name
     */
    public function tableName () {
        return 'db_video';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules () {
        return array(
            array('video', 'required'),

            array('created, updated', 'numerical', 'integerOnly' => true),
            array('video', 'length', 'max' => 20),
            array('title', 'length', 'max' => 255),
            array('video', 'unique'),

            array('updated', 'default', 'value' => time(), 'setOnEmpty' => false),
            array('created', 'default', 'value' => time(), 'setOnEmpty' => false, 'on' => 'insert'),

            array('description, catIds', 'safe'),
            array('id, created, updated, video, title, description', 'safe', 'on' => 'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations () {
        return array(
            'assigns' => array(self::HAS_MANY, 'DbVideoAssign', 'videoId'),
            'cats' => array(self::HAS_MANY, 'DbVideoCat', array('catId' => 'id'), 'through' => 'assigns'),
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
            'video' => 'YouTube Video ID',
            'title' => 'Title',
            'description' => 'Description',
            'catIds' => 'Categories',
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
        $criteria->compare('t.video', $this->video, true);
        $criteria->compare('t.title', $this->title, true);
        $criteria->compare('t.description', $this->description, true);

        $options['sortDefault'] = 't.id ASC';

        $searchAttrs = array();
        $criteria = $this->addSearchTerms($criteria, $searchAttrs);

        return $this->formatSearch($this, $criteria, $options);
    }

    /**
     * @param string $className active record class name.
     * @return DbVideo the static model class
     */
    public static function model ($className = __CLASS__) {
        return parent::model($className);
    }

    public function assignCats () {
        $existing = [];
        if (!empty($this->cats)) {
            foreach ($this->cats as $cat) {
                $existing[] = $cat->id;
            }
        }

        if(empty($this->catIds)){
            DbVideoAssign::unassignAll($this->id);
        }else{
            foreach($this->catIds as $id){
                if(!in_array($id, $existing)){
                    DbVideoAssign::assign($this->id, $id);
                }
            }
            foreach($existing as $id){
                if(!in_array($id, $this->catIds)){
                    DbVideoAssign::unassign($this->id, $id);
                }
            }
        }
    }

    public function getCatIds () {
        if (empty($this->_catIds)) {
            $list = [];
            if (!empty($this->cats)) {
                foreach ($this->cats as $cat) {
                    $list[] = $cat->id;
                }
            }
            $this->_catIds = $list;
        }

        return $this->_catIds;
    }

    public function setCatIds($val){
        $this->_catIds = $val;
    }
}
