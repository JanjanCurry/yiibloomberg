<?php class LoginForm extends CFormModel {
    public $error;

    public $email;
    public $user;

    private $identity;

    public function rules () {
        return array(
            array('email', 'required', 'on' => 'login'),

            array('email', 'email'),
        );
    }

    public function login () {
        $valid = false;
        $duration = 60 * 60 * 2; // 2 hours
        $this->identity = new UserIdentity($this->email, null);
        $this->identity->authenticateSimple();

        if ($this->identity->errorCode == 'Valid') {
            Yii::app()->user->login($this->identity, $duration);
            $valid = true;
        }else if(empty($this->identity->user) && $this->identity->errorCode != 'Admin Account'){
            $user = new DbUser();
            $user->email = $this->email;
            $user->fName = $this->email;

            if ($user->save()) {
                foreach (DbUserService::model()->listTool()as $key => $val) {
                    DbUserService::add($user->id, $key, 'pro', null);
                }
                $user->setDefaultFavorites();
                $this->identity->user = $user;
                $this->identity->id = $user->id;
                Yii::app()->user->login($this->identity, $duration);
                $valid = true;
            }
        } else {
            $this->addError('error', 'Unable to login');
        }

        return $valid;
    }

}