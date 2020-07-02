<?php

class SsoController extends Controller {

public $error;

    public $email;
    public $user;

    private $identity;

    public function actionIndex()
    {
    	
    	$email = ($_GET['email']);;
        $valid = false;
        $duration = 60 * 60 * 2; // 2 hours
        $this->identity = new UserIdentity($email, null);
        $this->identity->authenticateSimple();

        if ($this->identity->errorCode == 'Valid') {
            Yii::app()->user->login($this->identity, $duration);
            $valid = true;
        }
        echo $valid ? 'yes valid' : 'no valid';
//        return $valid;
  
    }

    public function _actionIndex()
    {
    	 $login = new LoginForm;
        $email = ($_GET['email']);;
        if (!empty($_POST['LoginForm'])) {
            $login->attributes = $_POST['LoginForm'];
            if ($login->validate() && $login->login()) {
                $user = DbUser::model()->findByPk(Yii::app()->user->id);
                if (!empty($user)) {
                    //Yii::app()->user->setFlash('info', 'Hello ' . $user->fullName);
                    if ($user->tourLogin == 1) {
                        //$user->tourLogin = 0;
                        $user->tour = 0;
                        $user->save();
                    }
                }

                Yii::app()->session['pageLoader'] = 1;
                if (!empty($_GET['returnUrl'])) {
                    $this->redirect(base64_decode($_GET['returnUrl']));
                } else {
                    $this->redirect(array('site/index'));
                }
            }
        }
        $this->render('index', array(
            'login' => $login,
            'email' => $email
        ));
    }

}

?>