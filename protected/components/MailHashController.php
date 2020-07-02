<?php class MailHashController extends Controller {

    protected $mailHash;

    public function userLogin () {
        $user = $this->mailHash->ref;
        if (empty($user)) {
            $this->triggerMailError();
        }
        $this->verifyEmail($user);
        $this->mailHash->delete();

        $login = new LoginForm();
        $login->user = $user;

        if ($login->forceLogin()) {
            Yii::app()->session['pageLoader'] = 1;
            if($user->tourLogin == 1){
                //$user->tourLogin = 0;
                $user->tour = 0;
                $user->save();
            }
            $this->redirect(array('site/index'));
        }else{
            $this->redirect(array('site/login'));
        }
    }

    public function passwordReset () {
        $user = $this->mailHash->ref;
        if (empty($user)) {
            $this->triggerMailError();
        }
        $this->verifyEmail($user);

        $login = new LoginForm();
        $login->user = $user;
        $login->scenario = 'changePassword';
        if (!empty($_POST['LoginForm'])) {
            $login->attributes = $_POST['LoginForm'];
            if ($login->validate() && $login->changePassword()) {
                $this->mailHash->delete();

                //auto login
                $login->email = $user->email;
                $login->scenario = 'login';
                if ($login->validate() && $login->login()) {
                    $this->redirect(array('site/index'));
                }else{
                    Yii::app()->user->setFlash('success', 'Password saved');
                }

                $this->redirect(array('site/login'));
            }
        }

        //reset password fields so encrypted string is not shown in form
        $login->password = null;
        $login->passwordConfirm = null;
        $this->render('hash/passwordReset', array(
            'login' => $login,
        ));
    }

    public function passwordCreate () {
        $user = $this->mailHash->ref;
        if (empty($user)) {
            $this->triggerMailError();
        }
        $this->verifyEmail($user);

        $login = new LoginForm();
        $login->user = $user;
        $login->scenario = 'createPassword';
        if (!empty($_POST['LoginForm'])) {
            $login->attributes = $_POST['LoginForm'];
            if ($login->validate() && $login->changePassword()) {
                Yii::app()->user->setFlash('success', 'Password saved');
                $this->mailHash->delete();

                $mail = new Mail();
                $mail->addMail($user, 'client-welcome');

                //auto login
                $login->email = $user->email;
                $login->scenario = 'login';
                if ($login->validate() && $login->login()) {
                    $this->redirect(array('site/index'));
                }else{
                    Yii::app()->user->setFlash('success', 'Password saved');
                }

                $this->redirect(array('site/login'));
            }
        }

        //reset password fields so encrypted string is not shown in form
        $login->password = null;
        $login->passwordConfirm = null;
        $this->render('hash/passwordCreate', array(
            'login' => $login,
        ));
    }

    protected function triggerMailError () {
        if (!empty($this->mailHash)) {
            $this->mailHash->delete();
        }
        Yii::app()->user->setFlash('danger', 'Email link has expired');
        $this->redirect(array('site/login'));
    }

    protected function verifyEmail($user){
        $user->verifyEmail = 1;
        $user->save();
    }

}