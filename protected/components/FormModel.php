<?php class FormModel extends CFormModel{

    public $pdf; //process pdf flag
    public $excel; //process excel flag
    public $print; //show printer friendly version flag

    public $endDate;
    public $endTime;
    public $scale;
    public $startDate;
    public $startTime;

    public $settings;
    protected $_isStaff;
    protected $_isStaffId;

    protected function afterConstruct(){
        parent::afterConstruct();

        $this->scale = 'month';
        $this->startDate = '1 '.date('M Y', strtotime('-4 months'));
        $this->endDate = date('t M Y');
        $this->settings = array();
    }

    public static function model($className = __CLASS__) {
        return new $className;
    }

    ///////////////////////////////////////
    // MISC OPERATIONS
    ///////////////////////////////////////

    public function isStaff($id=null){
        //preserve results so we dont have to rerun them when accessing this function from a loop
        if($this->_isStaffId != $id  || is_null($this->_isStaff)){
            $this->_isStaffId = $id;
            $this->_isStaff = DbUser::model()->isStaff($id);
        }
        return $this->_isStaff;
    }

    //alternative function for listing model errors
    public function listErrors(){
        $errors = 'No Errors';
        if(!empty($this->errors)){
            $errors = '';
            foreach($this->errors as $attr){
                if(!empty($attr)){
                    foreach($attr as $error){
                        $errors .= $error.'<br />';
                    }
                }
            }
        }
        return $errors;
    }

    public function removeZeros($results){
        if(!empty($results)){
            foreach($results as $key1=>$val1){
                if(!empty($val1)){
                    if(is_array($val1)){
                        foreach($val1 as $key2=>$val2){
                            if(is_numeric($val2) && $val2 == 0){
                                $results[$key1][$key2] = '-';
                            }
                        }
                    }elseif(is_numeric($val1) && $val1 == 0){
                        $results[$key1] = '-';
                    }
                }
            }
        }
        return $results;
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
    public function getListLabel($listName, $key){
        $label = '';
        $list = $this->$listName();
        if(!empty($list)){
            foreach($list as $itemKey=>$itemVal){
                if($key == $itemKey){
                    $label = $itemVal;
                }
            }
        }
        return $label;
    }

    public function listAgent(){
        $list = array();
        $users = DbUser::Model()->active()->agents()->findAll();
        if(!empty($users)){
            foreach($users as $user){
                $list[$user->id] = $user->fullName;
            }
        }
        asort($list);
        return $list;
    }

    public function listScale(){
        return array(
            'day' => 'Day',
            'week' => 'Week',
            'month' => 'Month',
            'year' => 'Year',
        );
    }

    public function listStatus(){
        return array(
            'active' => 'Active',
            'inactive' => 'Inactive',
        );
    }

    public function listYesNo(){
        return array(
            0 => 'No',
            1 => 'Yes',
        );
    }

    public function listBoolean(){
        return array(
            0 => 'No',
            1 => 'Yes',
        );
    }

    public function listOffOn(){
        return array(
            0 => 'Off',
            1 => 'On',
        );
    }

    ///////////////////////////////////////
    // SETTING / OPTIONS
    ///////////////////////////////////////

    /*
     * format date range scale
     * @param array $settings - e.g. $this->settings
     * @return array
     */
    protected function setDateFormet($settings){
        if(empty($settings['scale'])){
            $settings['scale'] = 'month';
        }
        switch($settings['scale']){
            case 'day':
                $settings['dateFormat'] = 'D j M';
                $settings['scaleDivide'] = 'tomorrow midnight';
                break;

            case 'week':
                $settings['dateFormat'] = 'j M (\wk W)';
                $settings['scaleDivide'] = 'next monday midnight';
                if(date('D') == 'Sun'){
                    $settings['scaleDivide'] = 'tomorrow midnight';
                }
                break;

            case 'month':
                $settings['dateFormat'] = 'M Y';
                $settings['scaleDivide'] = 'first day of next month midnight';
                break;

            case 'year':
                $settings['dateFormat'] = 'Y';
                $settings['scaleDivide'] = 'first day of next year midnight';
                break;
        }
        return $settings;
    }

    public function getScaledEndDate($startTime,$settings=null){
        if(empty($settings)){
            $settings = $this->settings;
        }

        if(strtotime($settings['scaleDivide'],$startTime) < $settings['endTime']){
            $settings['endTime'] = strtotime($settings['scaleDivide'],$startTime)-1;
        }
        return $settings['endTime'];
    }

    /*
     * format form dates to time stamps for use in calculations
     * @param array $settings - e.g. $this->settings
     * @return array
     */
    protected function formatDateRange($settings){
        //format start time
        if(empty($settings['startTime'])){
            if(!empty($settings['startDate'])){
                $settings['startTime'] = $settings['startDate'];
                if(!is_numeric($settings['startDate'])){
                    $settings['startTime'] = strtotime($settings['startDate']);
                }
            }else{
                $settings['startTime'] = time();
            }
        }

        //format end time
        if(empty($settings['endTime'])){
            if(!empty($settings['endDate'])){
                $settings['endTime'] = $settings['endDate'];
                if(!is_numeric($settings['endDate'])){
                    $settings['endTime'] = strtotime($settings['endDate']);
                }
                //push end date forward 1 day to include the full day of the endDate
                $settings['endTime'] = strtotime('tomorrow',$settings['endTime']);
            }else{
                $settings['endTime'] = time();
            }
        }

        //reduce endDate by 1 sec to exclude results with a timestamp of midnight the following day
        $settings['endTime'] = $settings['endTime']-1;

        //update formatted dates
        $settings['startDate'] = date('j M Y', $settings['startTime']);
        $settings['endDate'] = date('j M Y', $settings['endTime']);
        return $settings;
    }

    /*
     * main function for updating model settings
     * @param array $settings - an array of attributes and undefined options to be used in calculations
     * @param boolean $merge - merge new settings with existing settings
     * @param boolean $preserve - overwrite $this->settings with the new settings
     * @return array
     */
    public function setSettings($settings=array(), $merge=true, $preserve=false){
        //add missing attributes to the settings
        foreach($this->attributes as $key => $val){
            if(!isset($settings[$key])){
                $settings[$key] = $val;
            }
        }

        //merge existing settings that are missing if new settings  will not be preserved
        if($merge && !empty($this->settings)){
            foreach($this->settings as $key => $val){
                if(!isset($settings[$key])){
                    $settings[$key] = $val;
                }
            }
        }

        //format settings data
        $newSettings = $settings;
        $newSettings = array_merge($newSettings, $this->formatDateRange($newSettings));
        $newSettings = array_merge($newSettings, $this->setDateFormet($newSettings));
        $newSettings = array_merge($newSettings, $this->setTimeDiff($newSettings));

        //preserve new settings
        if($preserve){
            $this->settings = $newSettings;
            $this->startTime = strtotime($this->startDate);
            $this->endTime = strtotime($this->endDate);
        }
        return $newSettings;

    }

    /*
     * calculate the time difference between the start and end time stamps
     * @param array $settings - e.g. $this->settings
     * @return array
     */
    protected function setTimeDiff($settings){
        if(!empty($settings['startTime']) && !empty($settings['endTime'])){
            $settings['timeDiff'] = $settings['endTime'] - $settings['startTime'];
        }
        return $settings;
    }

    ///////////////////////////////////////
    // COMMA SEPARATED
    ///////////////////////////////////////


    /**
     * switch comma separated attribute between array and string
     * @param string $attr - attribute name
     * @param string $format - string/array - format to convert the attribute to
     * @param mixed $separator - character to separate data by
     */
    public function formatCommaSeparated($attr, $format='string', $separator=','){
        if(empty($this->$attr)){
            $this->$attr = array();
        }
        if($format == 'string' && is_array($this->$attr)){
            $result = array();
            foreach($this->$attr as $key=>$val){
                if(!empty($val) || $val==0){
                    $result[] = trim($val);
                }
            }
            sort($result);
            $this->$attr = $separator.implode($separator, $result).$separator;
            if($this->$attr == $separator.$separator){
                $this->$attr = '';
            }
        }elseif($format == 'array' && !is_array($this->$attr)){
            $result = array();
            $temp = explode($separator, str_replace(', ',',',$this->$attr));
            foreach($temp as $key=>$val){
                if(!empty($val) || $val===0){
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
     * @param mixed $values - data to add to the attribute
     * @return boolean
     */
    public function addCommaSeparated($attr, $values){
        if(!is_array($values)){
            $values = array($values);
        }
        if(!is_array($this->$attr)){
            $this->formatCommaSeparated($attr, 'array');
        }
        $temp = array();
        foreach($this->$attr as $val){
            if(!in_array($val, $temp) && $val!='none'){
                $temp[] = $val;
            }
        }
        foreach($values as $val){
            if(!in_array($val, $temp)){
                $temp[] = $val;
            }
        }
        sort($temp);
        $this->$attr = $temp;
    }

    /**
     * remove a single or multiple comma separates value, if all are removed then insert the account type 'none' if $allowEmpty is false
     * @param string $attr - attribute name
     * @param mixed $values - data to remove from the attribute
     * @param boolean $allowEmpty - allow array to be empty or include value 'none'
     */
    public function deleteCommaSeparated($attr, $values, $allowEmpty=true){
        if(!is_array($values)){
            $values = array($values);
        }
        if(!is_array($this->$attr)){
            $this->formatCommaSeparated($attr, 'array');
        }
        $temp = array();
        foreach($this->$attr as $val){
            if(!in_array($val, $temp) && !in_array($val, $values)){
                $temp[] = $val;
            }
        }
        if(!$allowEmpty && empty($temp)){
            //insert 'none' if no other account types are present
            $temp[] = 'none';
        }
        $this->$attr = $temp;
    }

    /*
     * array version of strpos()
     * @param string $haystack
     * @param array $needles
     * @param integer $offset
     * @return integer
     */
    public function strposa($haystack, $needles=array(), $offset=0) {
        $chr = array();
        foreach($needles as $needle) {
            $res = strpos($haystack, $needle, $offset);
            if ($res !== false) $chr[$needle] = $res;
        }
        if(empty($chr)) return false;
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
    public function formatSerialised($attr, $format='string'){
        if(empty($this->$attr)){
            $this->$attr = array();
        }
        if($format == 'string' && is_array($this->$attr)){
            $this->$attr = serialize($this->$attr);
        }elseif($format == 'array' && !is_array($this->$attr)){
            $temp = unserialize($this->$attr);
            $this->$attr = $temp;
        }
    }

    /**
     * add associative array values and clean out any duplicates
     * @param string $attr - attribute name
     * @param array $values - data to add to the attribute
     * @param boolean $overwriteExisting - true overwrites existing data and false only adds missing data
     * @return boolean
     */
    public function addSerialised($attr, $values, $overwriteExisting=true){
        if(!is_array($this->$attr)){
            $this->formatSerialised($attr, 'array');
        }
        if(!is_array($values)){
            $values = array($values);
        }
        $temp = array();
        foreach($this->$attr as $key => $val){
            if(!array_key_exists($key, $temp)){
                $temp[$key] = $val;
            }
        }
        foreach($values as $key => $val){
            if($overwriteExisting) {
                if (!array_key_exists($key, $temp) || (array_key_exists($key, $temp) && $temp[$key] != $val)) {
                    $temp[$key] = $val;
                }
            }else{
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
     * @param mixed $values - array keys to remove from the attribute
     */
    public function deleteSerialised($attr, $values){
        if(!is_array($values)){
            $values = array($values);
        }
        if(!is_array($this->$attr)){
            $this->formatSerialised($attr, 'array');
        }
        $temp = array();
        foreach($this->$attr as $key => $val){
            if(!array_key_exists($key, $temp) && !in_array($key, $values)){
                $temp[$key] = $val;
            }
        }
        $this->$attr = $temp;
    }

}