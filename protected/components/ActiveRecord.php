<?php class ActiveRecord extends CActiveRecord {

    public $logStatusChange = true; //boolean - manual override to stop the logging of status changes
    public $modelOld; //model copy of current database record
    public $search; //common attributes for search fields
    public $searchTerms = array(); //exploded versions of $this->search
    public $mailHash;

    public static function model ($className = __CLASS__) {
        return parent::model($className);
    }

    protected function beforeValidate () {
        if ($this->hasAttribute('addedById') && empty($this->addedById)
            && $this->scenario == 'insert'
            && !empty(Yii::app()->user) && !empty(Yii::app()->user->id)
        ) {
            $this->addedById = Yii::app()->user->id;
        }

        return parent::beforeValidate();
    }

    ///////////////////////////////////////
    // SAVE / VALIDATION
    ///////////////////////////////////////

    //custom save function, only save model if there is something new to save, otherwise ignore and fake a successful save
    public function save ($runValidation = true, $attributes = null) {
        //$this->beforeSave();
        if (!$runValidation || $this->validate($attributes)) {
            if ($this->getIsNewRecord()) {
                //clone model to modelOld and insert new record
                $class = get_class($this);
                $this->modelOld = new $class;

                return $this->insert($attributes);
            } else {
                //update modelOld with current db record
                if (empty($this->modelOld)) {
                    $this->modelOld = $this->findByPk($this->id);
                }

                //check attributes to see if db needs updated
                $change = false;
                if (empty($this->modelOld)) {
                    $change = true;
                } else {
                    foreach ($this->attributes as $name => $attribute) {
                        if ($this->modelOld->$name != $attribute) {
                            $change = true;
                            break;
                        }
                    }
                }

                //if change is needed then update otherwise fake a successful update
                if ($change) {
                    $return = $this->update($attributes);
                } else {
                    $return = true;
                    //$this->afterSave();
                }

                return $return;
            }
        } else {
            return false;
        }
    }

    protected function afterConstruct () {
        parent::afterConstruct();

        if(!empty($_REQUEST[get_class($this)]['search'])){
            $this->search = $_REQUEST[get_class($this)]['search'];
            $this->searchTerms = $this->explodeTerms($this->search);
        }

        $this->setPkName();
        $this->modelOld = clone $this;
    }

    //run these functions after retrieving record(s) from the database
    protected function afterFind () {
        parent::afterFind();
        $this->setPkName();
        $this->modelOld = clone $this;
    }

    public function convertTime ($attr, $convertTo = 'int', $strFormat = 'datetime') {
        $convertTo = ($convertTo == 'integer' ? 'int' : $convertTo);
        $convertTo = ($convertTo == 'string' ? 'str' : $convertTo);
        if (!empty($this->$attr)) {
            if ($convertTo == 'int' && !is_numeric($this->$attr)) {
                $this->$attr = strtotime(str_replace('/', '-', $this->$attr));
                //check if strtotime() formatted correctly
                if (empty($this->$attr) || !is_numeric($this->$attr) || $this->$attr < strtotime('1 Jan 2000')) {
                    $this->$attr = null;
                }
            } elseif ($convertTo == 'str' && is_numeric($this->$attr)) {
                if ($strFormat == 'date') {
                    $this->$attr = Yii::app()->format->date($this->$attr);
                } else {
                    $this->$attr = Yii::app()->format->datetime($this->$attr);
                }
            }
        }
    }


    ///////////////////////////////////////
    // MAIL HASH
    ///////////////////////////////////////

    public function findByMailHash ($action, $hash) {
        $temp = DbMailHash::Model()->findByAttributes(array(
            'action' => $action,
            'hash' => $hash,
        ));
        if (!empty($temp) && $temp->valid) {
            return $temp->ref;
        }

        return false;
    }

    public function setMailHash ($action) {
        $temp = DbMailHash::Model()->findByAttributes(array(
            'action' => $action,
            'refId' => $this->id,
            'refType' => get_class($this),
        ));
        if (!empty($temp) && $temp->valid) {
            $this->mailHash = $temp->hash;
        } else {
            $this->mailHash = DbMailHash::add($this, $action);
        }

        return $this->mailHash;
    }




    ///////////////////////////////////////
    // SEARCH
    ///////////////////////////////////////

    /*
     * @param Array $attrs - 1 dimension array of models attributes
     * @return CDbCriteria
     */
    public function addSearchTerms($baseCriteria,$attrs,$operator='AND') {
        $criteria = new CDbCriteria();
        if (!empty($this->searchTerms) && !empty($attrs)) {
            foreach ($attrs as $attr) {
                if (strpos($attr, '.')) {
                    $relations = explode('.', $attr);
                    if (!empty($relations)) {
                        $parent = '';
                        foreach ($relations as $i => $relation) {
                            if (!empty($relation) && $relation != 't' && array_key_exists($relation, $this->relations())) {
                                $criteria->with[] = $parent . $relation;
                                $parent .= $relation . '.';
                            }
                        }
                    }
                }
            }

            foreach ($this->searchTerms as $term) {
                foreach ($attrs as $attr) {
                    $criteria->compare($attr, $term, true, 'OR');
                }
            }
        }
        $baseCriteria->mergeWith($criteria,$operator);
        return $baseCriteria;
    }


    //explode public search terms. use different function if exploding only by specific character e.g. commas
    public function explodeTerms ($input) {
        $terms = array();
        if (!empty($input)) {
            //remove symbols
            $input = str_replace(array(
                ',',
                //'.',
                '&'
            ), ' ', $input);

            //remove double spacing
            $input = preg_replace('!\s+!', ' ', $input);

            $parts = explode(' ', $input);
            if (!empty($parts)) {
                $terms = $parts;
            }
        }

        return $terms;
    }

    /**
     * Return db results in different formats. typically used with $model->search()
     * @param ActiveRecord $model
     * @param CDbCriteria  $criteria
     * @param array        $options
     * @return mixed
     */
    public function formatSearch ($model, $criteria, $options = null) {
        $default = array(
            'returnFormat' => 'dataProvider', //dataProvider, criteria, array, single, criteria
            'paginationSize' => 25,
            'sortAttributes' => array('*'),
            'sortDefault' => '',
            'debug' => false,
            'debugDie' => false,
        );
        $options = Yii::app()->format->options($options, $default);

        $format = $options['returnFormat'];
        if (!empty($options['sort'])) {
            $criteria->order = $options['sort'];
        }
        if (!empty($options['limit'])) {
            $criteria->limit = $options['limit'];
        }
        if (!empty($options['group'])) {
            $criteria->group = $options['group'];
        }
        if (!empty($options['criteria'])) {
            $criteria->mergeWith($options['criteria']);
        }
        if (!empty($options['sortDefault']) && empty($criteria->order)) {
            $criteria->order = $options['sortDefault'];
        }

        switch ($format) {
            case 'array':
                $return = $model->findAll($criteria);
                break;
            case 'count':
                $return = $model->count($criteria);
                break;
            case 'criteria':
                $return = $criteria;
                break;
            case 'single':
                $return = $model->find($criteria);
                break;
            case 'dataProvider':
            default:
                if (!empty($options['paginationSize']) && $options['paginationSize'] == 'all') {
                    $options['paginationSize'] = $model->count($criteria);
                }
                $pagination = array(
                    'pageSize' => $options['paginationSize'],
                );
                if (!empty($options['limit'])) {
                    $pagination = false;
                }

                $return = new CActiveDataProvider($model, array(
                    'criteria' => $criteria,
                    'sort' => array(
                        'attributes' => $options['sortAttributes'],
                    ),
                    'pagination' => $pagination,
                ));
                break;
        }

        if ($options['debug']) {
            echo '<pre>' . var_dump($criteria) . '</pre>';
            echo '<pre>' . var_dump($return) . '</pre>';
        } elseif ($options['debugDie']) {
            echo '<pre>' . var_dump($criteria) . '</pre>';
            echo '<pre>' . var_dump($return) . '</pre>';
            exit;
        }

        return $return;
    }

    //encryption method for public use e.g. used for hashing links. Do not use for password encryption
    public static function encryption ($str) {
        $salt = Yii::app()->params['publicSalt'];
        $encryption = base64_encode(md5($salt . md5(hash('SHA512', $str . $salt) . $salt)));

        return $encryption;
    }


    ///////////////////////////////////////
    // FILES
    ///////////////////////////////////////

    /**
     * To be used as alternative to $media->delete() - deletes file from server before running $this->delete()
     * @return boolean
     */
    public function deleteFile () {
        if (!empty($this->filename) && file_exists($this->path . $this->filename) && is_file($this->path . $this->filename)) {
            unlink($this->path . $this->filename);
        }
        if (!empty($this->thumb) && file_exists($this->path . 'thumb/' . $this->filename) && is_file($this->path . 'thumb/' . $this->filename)) {
            unlink($this->path . 'thumb/' . $this->filename);
        }
        if (!empty($this->large) && file_exists($this->path . 'large/' . $this->filename) && is_file($this->path . 'large/' . $this->filename)) {
            unlink($this->path . 'large/' . $this->filename);
        }
        if ($this->delete()) {
            return true;
        }

        return false;
    }

    /**
     * set header for this related media file extension and present file for download
     */
    public function downloadFile () {
        if (!empty($this->filename) && file_exists($this->path . $this->filename) && is_file($this->path . $this->filename)) {
            header('Content-disposition: attachment; filename=' . $this->filename);
            $mime = CFileHelper::getMimeType($this->path . $this->filename);

            if (!empty($mime)) {
                header('Content-type: ' . $mime);
                readfile($this->path . $this->filename);
            } else {
                throw new CHttpException(404, 'Invalid file type.');
            }
        } else {
            throw new CHttpException(404, 'File not found.');
        }
    }

    /*
     * Once its gone, its gone, be very careful with this function. Recursively deletes directories and files
     * @param string $dir - path (including server root)
     * @param boolean $contentsOnly - dont delete the root dir, just delete the contents
     */
    public function deleteDirAndContents ($dir, $contentsOnly = false) {
        if (!empty($dir)) {
            foreach (glob($dir . '/*') as $file) {
                if (is_dir($file)) {
                    $this->deleteDirAndContents($file);
                } else {
                    //var_dump('Dir unlink: '.$file);
                    unlink($file);
                }
            }
            if (!$contentsOnly) {
                //var_dump('Dir rmdir: '.$dir);
                rmdir($dir);
            }
        }
    }


    ///////////////////////////////////////
    // COMMA SEPARATED
    ///////////////////////////////////////


    /**
     * switch comma separated attribute between array and string
     * @param string $attr - attribute name
     * @param string $format - string/array - format to convert the attribute to
     * @param mixed  $separator - character to separate data by
     */
    public function formatCommaSeparated ($attr, $format = 'string', $separator = ',') {
        if (empty($this->$attr)) {
            $this->$attr = array();
        }
        if ($format == 'string' && is_array($this->$attr)) {
            $result = array();
            foreach ($this->$attr as $key => $val) {
                if (!empty($val) || $val == 0) {
                    $result[] = trim($val);
                }
            }
            sort($result);
            $this->$attr = $separator . implode($separator, $result) . $separator;
            if ($this->$attr == $separator . $separator) {
                $this->$attr = '';
            }
        } elseif ($format == 'array' && !is_array($this->$attr)) {
            $result = array();
            $temp = explode($separator, str_replace(', ', ',', $this->$attr));
            foreach ($temp as $key => $val) {
                if (!empty($val) || $val === 0) {
                    $result[] = trim($val);
                }
            }
            sort($result);
            $this->$attr = $result;
        }
    }

    /**
     * add a single or multiple comma separates value, clean out any duplicates and if previously empty then also remove 'none' if present.
     * @param string $attr - attribute name
     * @param mixed  $values - data to add to the attribute
     * @return boolean
     */
    public function addCommaSeparated ($attr, $values) {
        if (!is_array($values)) {
            $values = array($values);
        }
        if (!is_array($this->$attr)) {
            $this->formatCommaSeparated($attr, 'array');
        }
        $temp = array();
        foreach ($this->$attr as $val) {
            if (!in_array($val, $temp) && $val != 'none') {
                $temp[] = $val;
            }
        }
        foreach ($values as $val) {
            if (!in_array($val, $temp)) {
                $temp[] = $val;
            }
        }
        sort($temp);
        $this->$attr = $temp;
    }

    /**
     * remove a single or multiple comma separates value, if all are removed then insert the account type 'none' if $allowEmpty is false
     * @param string  $attr - attribute name
     * @param mixed   $values - data to remove from the attribute
     * @param boolean $allowEmpty - allow array to be empty or include value 'none'
     */
    public function deleteCommaSeparated ($attr, $values, $allowEmpty = true) {
        if (!is_array($values)) {
            $values = array($values);
        }
        if (!is_array($this->$attr)) {
            $this->formatCommaSeparated($attr, 'array');
        }
        $temp = array();
        foreach ($this->$attr as $val) {
            if (!in_array($val, $temp) && !in_array($val, $values)) {
                $temp[] = $val;
            }
        }
        if (!$allowEmpty && empty($temp)) {
            //insert 'none' if no other account types are present
            $temp[] = 'none';
        }
        $this->$attr = $temp;
    }

    /*
     * array version of strpos()
     * @param CDbCriteria $criteria
     * @param string $attr
     * @param array $values
     * @return integer
     */
    public function compareCommaSeparated($criteria, $attr, $values=null){
        if (!is_array($this->$attr)) {
            $this->formatCommaSeparated($attr, 'array');
        }
        if(empty($values)){
            $values = $this->$attr;
        }
        if (!is_array($values)) {
            $values = array($values);
        }
        $temp = array();
        $criteria2 = new CDbCriteria();
        foreach ($values as $key => $val) {
            $criteria2->compare($attr, ','.$val.',', true, 'OR');
        }
        $criteria->mergeWith($criteria2);
        return $criteria;
    }

    /*
     * array version of strpos()
     * @param string $haystack
     * @param array $needles
     * @param integer $offset
     * @return integer
     */
    public function strposa ($haystack, $needles = array(), $offset = 0) {
        $chr = array();
        foreach ($needles as $needle) {
            $res = strpos($haystack, $needle, $offset);
            if ($res !== false) {
                $chr[$needle] = $res;
            }
        }
        if (empty($chr)) {
            return false;
        }

        return min($chr);
    }


    ///////////////////////////////////////
    // SERIALISED
    ///////////////////////////////////////


    /**
     * switch serialised attribute between array and string
     * @param string $attr - attribute name
     * @param string $format - string/array - format to convert the attribute to
     */
    public function formatSerialised ($attr, $format = 'string') {
        if (empty($this->$attr)) {
            $this->$attr = array();
        }
        if ($format == 'string' && is_array($this->$attr)) {
            $this->$attr = serialize($this->$attr);
        } elseif ($format == 'array' && !is_array($this->$attr)) {
            $temp = unserialize($this->$attr);
            $this->$attr = $temp;
        }
    }

    /**
     * add associative array values and clean out any duplicates
     * @param string  $attr - attribute name
     * @param array   $values - data to add to the attribute
     * @param boolean $overwriteExisting - true overwrites existing data and false only adds missing data
     * @return boolean
     */
    public function addSerialised ($attr, $values, $overwriteExisting = true) {
        if (!is_array($this->$attr)) {
            $this->formatSerialised($attr, 'array');
        }
        if (!is_array($values)) {
            $values = array($values);
        }
        $temp = array();
        foreach ($this->$attr as $key => $val) {
            if (!array_key_exists($key, $temp)) {
                $temp[$key] = $val;
            }
        }
        foreach ($values as $key => $val) {
            if ($overwriteExisting) {
                if (!array_key_exists($key, $temp) || (array_key_exists($key, $temp) && $temp[$key] != $val)) {
                    $temp[$key] = $val;
                }
            } else {
                if (!array_key_exists($key, $temp)) {
                    $temp[$key] = $val;
                }
            }
        }
        $this->$attr = $temp;
    }

    /**
     * remove associative array values
     * @param string $attr - attribute name
     * @param mixed  $values - array keys to remove from the attribute
     */
    public function deleteSerialised ($attr, $values) {
        if (!is_array($values)) {
            $values = array($values);
        }
        if (!is_array($this->$attr)) {
            $this->formatSerialised($attr, 'array');
        }
        $temp = array();
        foreach ($this->$attr as $key => $val) {
            if (!array_key_exists($key, $temp) && !in_array($key, $values)) {
                $temp[$key] = $val;
            }
        }
        $this->$attr = $temp;
    }

    ///////////////////////////////////////
    // LISTS
    ///////////////////////////////////////

    /**
     * get the full display name of a value used in a list function
     * @param string $listName - function name (that returns an array e.g. listSomething())
     * @param string $key - key within array returned from $listName
     * @return string
     */
    public function getListLabel ($listName, $key) {
        $label = '';
        $list = $this->$listName();
        if (!empty($list)) {
            foreach ($list as $itemKey => $itemVal) {
                if ($key == $itemKey) {
                    $label = $itemVal;
                }
            }
            if(empty($label)){
                foreach ($list as $itemKey => $itemVal) {
                    if(is_array($itemVal)) {
                        foreach ($itemVal as $itemKey2 => $itemVal2) {
                            if ($key == $itemKey2) {
                                $label = $itemVal2;
                            }
                        }
                    }
                }
            }
        }

        return $label;
    }

    public function getQuarter($month){
        $quarters = $this->listQuarterMonths();
        foreach($quarters as $key => $val){
            if(in_array($month,$val)){
                return $key;
            }
        }
    }

    public function listQuarterMonths(){
        return array(
            'Q1' => array('01', '02', '03'),
            'Q2' => array('04', '05', '06'),
            'Q3' => array('07', '08', '09'),
            'Q4' => array('10', '11', '12'),
        );
    }

    public $_pkName;
    public function getPkName () {
        return $this->_pkName;
    }
    public function getPkVal () {
        if(!empty($this->pkName)){
            $pkName = $this->pkName;
            return $this->$pkName;
        }
    }
    public function fetchPkName ($model) {
        $pkName = 'id';
        $list = array(
            //'DbReporters' => 'SLNO',
        );
        if(!empty($model)){
            if(is_object($model)){
                $model = get_class($model);
            }
            if(array_key_exists($model,$list)){
                $pkName = $list[$model];
            }
        }
        return $pkName;
    }
    public function setPkName () {
        $this->_pkName = $this->fetchPkName($this);
    }


    //alternative function for listing model errors
    public function listErrors () {
        $errors = 'No Errors';
        if (!empty($this->errors)) {
            $errors = '';
            foreach ($this->errors as $attr) {
                if (!empty($attr)) {
                    foreach ($attr as $error) {
                        $errors .= $error . '<br />';
                    }
                }
            }
        }

        return $errors;
    }

    public function listPaginationSize () {
        return array(
            1 => 1,
            10 => 10,
            25 => 25,
            50 => 50,
            100 => 100,
        );
    }

    public function listStatus () {
        return array(
            'active' => 'Active',
            'inactive' => 'Inactive',
        );
    }

    public function listYesNo () {
        return array(
            0 => 'No',
            1 => 'Yes',
        );
    }

    public function listBoolean () {
        return array(
            0 => 'No',
            1 => 'Yes',
        );
    }

    public function listOffOn () {
        return array(
            0 => 'Off',
            1 => 'On',
        );
    }

    public function generateRef ($prefix = '', $affix = '') {
        $ref = $prefix . rand(11111, 99999) . $affix;
        $exists = $this->findByAttributes(array(
            'ref' => $ref,
        ));
        if (!empty($exists)) {
            $ref = $this->generateRef($prefix, $affix);
        }

        return $ref;
    }

    public function scopes () {
        return array(
            'active' => array(
                'condition' => 't.status = "active"',
            ),
            'inactive' => array(
                'condition' => 't.status = "inactive"',
            ),
        );
    }

    public function fetchDiaryItems ($start = null, $end = null) {
        $items = array();
        if (isset($this->id)) {
            $criteria = new CDbCriteria();
            if(!empty($start)) {
                $criteria->compare('t.startTime', '>=' . strtotime($start));
            }
            if(!empty($end)) {
                $criteria->compare('t.startTime', '<=' . strtotime($end));
            }
            $criteria->compare('t.refId',$this->id);
            $criteria->compare('t.refType',get_class($this));

            $temp = DbDiary::model()->findAll($criteria);
            if (!empty($temp)) {
                $items = $temp;
            }
        }
        return $items;
    }

    public function renderDiaryItems ($start = null, $end = null) {
        $items = $this->fetchDiaryItems($start,$end);
        $items = DbDiary::model()->formatDiaryItems($items);
        echo CJSON::encode($items);
    }

}