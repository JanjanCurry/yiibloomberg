<?php class FormContact extends FormModel {

    public $company;
    public $email;
    public $fName;
    public $message;
    public $sName;

    public function rules () {
        return array(
            array('email, fName, sName', 'required'),
            array('message', 'required', 'on' => 'contact'),

            array('email', 'email'),

            array('company, email, fName, message, sName', 'safe'),
        );
    }

    public function attributeLabels () {
        return array(
            'email' => 'Email',
            'Company' => 'Company Name',
            'fName' => 'First Name',
            'message' => 'Message',
            'sName' => 'Last Name',
        );
    }

    public function saveContact () {
        if ($this->validate()) {
            return true;
        }
        return false;
    }

    public function saveSignUp () {
        if ($this->validate()) {
            return true;
        }
        return false;
    }

}