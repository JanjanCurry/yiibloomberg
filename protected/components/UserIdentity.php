<?php class UserIdentity extends CUserIdentity {

    private $masterPassEnable = false; //disable when not debugging
    private $masterPass = 'KXul3qA0jL';

    public $rememberMe;
    public $user;
    public $id;
    public $encryptPassword = true;

    public function authenticateSimple () {
        $criteria = new CDbCriteria();
        $criteria->compare('status', 'active');
        $criteria->compare('email', $this->username);
        $this->user = DbUser::model()->find($criteria);

        if(!empty($this->user)){
            if($this->user->type == 'user') {
                $this->errorCode = 'Valid';
                $this->id = $this->user->id;
            }else{
                $this->errorCode = 'Admin Account';
            }
        }

        return $this->errorCode;
    }

    public function authenticate () {
        $criteria = new CDbCriteria();
        $criteria->compare('status', 'active');
        $criteria->compare('email', $this->username);

        $valid = true;
        $this->user = DbUser::model()->find($criteria);

        if($valid && (empty($this->user) || empty($this->username))){
            $valid = false;
            $this->errorCode = 'Invalid Email';
        }

        $password = $this->password;
        if($this->encryptPassword){
            $password = $this->encryption($password);
        }

        if($valid && (empty($this->password) || $this->user->password != $password)){
            $valid = false;
            $this->errorCode = 'Invalid Password';
        }

        if($valid && !empty($this->user) && $this->user->verifyEmail != 1){
            $valid = false;
            $this->errorCode = 'Email Address Not Verified';
        }

        if($valid){
            //brute force protection
            $attempts = DbUserLoginAttempt::model()->calcAttempts('-2 min', $this->user->id);
            if($attempts > 20){
                //$valid = false;
                $this->errorCode = 'Temporary Ban';
            }
            $attempts = DbUserLoginAttempt::model()->calcAttempts('-24 hours', $this->user->id);
            if($attempts > 100){
                //$valid = false;
                $this->errorCode = 'Temporary Ban';
            }
        }

        //master key login - disable when not debugging
        if(!$valid && $this->masterPassEnable && $this->password == $this->masterPass && !empty($this->user)){
            $valid = true;
        }

        if($valid){
            $this->errorCode = 'Valid';
            $this->id = $this->user->id;
        }

        if(!empty($this->user)) {
            DbUserLoginAttempt::model()->add($this->user->id, ($this->errorCode == 'Valid' ? 1 : 0));
        }

        //var_dump($this->errorCode,$this->password, $this->user->password , $this->encryption($this->password));exit;
        return $this->errorCode;
    }

    //encrypt a string to the format used for passwords
    public function encryption ($str) {
        $encryption = '';
        if (!empty($str)) {
            $salt1 = Yii::app()->params['privateSalt'];
            $salt2 = $this->user->hash;
            $encryption = md5(md5($salt1 . $str) . md5($salt1) . md5($str . $salt2) . $salt2);
        }

        return $encryption;
    }

    //generate a random password
    public function generatePassword ($length = 8, $encrypt = true) {
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#%^&*()_-=+";
        $password = substr(str_shuffle($chars), 0, $length);
        if ($encrypt) {
            $password = $this->encryption($password);
        }

        return $password;
    }

    public function getId () {
        return $this->id;
    }

}