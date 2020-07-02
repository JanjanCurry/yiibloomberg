<?php class Formatter extends CFormatter {

    public $dateFormat = 'j M Y';
    public $datetimeFormat = 'j M Y H:i';
    public $timeFormat = 'H:i';

    /**
     * calculate length of time between 2 dates
     * @param mixed $startVal
     * @param mixed $endVal
     * @param string $type
     * @return string
     */
    public function formatAge($startVal, $endVal=null, $type=null) {
        if(is_numeric($startVal)){
            $start = new DateTime();
            $start->setTimestamp($startVal);
        }else{
            $start = new DateTime($startVal);
        }

        if(!empty($endVal)) {
            if(is_numeric($endVal)){
                $end = new DateTime();
                $end->setTimestamp($endVal);
            }else{
                $end = new DateTime($endVal);
            }
        }else{
            $end = new DateTime();
        }

        $interval = $end->diff($start);
        $doPlural = function($nb,$str){return $nb>1?$str.'s':$str;}; // adds plurals

        $data = array();
        if($interval->y > 0) {
            $data[] = array(
                'key' => 'y',
                'val' => '%y',
                'ref' => $doPlural($interval->y, "year")
            );
        }
        if($interval->m > 0) {
            $data[] = array(
                'key' => 'm',
                'val' => '%m',
                'ref' => $doPlural($interval->m, "month")
            );
        }
        if($interval->d > 0) {
            $data[] = array(
                'key' => 'd',
                'val' => '%d',
                'ref' => $doPlural($interval->d, "day")
            );
        }
        if($interval->h > 0) {
            $data[] = array(
                'key' => 'h',
                'val' => '%h',
                'ref' => $doPlural($interval->h, "hour")
            );
        }
        if($interval->i > 0) {
            $data[] = array(
                'key' => 'i',
                'val' => '%i',
                'ref' => $doPlural($interval->i, "minute")
            );
        }
        if($interval->s > 0) {
            if(empty($data)) {
                switch($type){
                    case 'double':
                        return $interval->s.' '.$doPlural($interval->s, "second");

                    case 'age':
                    default:
                        return "less than a minute ago";
                }
            } else {
                $data[] = array(
                    'key' => 's',
                    'val' => '%s',
                    'ref' => $doPlural($interval->s, "second")
                );
            }
        }

        if(!empty($data)){
            switch($type){
                case 'age':
                    $msg = $data[0]['val'].' '.$data[0]['ref'].' ago';
                    break;
                case 'days':
                    $msg = 0;
                    foreach($data as $set){
                        $val = $interval->format($set['val']);
                        switch($set['key']){
                            case 'y':
                                $msg += $val*365;
                                break;                            
                            case 'm':
                                $msg += $val*30;
                                break;
                            case 'd':
                                $msg += $val;
                                break;
                        }
                    }
                    break;
                case 'months':
                    $msg = 0;
                    foreach($data as $set){
                        $val = $interval->format($set['val']);
                        switch($set['key']){
                             case 'y':
                                $msg += $val*12;
                                break;                           
                            case 'm':
                                $msg += $val;
                                break;
                        }
                    }
                    break;
                case 'decimal':
                    $msg = 0;
                    foreach($data as $set){
                        $val = $interval->format($set['val']);
                        switch($set['key']){
                            case 'm':
                                $msg += ($val*30)*24;
                                break;
                            case 'd':
                                $msg += $val*24;
                                break;
                            case 'h':
                                $msg += $val;
                                break;
                            case 'i':
                                $msg += $val * (1/60);
                                break;
                        }
                    }
                    break;
                case 'double':
                    $msg = $data[0]['val'].' '.$data[0]['ref'].' and '.$data[1]['val'].' '.$data[1]['ref'];
                    break;
                case 'single':
                    $msg = $data[0]['val'].' '.$data[0]['ref'];
                    break;
                case 'ending':
                    $msg = $data[0]['val'].' <span class="">('.$data[0]['ref'].')</span>';
                    break;
                case 'hours':
                    $msg = 0;
                    foreach($data as $set){
                        $val = $interval->format($set['val']);
                        switch($set['key']){
                            case 'm':
                                $msg += ($val*30)*24;
                                break;
                            case 'd':
                                $msg += $val*24;
                                break;
                            case 'h':
                                $msg += $val;
                                break;
                            case 'i':
                                if($val > 30){
                                    $msg += 1;
                                }
                                break;
                        }
                    }
                    break;
                case 'minutes':
                    $msg = 0;
                    foreach($data as $set){
                        $val = $interval->format($set['val']);
                        switch($set['key']){
                            case 'm':
                                $msg += ($val*30)*24*60;
                                break;
                            case 'd':
                                $msg += $val*24*60;
                                break;
                            case 'h':
                                $msg += $val*60;
                                break;
                            case 'i':
                                $msg += $val;
                                break;
                            case 's':
                                if($val > 30){
                                    $msg += 1;
                                }
                                break;
                        }
                    }
                    break;
                default:
                    foreach($data as $set){
                        if(empty($msg)){
                            $msg = $set['val'].' '.$set['ref'];
                        }else{
                            $msg .= ', '.$set['val'].' '.$set['ref'];
                        }
                    }
                //$msg = $data[0]['val'].' '.$data[0]['ref'];
            }
        }else{
            $msg = '';
        }

        return $interval->format($msg);
    }

    /**
     * Formats the value as a date.
     * @param mixed $value the value to be formatted
     * @return string the formatted result
     * @see dateFormat
     */
    public function formatDate($value)
    {
        if(!empty($value) && is_numeric($value)){
            return parent::formatDate($value);
        }
    }

    /**
     * Formats the value as a time.
     * @param mixed $value the value to be formatted
     * @return string the formatted result
     * @see timeFormat
     */
    public function formatTime($value)
    {
        if(!empty($value) && is_numeric($value)){
            return parent::formatTime($value);
        }
    }

    /**
     * Formats the value as a date and time.
     * @param mixed $value the value to be formatted
     * @return string the formatted result
     * @see datetimeFormat
     */
    public function formatDatetime($value)
    {
        if(!empty($value) && is_numeric($value)){
            return parent::formatDatetime($value);
        }
    }

    /**
     * Wraps a pre tag around a var_dump()
     * @param mixed $data
     * @param boolean $die
     */
    public function formatDebug($data,$die=false){
        echo '<pre>';
        var_dump($data);
        echo '</pre>';
        if($die){
            exit;
        }
    }

    /**
     * Converts hex colour to rgb colour
     * @param mixed $hex
     * @return array
     */
    function formatHex2rgb( $hex ) {
        if ( $hex[0] == '#' ) {
            $hex = substr( $hex, 1 );
        }
        if ( strlen( $hex ) == 6 ) {
            list( $r, $g, $b ) = array( $hex[0] . $hex[1], $hex[2] . $hex[3], $hex[4] . $hex[5] );
        } elseif ( strlen( $hex ) == 3 ) {
            list( $r, $g, $b ) = array( $hex[0] . $hex[0], $hex[1] . $hex[1], $hex[2] . $hex[2] );
        } else {
            return false;
        }
        $r = hexdec( $r );
        $g = hexdec( $g );
        $b = hexdec( $b );
        return array( 'r' => $r, 'g' => $g, 'b' => $b );
    }

    /**
     * Converts camelCaseValue to normal value
     * @param string $value
     * @return string
     */
    public function formatCamelCase($value) {
        return ucfirst(implode(" ",preg_split("/(?=[A-Z0-9])/",$value)));
    }

    /**
     * switch comma separated values between array and string
     * @param string $values - values to be formatted
     * @param string $format - string/array - format to convert the attribute to
     * @param mixed $separator - character to separate data by
     */
    public function formatCommaSeparated($values, $format='string', $separator=','){
        if(empty($values)){
            $values = array();
        }
        if($format == 'string' && is_array($values)){
            $result = array();
            foreach($values as $key=>$val){
                if(!empty($val) || $val==0){
                    $result[] = trim($val);
                }
            }
            sort($result);
            $values = $separator.implode($separator, $result).$separator;
            if($values == $separator.$separator){
                $values = '';
            }
        }elseif($format == 'array' && !is_array($values)){
            $result = array();
            $temp = explode($separator, str_replace(', ',',',$values));
            foreach($temp as $key=>$val){
                if(!empty($val) || $val===0){
                    $result[] = trim($val);
                }
            }
            sort($result);
            $values = $result;
        }
        return $values;
    }

    /**
     * Prepends pound sign and formats the number
     * @param string $value
     * @return string
     */
    public function formatCurrency($value) {
        return '$'.number_format(floatval($value));
    }

    /*
     * Converts a file size in bytes to a readable short-hand format
     * @param integer $bytes - file size
     * @param integer $decimals - round to number of decimals (default: 2)
     * @return string
     */
    public function formatFileSize($bytes, $precision = 2) {
        $units = array('B', 'KB', 'MB', 'GB', 'TB');

        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);

        // Uncomment one of the following alternatives
        $bytes /= pow(1024, $pow);
        // $bytes /= (1 << (10 * $pow));

        return round($bytes, $precision) . ' ' . $units[$pow];
    }

    /**
     * Appends the text th, st, nd, or rd to a number based on its value
     * @param integer $number
     * @return string
     */
    function formatOrdinalNumber($number) {
        $ends = array('th','st','nd','rd','th','th','th','th','th','th');
        if ((($number % 100) >= 11) && (($number%100) <= 13)) {
            return $number . 'th';
        }else {
            return $number . $ends[$number % 10];
        }
    }

    /**
     * generic recursive loop to update option arrays
     * @param array $new
     * @param array $old
     * @return string
     */
    public function formatOptions($new, $old=null) {
        if(empty($old)){
            $old = array();
        }
        if(!empty($new)){
            foreach($new as $key => $val){
                if(is_array($val)){
                    if(empty($old[$key])){
                        $old[$key] = array();
                    }
                    $old[$key] = $this->formatOptions($val, $old[$key]);
                }else{
                    $old[$key] = $val;
                }
            }
        }
        return $old;
    }

    /*
     * calculates the percentage and returns as int
     * @param int $int1
     * @param int $int2
     * @param boolean $incSymbol
     * @return int
     */
    public function formatPercentDiff($int1,$int2,$incSymbol=false){
        $result = 0;

        if(!empty($int1) && !empty($int2)) {
            $result = round(($int1 / $int2) * 100);

            if ($result > 0) {
                $result = $result - 100;
            }
        }

        if ($incSymbol) {
            return $result . '%';
        } else {
            return $result;
        }
    }

    /*
     * format a phone number to a common format
     * @param string $number
     * @return string
     */
    public function formatPhone($number){
        //remove any spaces before formatting
        $number = trim(str_replace(' ','',$number));

        // Change the international number format and remove any non-number character
        $number=preg_replace( '/[^0-9]+/','',str_replace("+", "00", $number));

        // This uses full codes from http://www.area-codes.org.uk/formatting.shtml
        $telephoneFormat = array (
            '02' => "3,4,4",
            '03' => "4,3,4",
            '05' => "3,4,4",
            '0500' => "4,6",
            '07' => "5,6",
            '070' => "3,4,4",
            '076' => "3,4,4",
            '07624' => "5,6",
            '08' => "4,3,4", // some 0800 numbers are 4,6
            '09' => "4,3,4",
            '01' => "5,6", // some 01 numbers are 5,5
            '011' => "4,3,4",
            '0121' => "4,3,4",
            '0131' => "4,3,4",
            '0141' => "4,3,4",
            '0151' => "4,3,4",
            '0161' => "4,3,4",
            '0191' => "4,3,4",
            '013873' => "6,5",
            '015242' => "6,5",
            '015394' => "6,5",
            '015395' => "6,5",
            '015396' => "6,5",
            '016973' => "6,5",
            '016974' => "6,5",
            '016977' => "6,5",
            '0169772' => "6,4",
            '0169773' => "6,4",
            '017683' => "6,5",
            '017684' => "6,5",
            '017687' => "6,5",
            '019467' => "6,5"
        );
        //$telephoneFormat= array_reverse($telephoneFormat);

        // Sorts into longest key first
        $keys = array_map('strlen', array_keys($telephoneFormat));
        array_multisort($keys, SORT_DESC, $telephoneFormat);

        $format = "6,5";
        foreach ($telephoneFormat AS $key=>$value) {
            if (substr($number,0,strlen($key)) == $key){
                $format = $value;
                break;
            }
        }
        $format = explode(',',$format);

        // Turn number into array based on Telephone Format
        $start=0;
        $numberArray = array();
        if(!empty($format) && is_array($format)){
            foreach($format AS $value) {
                $numberArray[] = substr($number,$start,$value);
                $start = $start+$value;
            }
        }

        // Add brackets around first split of numbers if number starts with 01 or 02
        /*if(!empty($numberArray)){
            if (substr($number,0,2)=="01" || substr($number,0,2)=="02"){
                $numberArray[0]="(".$numberArray[0].")";
            }
        }*/

        // Convert array back into string, split by spaces
        $formattedNumber = implode(" ",$numberArray);

        //revert back to original number if formatting failed
        if(empty($formattedNumber)){
            $formattedNumber = $number;
        }

        return $formattedNumber;
    }

    /**
     * change filename to a common format for file-system
     * @param string $filename
     * @param string $fileExtension
     * @return string
     */
    public function formatSafeFileName($filename, $fileExtension){
        //remove extension
        $clean = str_replace('.' . $fileExtension, '', $filename);

        //strip blacklisted chars
        $strip = array("~", "`", "!", "@", "#", "$", "£", "%", "^", "&", "*", "(", ")", "_", "=", "+", "[", "{", "]",
            "}", "\\", "|", ";", ":", "\"", "'", "&#8216;", "&#8217;", "&#8220;", "&#8221;", "&#8211;", "&#8212;",
            "â€”", "â€“", ",", "<", ".", ">", "/", "?");
        $clean = trim(str_replace($strip, "", strip_tags($clean)));

        //replace spaces
        $clean = preg_replace('/\s+/', "-", $clean);
        $clean = str_replace('--', "-", $clean);

        //add extension back in and format to lowercase
        $clean = $clean . '.' . $fileExtension;
        //$clean = strtolower($clean . '.' . $fileExtension);

        return $clean;
    }

    /**
     * reface url unfriendly chars with readable url friendly chars
     * @param string $string
     * @return string
     */
    public function formatSafeUrlString($string){
        $string = strtolower($string); //convert to lower case
        $string = str_replace('(','',$string); //replace slashes
        $string = str_replace(')','',$string); //replace slashes
        $string = str_replace('?','',$string); //replace slashes
        $string = str_replace('&',' and ',$string); //replace slashes
        $string = str_replace('  ',' ',$string); //replace double spacing
        $string = str_replace(', ','_',$string); //replace commas
        $string = str_replace(',','_',$string); //replace commas
        $string = str_replace('.','_',$string); //replace commas
        $string = str_replace(' ','_',$string); //replace spaces
        $string = str_replace('/','_',$string); //replace slashes
        $string = str_replace('\\','_',$string); //replace slashes
        $string = str_replace('__','_',$string); //replace slashes
        $string = str_replace('__','_',$string); //replace slashes
        return preg_replace('/[^A-Za-z0-9\-_]/', '', $string);
        return $string;
    }

    /**
     * Prepends pound sign and formats the number without cent part
     * @param string $value
     * @return string
     */
    public function formatShortCurrency($value) {
        return '£'.number_format(floatval($value));
    }

    /**
     * Toggles value between DatePicker and timestamp formats
     * @param string/integer $value
     * @param string $convertTo - integer/string
     * @param string $strFormat - date/datetime
     * @return string
     */
    public function formatTimestamp($value, $convertTo='int', $strFormat='datetime'){
        if(!empty($value)){
            if(($convertTo == 'int' || $convertTo == 'integer') && !is_numeric($value)){
                $value = strtotime(str_replace('/', '-', $value));
                //check if strtotime() formatted correctly
                if(empty($value) || !is_numeric($value) || $value < strtotime('1 Jan 2000')){
                    $value = null;
                }
            }elseif($convertTo == 'string' && is_numeric($value)){
                if($strFormat == 'date') {
                    $value = Yii::app()->format->date($value);
                }else{
                    $value = Yii::app()->format->datetime($value);
                }
            }else{
                $value = null;
            }
        }
        return $value;
    }

    /*
     * Get the first number of words from a string
     * @param string $string - the text to shortened
     * @param integer $wordCount - number of words to return
     * @return string
     */
    public function formatWordLength($string, $wordCount=10){
        return implode(' ', array_slice(explode(' ', $string), 0, $wordCount));
    }

}