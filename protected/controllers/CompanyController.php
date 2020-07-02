<?php class CompanyController extends Controller {

    public function actionAdd(){
        $this->checkAccess('user-manage');
        $model = new DbCompany();

        $this->performAjaxValidation($model);

        if (!empty($_POST['DbCompany'])) {
            $model->attributes = $_POST['DbCompany'];
            if ($model->save()) {
                Yii::app()->user->setFlash('success', 'Account created');
                $this->redirect(array('company/edit', 'id' => $model->id));
            }
        }

        $this->render('add', array(
            'model' => $model,
        ));
    }

    public function actionEdit($id){
        $this->checkAccess('user-manage');
        $model = DbCompany::model()->findByPk($id);
        if (empty($model)) {
            $this->redirect(array('site/error'));
        }

        $this->performAjaxValidation($model);

        if (!empty($_POST['DbCompany'])) {
            $model->attributes = $_POST['DbCompany'];
            if ($model->save()) {
                Yii::app()->user->setFlash('success', 'Account created');
                $this->refresh();
            }
        }

        $this->render('edit', array(
            'model' => $model,
        ));
    }

    public function actionIndex () {
        $this->checkAccess('user-manage');
        $model = new DbCompany();
        $model->unsetAttributes();

        if (!empty($_POST['DbCompany'])) {
            $model->attributes = $_POST['DbCompany'];
        }

        /*$users = DbUser::model()->findAll();
        if(!empty($users)){
            foreach($users as $user){
                if(!empty($user->company)) {
                    $company = DbCompany::add($user->company);
                    $user->companyId = $company->id;
                    $user->save();
                }
            }
        }*/

        $this->render('index', array(
            'model' => $model,
        ));
    }

    public function actionDelete ($id) {
        $this->checkAccess('user-manage');
        $model = DbCompany::model()->findByPk($id);
        if (empty($model)) {
            $this->redirect(array('site/error'));
        }

        $users = DbUser::model()->findAllByAttributes(['companyId' => $id]);
        if(!empty($users)){
            foreach($users as $user){
                $user->companyId = null;
                $user->save();
            }
        }

        if ($model->delete()) {
            Yii::app()->user->setFlash('success', 'Deleted');
            $this->redirect(array('company/index'));
        }
    }


}