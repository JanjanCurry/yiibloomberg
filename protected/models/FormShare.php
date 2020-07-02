<?php class FormShare extends FormModel {

    public $email;
    public $image;
    public $message;
    public $title;

    public function rules () {
        return array(
            array('email, image, message, title', 'required'),
            array('email', 'email'),
            array('email, image, message, title', 'safe'),
        );
    }

    public function attributeLabels () {
        return array(
            'email' => 'Email',
            'message' => 'Message',
        );
    }

    public function send () {
        if ($this->validate()) {
            $mail = new Mail();

            $image = str_replace('http://api.completeintel.com',YiiBase::getPathOfAlias('root'),$this->image);

            return $mail->addMail($this, 'chart-share', array(
                'sendTo' => $this->email,
                'attach' => array(
                    $image,
                ),
            ));
        }
    }

}