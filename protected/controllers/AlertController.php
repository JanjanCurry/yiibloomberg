<?php

class AlertController extends Controller {

    public function actionAdd () {
        $this->checkAccess('user-manage');
        $model = new FormAddAlert();

        if (!empty($_POST['FormAddAlert'])) {
            $model->attributes = $_POST['FormAddAlert'];
            if ($model->save()) {
                Yii::app()->user->setFlash('success', 'Notifications Sent');
                $this->refresh();
            }
        }

        $this->render('add', array(
            'model' => $model,
        ));
    }

    public function actionIndex(){
        $model = new DbMessage();
        $model->userId = $this->user->id;

        $criteria = new CDbCriteria();
        $criteria->compare('userId', $this->user->id);
        $criteria->compare('status', '<=2');
        $messages = DbMessage::model()->findAll($criteria);
        if (!empty($messages)) {
            foreach ($messages as $message) {
                $message->status = 3;
                $message->save();
            }
        }

        $this->render('index', array(
            'model' => $model,
        ));
    }

    public function actionIndexAll(){
        $this->checkAccess('user-manage');
        $model = new DbMessage();

        $this->render('indexAll', array(
            'model' => $model,
        ));
    }

    public function actionSeen () {
        $criteria = new CDbCriteria();
        $criteria->compare('userId', $this->user->id);
        $criteria->compare('status', 1);
        $models = DbMessage::model()->findAll($criteria);
        if (!empty($models)) {
            foreach ($models as $model) {
                $model->status = 2;
                $model->save();
            }
        }
    }

    public function actionView ($id) {
        $return = [
            'valid' => false,
            'message' => '',
            'list' => '',
        ];
        $model = DbMessage::model()->findByPk($id);
        if (!empty($model) && $model->userId == $this->user->id) {
            if ($model->status != 3) {
                $model->status = 3;
                $model->save();
            }
            $return['valid'] = true;
            $return['message'] = '<p>'.nl2br($model->message).'</p><p class="alert-item-footer">'.Yii::app()->format->date($model->created).'</p>';
        }

        header('Content-Type: application/json');
        echo CJSON::encode($return);
    }

}