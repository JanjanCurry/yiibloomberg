<?php

class SamlController extends Controller {

    // protected function beforeAction ($action) {
    //     $this->redirect(['site/index']);
    // }
    public function actionIndex()
    {
     $this->actionLogin();
    }

    public function actionLogin(){
        $auth = Yii::app()->saml->getAuth();
        //Yii::app()->format->debug($auth);
        if(empty($_SESSION['samlUserdata'])){
            //$auth->login();
            $ssoBuiltUrl = $auth->login(null, array(), false, false, true);
            $_SESSION['AuthNRequestID'] = $auth->getLastRequestID();
            header('Pragma: no-cache');
            header('Cache-Control: no-cache, must-revalidate');
            header('Location: ' . $ssoBuiltUrl);
            exit();
        }else{
            $this->redirect(['site/index']);
        }
    }

    public function actionLogout(){
        $auth = Yii::app()->saml->getAuth();
        if(!empty($_SESSION['samlUserdata'])) {
            $auth->logout($_SESSION['samlUserdata']);
        }else{
            $this->redirect(['site/index']);
        }
    }

    public function actionAcs(){
        if (isset($_SESSION) && isset($_SESSION['AuthNRequestID'])) {
            $requestID = $_SESSION['AuthNRequestID'];
        } else {
            $requestID = null;
        }

        $auth = Yii::app()->saml->getAuth();
        $auth->processResponse($requestID);
        if (!empty($errors)) {
            echo '<p>',implode(', ', $errors),'</p>';
        }

        if ($auth->isAuthenticated()) {
            $_SESSION['samlUserdata'] = $auth->getAttributes();
            $_SESSION['samlNameId'] = $auth->getNameId();
            $_SESSION['samlNameIdFormat'] = $auth->getNameIdFormat();
            $_SESSION['samlNameIdNameQualifier'] = $auth->getNameIdNameQualifier();
            $_SESSION['samlNameIdSPNameQualifier'] = $auth->getNameIdSPNameQualifier();
            $_SESSION['samlSessionIndex'] = $auth->getSessionIndex();
            unset($_SESSION['AuthNRequestID']);

            $login = new LoginForm();
            $login->user = DbUser::model()->findByAttributes(['email' => 'rm@completeintel.com']);
            if($login->forceLogin()) {
                //Yii::app()->format->debug($_SESSION);exit;
                $this->redirect(['site/index']);
            }
        }
    }

    public function actionMeta(){
        $meta = Yii::app()->saml->getMeta();
        $metadata = $meta->getSPMetadata();
        $errors = $meta->validateMetadata($metadata);
        if (empty($errors)) {
            header('Content-Type: text/xml');
            echo $metadata;
        } else {
            var_dump('Invalid SP metadata: '.implode(', ', $errors));
        }
    }

}