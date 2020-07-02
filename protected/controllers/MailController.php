<?php class MailController extends MailHashController {

    protected function beforeAction ($action) {
        $mailPages = array(
            'mail/viewBody',
        );
        if (in_array(Yii::app()->controller->id . '/' . Yii::app()->controller->action->id, $mailPages)) {
            $this->layout = 'mail';
        }
        $return = parent::beforeAction($action);

        return $return;
    }

    public function actionLink ($hash) {
        $model = DbMailHash::model()->findByAttributes(array(
            'hash' => $hash,
        ));
        if (!empty($model)) {
            if (!empty($model->action) && method_exists($this, $model->action)) {
                $this->mailHash = $model;
                $action = $model->action;
                $this->$action(); //methods contained in components/MailHashController
                Yii::app()->end();
            } else {
                $model->delete();
            }
        }
        Yii::app()->user->setFlash('danger', 'Email link has expired');
        $this->redirect(array('site/login'));
    }

    public function actionOutbox () {
        $outbox = new DbMailOutbox();
        $outbox->unsetAttributes();

        if (!empty($_REQUEST['DbMailOutbox'])) {
            $outbox->attributes = $_REQUEST['DbMailOutbox'];
        }


        $this->render('index', array(
            'outbox' => $outbox,
        ));
    }

    public function actionView ($id) {
        $outbox = DbMailOutbox::model()->findByPk($id);
        if (empty($trigger)) {
            $this->triggerError();
        }

        $this->performAjaxValidation($outbox);

        if (!empty($_POST['DbMailOutbox'])) {
            $outbox->attributes = $_POST['DbMailOutbox'];
            if ($outbox->save()) {
                Yii::app()->user->setFlash('success', 'Saved');
                $this->refresh();
            }
        }

        $this->render('view', array(
            'outbox' => $outbox,
        ));
    }

    public function actionTriggers () {
        $trigger = new DbMailTrigger();
        $trigger->unsetAttributes();

        if (!empty($_REQUEST['DbMailTrigger'])) {
            $trigger->attributes = $_REQUEST['DbMailTrigger'];
        }

        $this->render('triggers', array(
            'trigger' => $trigger,
        ));
    }

    public function actionTriggerAdd () {
        $trigger = new DbMailTrigger();

        $this->performAjaxValidation($trigger);

        if (!empty($_POST['DbMailTrigger'])) {
            $trigger->attributes = $_POST['DbMailTrigger'];
            if ($trigger->save()) {
                Yii::app()->user->setFlash('success', 'Saved');
                $this->redirect(array('mail/triggerView', 'id' => $trigger->id));
            }
        }

        $this->render('triggerAdd', array(
            'trigger' => $trigger,
        ));
    }

    public function actionTriggerView ($id) {
        $trigger = DbMailTrigger::model()->findByPk($id);
        if (empty($trigger)) {
            $this->triggerError();
        }

        $this->performAjaxValidation($trigger);

        if (!empty($_POST['DbMailTrigger'])) {
            $trigger->attributes = $_POST['DbMailTrigger'];
            if ($trigger->save()) {
                Yii::app()->user->setFlash('success', 'Saved');
                $this->refresh();
            }
        }

        $this->render('triggerView', array(
            'trigger' => $trigger,
        ));
    }

    public function actionCancel ($id) {
        $valid = false;
        $mail = DbMailOutbox::Model()->findByPk($id);
        if (!empty($mail)) {
            $mail->status = 'failed';
            $valid = $mail->save();
        }

        if ($valid && Yii::app()->request->isAjaxRequest) {
            header('Content-Type: application/json');
            echo CJSON::encode(array(
                'valid' => $valid,
            ));
            Yii::app()->end();
        }

        if ($valid) {
            Yii::app()->user->setFlash('success', 'Email cancelled');
        } else {
            Yii::app()->user->setFlash('danger', 'Failed to cancel email');
        }
        $this->redirect(array('mail/view', 'id' => $id));
    }

    public function actionClearSession () {
        unset(Yii::app()->session['pending-mail']);
    }

    public function actionGetSession () {
        header('Content-Type: application/json');
        echo CJSON::encode(array(
            'view' => $this->widget('application.widgets.pendingMailWidget', array(
                'view' => 'mailList',
                'clear' => false,
            ), true),
        ));
    }

    public function actionViewBody ($id) {
        $outbox = DbMailOutbox::Model()->findByPk($id);
        if (!empty($outbox)) {
            $mail = new Mail();
            echo $mail->emogrify($outbox->body);
        }
    }

}