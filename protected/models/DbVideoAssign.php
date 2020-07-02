<?php

/**
 * This is the model class for table "db_video_assign".
 *
 * The followings are the available columns in table 'db_video_assign':
 * @property integer $id
 * @property integer $created
 * @property integer $updated
 * @property integer $videoId
 * @property integer $catId
 */
class DbVideoAssign extends ActiveRecord {
    /**
     * @return string the associated database table name
     */
    public function tableName () {
        return 'db_video_assign';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules () {
        return array(
            array('videoId, catId', 'required'),

            array('created, updated, videoId, catId', 'numerical', 'integerOnly' => true),

            array('updated', 'default', 'value' => time(), 'setOnEmpty' => false),
            array('created', 'default', 'value' => time(), 'setOnEmpty' => false, 'on' => 'insert'),

            array('id, created, updated, videoId, catId', 'safe', 'on' => 'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations () {
        return array();
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels () {
        return array(
            'id' => 'ID',
            'created' => 'Created',
            'updated' => 'Updated',
            'videoId' => 'Video',
            'catId' => 'Cat',
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
        $criteria->compare('t.videoId', $this->videoId);
        $criteria->compare('t.catId', $this->catId);

        $options['sortDefault'] = 't.id ASC';

        $searchAttrs = array();
        $criteria = $this->addSearchTerms($criteria, $searchAttrs);

        return $this->formatSearch($this, $criteria, $options);
    }

    /**
     * @param string $className active record class name.
     * @return DbVideoAssign the static model class
     */
    public static function model ($className = __CLASS__) {
        return parent::model($className);
    }

    public static function assign($videoId, $catId) {
        $exists = DbVideoAssign::Model()->findByAttributes(array(
            'videoId' => $videoId,
            'catId' => $catId,
        ));
        if (empty($exists)) {
            $assign = new DbVideoAssign();
            $assign->videoId = $videoId;
            $assign->catId = $catId;
            if (!$assign->save()) {
                return false;
            }
        }

        return true;
    }

    public static function unassign($videoId, $catId) {
        $return = DbVideoAssign::model()->deleteAllByAttributes(array(
            'videoId' => $videoId,
            'catId' => $catId,
        ));

        return $return;
    }

    public static function unassignAll($videoId = null, $catId = null) {
        if(!empty($videoId) && empty($catId)) {
            return DbVideoAssign::model()->deleteAllByAttributes(array(
                'videoId' => $videoId,
            ));
        }

        if(empty($videoId) && !empty($catId)) {
            return DbVideoAssign::model()->deleteAllByAttributes(array(
                'catId' => $catId,
            ));
        }
    }
}
