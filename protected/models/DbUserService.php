<?php

/**
 * This is the model class for table "db_user_service".
 *
 * The followings are the available columns in table 'db_user_service':
 * @property integer $id
 * @property integer $userId
 * @property integer $created
 * @property integer $updated
 * @property string  $tool
 * @property string  $level
 * @property integer $expire
 *
 * @property DbUser  $user
 */
class DbUserService extends ActiveRecord {

    protected function beforeValidate () {
        $this->convertTime('expire', 'int');

        return parent::beforeValidate();
    }

    /**
     * @return string the associated database table name
     */
    public function tableName () {
        return 'db_user_service';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules () {
        return array(
            array('userId, tool, level', 'required'),

            array('userId, created, updated, expire', 'numerical', 'integerOnly' => true),
            array('tool, level', 'length', 'max' => 20),

            array('updated', 'default', 'value' => time(), 'setOnEmpty' => false),
            array('created', 'default', 'value' => time(), 'setOnEmpty' => false, 'on' => 'insert'),

            array('id, userId, created, updated, tool, level, expire', 'safe', 'on' => 'search'),
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
            'userId' => 'User',
            'created' => 'Date Added',
            'updated' => 'Date Edited',
            'tool' => 'Tool',
            'level' => 'Service Level',
            'expire' => 'Expiry Date',
        );
    }

    /**
     * @param array $options formatSearch() options
     * @return CActiveDataProvider
     */
    public function search ($options = null) {
        $criteria = new CDbCriteria;

        $criteria->compare('t.id', $this->id);
        $criteria->compare('t.userId', $this->userId);
        $criteria->compare('t.created', $this->created);
        $criteria->compare('t.updated', $this->updated);
        $criteria->compare('t.tool', $this->tool);
        $criteria->compare('t.level', $this->level);
        $criteria->compare('t.expire', $this->expire);

        $options['sortDefault'] = 't.id ASC';

        $sort = 'CASE ';
        foreach (array_keys($this->listLevel()) as $i => $key) {
            $sort .= ' WHEN t.level = "' . $key . '" THEN ' . ($i + 1);
        }
        $sort .= ' END ASC';

        $options['sortDefault'] = 't.tool ASC, ' . $sort . ', t.expire DESC, t.created DESC';

        $searchAttrs = array();
        $criteria = $this->addSearchTerms($criteria, $searchAttrs);

        return $this->formatSearch($this, $criteria, $options);
    }

    /**
     * @param string $className active record class name.
     * @return DbUserService the static model class
     */
    public static function model ($className = __CLASS__) {
        return parent::model($className);
    }

    public static function add ($userId, $tool, $level, $expire = null) {
        $return = false;

        if (empty($model)) {
            $model = new DbUserService();
            $model->userId = $userId;
            $model->tool = $tool;
            $model->level = $level;
        }

        $model->expire = $expire;

        if ($model->save()) {
            $model->user->updateRoles();
            $model->user->setDefaultFavorites($tool);
            $return = $model;
        }

        return $return;
    }

    public function calcLevel ($tool, $userId = null) {
        $userId = (empty($userId) ? $this->userId : $userId);
        $return = null;

        if (!empty($userId)) {
            $criteria = new CDbCriteria();
            $criteria->select = 'expire, level';
            $criteria->compare('userId', $userId);
            $criteria->compare('tool', $tool);

            $sort = 'CASE ';
            foreach (array_keys($this->listLevel()) as $i => $key) {
                $sort .= ' WHEN t.level = "' . $key . '" THEN ' . ($i + 1);
            }
            $sort .= ' END DESC';
            $criteria->order = $sort;

            $models = DbUserService::model()->findAll($criteria);
            if (!empty($models)) {
                foreach ($models as $model) {
                    if (empty($model->expire) || $model->expire > time()) {
                        $return = $model->level;
                        break;
                    }
                }
            }
        }

        return $return;
    }

    public static function convertTool ($type) {
        $list = array(
            'trade' => 'tra',
            'macro' => 'tra',
            'commodity' => 'com',
            'currency' => 'cur',
            'equity' => 'equ',
        );
        if (!empty($list[$type])) {
            return $list[$type];
        }

        return null;
    }

    public function gridTool ($userId) {
        $return = [];
        foreach ($this->listTool() as $key => $val) {
            $level = $this->calcLevel($key, $userId);

            switch ($level) {
                case 'ess':
                    $color = 'text-warning';
                    break;
                case 'pro':
                    $color = 'text-primary';
                    break;
                case 'ent':
                    $color = 'text-success';
                    break;
                default:
                    $color = 'text-white';
                    break;
            }

            if (empty($level)) {
                $level = 'Off';
            } else {
                $level = $this->getListLabel('listLevel', $level);
            }
            $icon = $this->getListLabel('listIcon', $key);


            //$return[] = $val.': '.$level;

            $return[] = ' <span class="fa ' . $icon . ' ' . $color . '" data-toggle="tooltip" title="' . $val . ': ' . $level . '"></span>';
        }

        return implode('', $return);
    }

    public function isPromo ($tool, $userId = null) {
        $userId = (empty($userId) ? $this->userId : $userId);
        $return = false;

        if (!empty($userId)) {
            $criteria = new CDbCriteria();
            $criteria->select = 'expire, level';
            $criteria->compare('userId', $userId);
            $criteria->compare('tool', $tool);
            $models = DbUserService::model()->findAll($criteria);
            if (!empty($models)) {
                $hasNorm = $hasPromo = false;
                $lvls = $this->listLevelOrder();
                $lvl = 0;
                foreach ($models as $model) {
                    if (isset($lvls[$model->level])) {
                        if ($lvls[$model->level] > $lvl) {
                            $lvl = $lvls[$model->level];
                            $hasNorm = $hasPromo = false;
                        }

                        if ($lvl >= $lvls[$model->level]) {
                            if (!empty($model->expire) && $model->expire > time()) {
                                $hasPromo = true;
                                break;
                            } else if (empty($model->expire)) {
                                $hasNorm = true;
                            }
                        }
                    }
                }

                if ($hasPromo && !$hasNorm) {
                    $return = true;
                }
            }
        }

        return $return;
    }

    public function listIcon () {
        return [
            'com' => 'fa-tint',
            'cur' => 'fa-usd',
            'equ' => 'fa-balance-scale',
            'tra' => 'fa-exchange',
        ];
    }

    public function listLevel () {
        return [
            'ess' => 'Essentials',
            'pro' => 'Pro',
            //'bus' => 'Business',
            'ent' => 'Enterprise',
        ];
    }

    public function listLevelOrder () {
        return [
            'ess' => 1,
            'pro' => 2,
            //'bus' => 3,
            'ent' => 4,
        ];
    }

    public function listTool () {
        return [
            'com' => 'Commodities',
            'cur' => 'Currencies',
            'equ' => 'Equities',
            'tra' => 'Trade',
        ];
    }


}
