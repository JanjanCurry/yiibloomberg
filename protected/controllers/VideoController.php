<?php

class VideoController extends Controller {

    protected function beforeAction ($action) {
        $return = parent::beforeAction($action);
        $this->checkAccess('admin');

        return $return;
    }

    public function actionAdd(){
        $model = new DbVideo();

        if(!empty($_POST['DbVideo'])){
            $model->attributes = $_POST['DbVideo'];
            if($model->save()){
                $model->assignCats();
                $this->redirect(['video/edit', 'id' => $model->id]);
            }
        }

        $this->render('add', [
            'model' => $model,
        ]);
    }

    public function actionEdit($id){
        $model = DbVideo::model()->findByPk($id);
        if(empty($model)){
            $this->redirect(['video/index']);
        }

        if(!empty($_POST['DbVideo'])){
            $model->attributes = $_POST['DbVideo'];
            if($model->save()){
                if(empty($_POST['DbVideo']['catIds'])){
                    DbVideoAssign::unassignAll($model->id);
                }
                $model->assignCats();
                $this->refresh();
            }
        }

        $this->render('edit', [
            'model' => $model,
        ]);
    }

    public function actionDelete($id){
        $model = DbVideo::model()->findByPk($id);
        if(!empty($model)){
            $model->delete();
        }
        $this->redirect(['video/index']);
    }

    public function actionIndex(){
        $model = new DbVideo();
        $model->unsetAttributes();

        if(!empty($_GET['DBVideo'])){
            $model->attributes = $_GET['DbVideo'];
        }

        $this->render('index', [
            'model' => $model,
        ]);
    }

    public function actionAddCat(){
        $model = new DbVideoCat();

        if(!empty($_POST['DbVideoCat'])){
            $model->attributes = $_POST['DbVideoCat'];
            if($model->save()){
                $this->redirect(['video/editCat', 'id' => $model->id]);
            }
        }

        $this->render('cat/add', [
            'model' => $model,
        ]);
    }

    public function actionEditCat($id){
        $model = DbVideoCat::model()->findByPk($id);
        if(empty($model)){
            $this->redirect(['video/indexCat']);
        }

        if(!empty($_POST['DbVideoCat'])){
            $model->attributes = $_POST['DbVideoCat'];
            if($model->save()){
                $this->refresh();
            }
        }

        $this->render('cat/edit', [
            'model' => $model,
        ]);
    }

    public function actionDeleteCat($id){
        $model = DbVideoCat::model()->findByPk($id);
        if(!empty($model)){
            $model->delete();
        }
        $this->redirect(['video/indexCat']);
    }

    public function actionIndexCat(){
        $model = new DbVideoCat();
        $model->unsetAttributes();

        if(!empty($_GET['DbVideoCat'])){
            $model->attributes = $_GET['DbVideoCat'];
        }

        $this->render('cat/index', [
            'model' => $model,
        ]);
    }

}