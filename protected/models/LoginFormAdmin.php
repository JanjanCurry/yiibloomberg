<?php class LoginFormAdmin extends CFormModel {
    public $error;
    public $rememberMe = 1;
    public $encryptPassword = true;

    public $password;
    public $email;

    public $user;
    public $passwordConfirm;
    public $verifyCode;

    private $identity;

    public function rules () {
        return array(
            array('email, password', 'required', 'on' => 'login'),
            array('email', 'required', 'on' => 'reset'),
            array('password, passwordConfirm', 'required', 'on' => array('createPassword','changePassword')),

            array('passwordConfirm', 'validatePasswordConfirm', 'on' => array('createPassword','changePassword')),
            array('verifyCode', 'application.extensions.yiiReCaptcha.ReCaptchaValidator', 'on' => array('createPassword')),

            array('rememberMe', 'boolean'),
            array('email', 'email'),
        );
    }

    public function attributeLabels () {
        return array(
            'rememberMe' => 'Keep me logged in',
            'passwordConfirm' => 'Confirm Password'
        );
    }

    public function login () {
        $valid = false;

        $this->identity = new UserIdentity($this->email, $this->password);
        $this->identity->authenticate();

        if ($this->identity->errorCode == 'Valid') {
            $duration = 0;
            if ($this->rememberMe == 1) {
                $duration = 60 * 60 * 2; // 2 hours
            }
            Yii::app()->user->login($this->identity, $duration);
            $valid = true;
        } else {
            if($this->identity->errorCode =='Email Address Not Verified'){
                $mail = new Mail();
                $mail->addMail($this->identity->user, 'create-password');
                $this->addError('error', 'Email not verified, please check your inbox');
            }else {
                $this->addError('error', 'Incorrect login details');
            }
        }

        return $valid;
    }

    public function forceLogin () {
        if(!empty($this->user) && $this->user->email) {
            $this->identity = new UserIdentity($this->user->email, $this->user->password);
            $this->identity->encryptPassword = false;
            $this->identity->authenticate();
            $duration = 60 * 60 * 2; // 2 hours
            return Yii::app()->user->login($this->identity, $duration);
        }
        return false;
    }

    public function forgotPassword () {
        $valid = false;

        if (!empty($this->email)) {
            $user = DbUser::model()->findByAttributes(array('email' => $this->email));
            if (!empty($user)) {
                $mail = new Mail();
                $valid = $mail->addMail($user, 'forgot-password');
            } else {
                $this->addError('email', 'That email address is already linked with an account');
            }
        }

        return $valid;
    }

    public function changePassword(){
        $valid = false;
        if(!empty($this->user) && ($this->password)){
            $this->user->passwordNew = $this->password;
            $this->user->passwordConfirm = $this->passwordConfirm;
            $this->user->verifyEmail = 1;
            if($this->user->save()){
                $valid = true;
            }else{
                $this->addError('password', 'Failed to change password');
            }
        }
        return $valid;
    }

    public function unsubscribe(){
        $valid = false;

        if (!empty($this->email)) {
            $user = DbUser::model()->findByAttributes(array('email' => $this->email));
            if (!empty($user)) {
                $user->unsubscribe = 1;
                $valid = $user->save();
            }
        }

        return $valid;
    }

    public function validatePasswordConfirm(){
        if(empty($this->password) || empty($this->passwordConfirm) || $this->password != $this->passwordConfirm){
            $this->addError('passwordConfirm', 'Passwords must match');
        }

        if(strlen($this->passwordConfirm) < 4 || strlen($this->passwordConfirm) > 24){
            $this->addError('passwordConfirm', 'Password must be between 4 and 24 characters long');
        }
    }

}