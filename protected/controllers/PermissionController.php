<?php class PermissionController extends Controller {

    protected function beforeAction ($action) {
        $return = parent::beforeAction($action);
        $this->checkAccess('access-manage');

        return $return;
    }

    public function actionIndex () {
        $this->render('index');
    }

    public function actionAdd ($type) {
        if (!empty($_POST[ucfirst($type)]['name'])) {
            if (!in_array($_POST[ucfirst($type)]['name'], array_keys(Yii::app()->authManager->{$type . 's'}))) {
                $authItem = Yii::app()->authManager->createAuthItem($_POST[ucfirst($type)]['name'], constant('CAuthItem::TYPE_' . strtoupper($type)), $_POST[ucfirst($type)]['description'], $_POST[ucfirst($type)]['bizRule']);
                if (!empty($authItem)) {
                    Yii::app()->user->setFlash('success', ucfirst($type) . ' "' . $authItem->name . '" added');
                    $this->redirect(array('permission/index'));
                } else {
                    Yii::app()->user->setFlash('error', ucfirst($type) . ' "' . $authItem->name . '" could not be created');
                    $this->refresh();
                }
            } else {
                Yii::app()->user->setFlash('error', ucfirst($type) . ' "' . $_POST[ucfirst($type)]['name'] . '" already exists');
                $this->refresh();
            }
        }

        $this->render('add', array(
            'type' => $type,
        ));
    }

    public function actionEdit ($type, $name) {
        $authItem = Yii::app()->authManager->getAuthItem($name);

        if (!empty($_POST[ucfirst($type)]['name'])) {
            $authItem->name = $_POST[ucfirst($type)]['name'];
            $authItem->description = !empty($_POST[ucfirst($type)]['description']) ? $_POST[ucfirst($type)]['description'] : '';
            $authItem->bizRule = !empty($_POST[ucfirst($type)]['bizRule']) ? $_POST[ucfirst($type)]['bizRule'] : '';

            Yii::app()->user->setFlash('success', ucfirst($type) . ' "' . $authItem->name . '" updated');
            $this->refresh();
        }

        $this->render('edit', array(
            'item' => $authItem,
            'type' => $type,
        ));
    }

    public function actionDelete ($name) {
        if (Yii::app()->authManager->removeAuthItem($name)) {
            Yii::app()->user->setFlash('success', $name . ' removed');
        } else {
            Yii::app()->user->setFlash('error', $name . ' could not be removed');
        }
        $this->redirect(array('permission/index'));
    }

    public function actionAddChild ($parent) {

        $parent = Yii::app()->authManager->getAuthItem($parent);

        if (!empty($_POST['Item'])) {
            if ($parent->addChild($_POST['Item'])) {
                Yii::app()->user->setFlash('success', '"' . $_POST['Item'] . '" added as a child to "' . $parent->name . '"');
            } else {
                Yii::app()->user->setFlash('error', '"' . $_POST['Item'] . '" could not be added');
            }

            $this->redirect(array('permission/edit', 'name' => $parent->name, 'type' => Yii::app()->authManager->getTypeLabel($parent->type)));
        }


        $this->render('addChild', array(
            'parent' => $parent,
        ));
    }

    public function actionDeleteChild ($parent, $child) {
        $parent = Yii::app()->authManager->getAuthItem($parent);
        if ($parent->removeChild($child)) {
            Yii::app()->user->setFlash('success', 'Unassigned item');
        } else {
            Yii::app()->user->setFlash('danger', 'Failed to unassign item');
        }
        $this->redirect(array('permission/edit', 'name' => $parent->name, 'type' => PermissionManager::getTypeLabel($parent->type)));
    }

}