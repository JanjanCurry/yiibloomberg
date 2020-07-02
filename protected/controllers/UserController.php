<?php class UserController extends Controller {

    public function actionAdd () {
        $this->checkAccess('user-manage');
        $user = new DbUser();

        $this->performAjaxValidation($user);

        if (!empty($_POST['DbUser'])) {
            $user->attributes = $_POST['DbUser'];
            if ($user->save()) {
                $user->setDefaultFavorites();
                Yii::app()->user->setFlash('success', 'Account created');
                $this->redirect(array('user/edit', 'id' => $user->id));
            }
        }

        $this->render('add', array(
            'user' => $user,
        ));
    }

    public function actionAddWp () {
        $valid = false;
        $data = $errors = array();
        /*$example = [
            'password' => '',
            'method' => '',
            'subscription' => [
                [
                    'tool' => 'com',
                    'level' => 'ess',
                    'expire' => null,
                ],
            ],
            'company' => '',
            'fName' => '',
            'sName' => '',
            'phone' => '',
            'email' => '',
            'address' => '',
        ];*/

        if ($_SERVER['REMOTE_ADDR'] == '127.0.0.1' || $_SERVER['REMOTE_ADDR'] == '5.153.251.91') {
            if (!empty($_POST['xdata'])) {
                Yii::log('AddWp xdata: ' . $_POST['xdata'], 'error', 'system.web.UserController');
                if (get_magic_quotes_gpc()) {
                    $data = stripslashes($_POST['xdata']);
                } else {
                    $data = $_POST['xdata'];
                }
                $data = CJSON::decode($data, true);
                if (!empty($data)) {
                    if (!empty($data['password']) && trim($data['password']) == 'mJ61{]}2SJkX)02R9eUjZ)52zIYiTU') {
                        $valid = true;
                        $existing = false;

                        if (!empty($data['email'])) {
                            $user = DbUser::model()->findByAttributes(array('email' => base64_decode($data['email'])));
                            if (!empty($user)) {
                                $existing = true;
                                if (empty($data['method']) || $data['method'] != 'upgrade') {
                                    $valid = false;
                                    $errors[] = 'Attempted to add duplicate account for ' . base64_decode($data['email']);
                                }
                            } elseif (!empty($data['method']) && $data['method'] == 'upgrade') {
                                $valid = false;
                                $errors[] = 'Attempted to upgrade an account that does not yet exist using email ' . base64_decode($data['email']);
                            }
                        }

                        if ($valid) {
                            if (empty($user)) {
                                $user = new DbUser();
                            }

                            $attrs = array(
                                'email',
                                'company',
                                'fName',
                                'sName',
                                'phone',
                                'address',
                            );
                            foreach ($attrs as $attr) {
                                if (!empty($data[$attr])) {
                                    $user->$attr = base64_decode($data[$attr]);
                                }
                            }
                            $user->type = $data['type'];
                            $user->terms = 1;

                            $valid = $user->save();

                            if ($valid) {
                                $user->setDefaultFavorites();
                                if (!empty($data['subscription']) && is_array($data['subscription'])) {
                                    foreach ($data['subscription'] as $sub) {
                                        $subValid = true;
                                        if (empty($sub['tool']) || !array_key_exists($sub['tool'], DbUserService::model()->listTool())) {
                                            $subValid = false;
                                        }

                                        if (empty($sub['level']) || !array_key_exists($sub['level'], DbUserService::model()->listLevel())) {
                                            $subValid = false;
                                        }

                                        $expire = (!empty($sub['expire']) ? $sub['expire'] : null);

                                        if ($subValid) {
                                            $subValid = DbUserService::add($user->id, $sub['tool'], $sub['level'], $expire);
                                        }

                                        if (!$subValid) {
                                            //$valid = false;
                                            $errors[] = 'Failed to assign access to ' . DbUserService::model()->getListLabel('listTool', $sub['tool']) . ' ' . DbUserService::model()->getListLabel('listLevel', $sub['level']);
                                        }
                                    }
                                }

                                if ($valid) {
                                    if (empty($data['method']) || $data['method'] != 'upgrade') {
                                        $mail = new Mail();
                                        if (!$existing) {
                                            $valid = $mail->addMail($user, 'create-password');
                                            if (!$valid) {
                                                $errors[] = 'Failed to send password creation email';
                                            }
                                        }
                                    }
                                }

                            } else {
                                if (!empty($user->errors)) {
                                    foreach ($user->errors as $error) {
                                        if (is_array($error)) {
                                            $error = implode(', ', $error);
                                        }
                                        $errors[] = 'Save Error: ' . $error;
                                    }
                                } else {
                                    $errors[] = 'Failed to save user for unknown reason';
                                }
                            }
                        }
                    } else {
                        if (empty($data['password'])) {
                            $errors[] = 'Missing Admin Password';
                        } else {
                            $errors[] = 'Invalid Admin Password: ' . $data['password'];
                        }
                    }
                } else {
                    $errors[] = 'Empty JSON data after decode';
                }
            } else {
                $errors[] = 'Invalid JSON Data';
            }
        } else {
            $errors[] = 'Invalid IP Address: ' . $_SERVER['REMOTE_ADDR'];
        }

        if ($valid) {
            $valid = 'SUCCESS';
        } else {
            $valid = 'FAILED';
            if (!empty($errors)) {
                Yii::log('AddWp Errors: ' . implode(' | ', $errors), 'error', 'system.web.UserController');
            }
        }

        header('Content-Type: application/json');
        echo CJSON::encode(array(
            'valid' => $valid,
            'script' => 'AddWp',
            'errors' => $errors,
            'data' => $data,
            'post' => $_POST,
        ));
    }

    public function actionCleanFavorites ($id) {
        $this->checkAccess('user-manage');
        $user = DbUser::model()->findByPk($id);
        if (empty($user)) {
            $this->redirect(array('site/error'));
        } elseif ($user->type == 'dev' && !Yii::app()->user->checkAccess('dev')) {
            $this->redirect(array('user/index'));
        }

        $maintenance = new Maintenance();
        $maintenance->cleanFavorites($user->id);
        Yii::app()->user->setFlash('success', 'Favorites Repaired');
        $this->redirect(array('user/edit', 'id' => $id));
    }

    public function actionDefaultFavorites ($id) {
        $this->checkAccess('user-manage');
        $user = DbUser::model()->findByPk($id);
        if (empty($user)) {
            $this->redirect(array('site/error'));
        } elseif ($user->type == 'dev' && !Yii::app()->user->checkAccess('dev')) {
            $this->redirect(array('user/index'));
        }

        $user->setDefaultFavorites();
        Yii::app()->user->setFlash('success', 'Default Favorites Added');
        $this->redirect(array('user/edit', 'id' => $id));
    }

    public function actionEdit ($id) {
        $this->checkAccess('user-manage');
        $user = DbUser::model()->findByPk($id);
        if (empty($user)) {
            $this->redirect(array('site/error'));
        }elseif ($user->type == 'dev' && !Yii::app()->user->checkAccess('dev')){
            $this->redirect(array('user/index'));
        }

        $service = new DbUserService();

        $this->performAjaxValidation($user);

        if (!empty($_POST['DbUser'])) {
            $user->attributes = $_POST['DbUser'];

            if (!empty($_POST['subscription'])) {
                foreach ($_POST['subscription'] as $ref) {
                    $parts = explode('-', $ref);
                    if (!empty($parts) && !empty($parts[0]) && !empty($parts[1])) {
                        DbUserService::add($user->id, $parts[0], $parts[1], (!empty($_POST['subscription-expire']) ? $_POST['subscription-expire'] : null));
                    }
                }
            }

            if ($user->save()) {
                Yii::app()->user->setFlash('success', 'Saved');
                $this->refresh();
            }

        }

        $this->render('edit', array(
            'service' => $service,
            'user' => $user,
        ));
    }

    public function actionExistsWp () {
        $valid = false;
        $data = $errors = array();
        $type = '';

        /*$example = [
            'password' => '',
            //'subscription' => 'com-pro',
            'email' => 'test@example.com',
        ];*/

        if ($_SERVER['REMOTE_ADDR'] == '127.0.0.1' || $_SERVER['REMOTE_ADDR'] == '5.153.251.91') {
            if (!empty($_POST['xdata'])) {
                Yii::log('ExistsWp xdata: ' . $_POST['xdata'], 'error', 'system.web.UserController');
                if (get_magic_quotes_gpc()) {
                    $data = stripslashes($_POST['xdata']);
                } else {
                    $data = $_POST['xdata'];
                }
                $data = CJSON::decode($data, true);

                if (!empty($data)) {
                    if (!empty($data['password']) && $data['password'] == 'mJ61{]}2SJkX)02R9eUjZ)52zIYiTU') {
                        if (!empty($data['email'])) {
                            $user = DbUser::model()->findByAttributes(array('email' => base64_decode($data['email'])));
                            if (!empty($user)) {
                                $type = $user->type;
                                $valid = true;

                                if (!empty($data['subscription'])) {
                                    $valid = Yii::app()->authManager->isAssigned('sub-' . $data['subscription'], $user->id);
                                }
                            } else {
                                $errors[] = 'User Not Found with email ' . base64_decode($data['email']);
                            }
                        } else {
                            $errors[] = 'Empty email address';
                        }
                    } else {
                        if (empty($data['password'])) {
                            $errors[] = 'Missing Admin Password';
                        } else {
                            $errors[] = 'Invalid Admin Password: ' . $data['password'];
                        }
                    }
                } else {
                    $errors[] = 'Empty JSON data after decode';
                }
            } else {
                $errors[] = 'Invalid JSON Data';
            }
        } else {
            $errors[] = 'Invalid IP Address' . $_SERVER['REMOTE_ADDR'];
        }

        if ($valid) {
            $valid = 'SUCCESS';
        } else {
            $valid = 'FAILED';
            if (!empty($errors)) {
                Yii::log('ExistsWp Errors: ' . implode(' | ', $errors), 'error', 'system.web.UserController');
            }
        }

        header('Content-Type: application/json');
        echo CJSON::encode(array(
            'valid' => $valid,
            'type' => $type,
            'script' => 'ExistsWp',
            'errors' => $errors,
            'data' => $data,
            'post' => $_POST,
        ));
    }

    public function actionExport(){
        $this->checkAccess('user-manage');
        $model = new FormUserExport();

        if(!empty($_POST['FormUserExport'])){
            $model->attributes = $_POST['FormUserExport'];
            $model->save();
        }

        $this->render('export', array(
            'model' => $model,
        ));
    }

    public function actionIndex () {
        $this->checkAccess('user-manage');
        $user = new DbUser();
        $user->unsetAttributes();

        if (!empty($_POST['DbUser'])) {
            $user->attributes = $_POST['DbUser'];
        }

        $this->render('index', array(
            'user' => $user,
        ));
    }

    public function actionLogins($id = null){
        $this->checkAccess('user-manage');

        if(!empty($id)){
            $locks = DbUserLoginLock::model()->findAllByAttributes([
                'userId' => $id,
            ]);
            if(!empty($locks)){
                foreach($locks as $lock){
                    $lock->delete();
                }
            }
            /*DbUserLoginLock::model()->deleteAllByAttributes([
                'userId' => $id,
            ]);*/
            $this->redirect(['user/logins']);
        }

        $lock = new DbUserLoginLock();
        $lock->unsetAttributes();

        if (!empty($_POST['DbUserLoginLock'])) {
            $user->attributes = $_POST['DbUserLoginLock'];
        }

        $this->render('logins', array(
            'lock' => $lock,
        ));
    }

    public function actionDelete ($id) {
        $this->checkAccess('user-manage');
        $user = DbUser::model()->findByPk($id);
        if (empty($user)) {
            $this->redirect(array('site/error'));
        }
        if ($user->delete()) {
            Yii::app()->user->setFlash('success', 'Deleted');
            $this->redirect(array('user/index'));
        }
    }

    public function actionTourLoginToggle () {
        $this->user->tourLogin = 0;
        if ($_POST['active'] == 1) {
            $this->user->tourLogin = 1;
        }
        $this->user->save(false, array('tourLogin'));
    }

    public function actionLog () {
        $this->checkAccess('user-manage');
        $user = new DbUserLog();
        $user->unsetAttributes();

        if (!empty($_POST['DbUserLog'])) {
            $user->attributes = $_POST['DbUserLog'];
        }

        $this->render('log', array(
            'user' => $user,
        ));
    }

    public function actionTourToggle () {
        if ($this->user->tour == 1) {
            $this->user->tour = 0;
        } else {
            $this->user->tour = 1;
        }
        $this->user->save(false, array('tour'));

        if (empty($_RE['ajax'])) {
            if (!empty($_GET['returnUrl'])) {
                $this->redirect(base64_decode($_GET['returnUrl']));
            } else {
                $this->redirect(array('site/index'));
            }
        }
    }

    public function actionSendPasswordCreate ($id) {
        $this->checkAccess('user-manage');
        $user = DbUser::model()->findByPk($id);
        if (empty($user)) {
            $this->redirect(array('site/error'));
        }
        $mail = new Mail();
        $valid = $mail->addMail($user, 'create-password');

        if ($valid) {
            Yii::app()->user->setFlash('success', 'Email sent');
        } else {
            Yii::app()->user->setFlash('danger', 'Failed to send email');
        }
        $this->redirect(array('user/edit', 'id' => $id));
    }

    public function actionSendPasswordReset ($id) {
        $this->checkAccess('user-manage');
        $user = DbUser::model()->findByPk($id);
        if (empty($user)) {
            $this->redirect(array('site/error'));
        }
        $mail = new Mail();
        $valid = $mail->addMail($user, 'forgot-password');

        if ($valid) {
            Yii::app()->user->setFlash('success', 'Email sent');
        } else {
            Yii::app()->user->setFlash('danger', 'Failed to send email');
        }
        $this->redirect(array('user/edit', 'id' => $id));
    }

    public function actionView () {
        $user = DbUser::model()->findByPk(Yii::app()->user->id);
        if (empty($user)) {
            $this->redirect(array('site/error'));
        }

        if (!empty($_POST['DbUser'])) {
            $user->scenario = 'limitAccess';
            $user->attributes = $_POST['DbUser'];
            if ($user->save()) {
                Yii::app()->user->setFlash('success', 'Saved');
                $this->refresh();
            }
        }

        $this->render('view', array(
            'user' => $user,
        ));
    }

    /*public function actionWatchlist () {
        $return = array();
        $chart = $action = $valid = false;

        if (!empty($_REQUEST['id']) && !empty($_REQUEST['type']) && Yii::app()->user->checkAccess('favorites')) {
            $task = (!empty($_REQUEST['task']) ? $_REQUEST['task'] : null);
            $action = DbUserWatchlist::updateByType($_REQUEST['id'], $_REQUEST['type'], $task);
            if (!empty($action)) {
                $valid = true;
                if ($action == 'assign') {
                    $criteria = new CDbCriteria();
                    $criteria->compare('userId', $this->user->id);
                    $criteria->compare('type', $_REQUEST['type']);
                    $criteria->compare('refType', 'DbReporters');
                    $criteria->compare('refId', $_REQUEST['id']);
                    $watchlistItem = DbUserWatchlist::model()->find($criteria);

                    $chart = $this->widget('application.widgets.TradeWidget', array(
                        'view' => 'group',
                        'reporter' => $watchlistItem->ref,
                        'showTable' => false,
                        'showModal' => false,
                        'col' => 'col-sm-12',
                    ));

                    if (!empty($chart)) {
                        $return['html'] = $chart->results;
                        $return['chartId'] = $chart->chartId;
                    }
                }
            }
            $return['action'] = $action;
        }

        $return['valid'] = $valid;
        header('Content-Type: application/json');
        echo CJSON::encode($return);
    }

    public function actionWatchlistOrder () {
        if (!empty($this->user) && !empty($_REQUEST['type']) && !empty($_REQUEST['order'])) {

            $criteria = new CDbCriteria;
            $criteria->compare('userId', $this->user->id);
            $criteria->compare('type', $_REQUEST['type']);
            $items = DbUserWatchlist::model()->findAll($criteria);

            $order = explode(',', $_REQUEST['order']);

            if (!empty($order) && !empty($items)) {
                foreach ($order as $i => $itemId) {
                    foreach ($items as $item) {
                        if ($item->id == $itemId) {
                            $item->orderId = ($i + 1);
                            $test = $item->save();
                            break;
                        }
                    }
                }
            }
        }
    }*/

    public function actionToggleFavorites () {
        $valid = false;
        $action = '';

        if (Yii::app()->user->checkToolAccess(DbUserService::convertTool($_POST['favorite-type']), 'favorites')) {
            if (!DbUserFavorites::model()->isAssigned(Yii::app()->user->id, $_POST['favorite-type'], $_POST)) {
                $action = 'add';
                $valid = DbUserFavorites::model()->assign(Yii::app()->user->id, $_POST['favorite-type'], $_POST);
            } else {
                $action = 'remove';
                $valid = DbUserFavorites::model()->unassign(Yii::app()->user->id, $_POST['favorite-type'], $_POST);
            }
        }

        header('Content-Type: application/json');
        echo CJSON::encode(array(
            'valid' => $valid,
            'action' => $action,
        ));
    }

    public function actionAddFavorite () {
        $return = array();
        $valid = false;

        if (!empty($_POST['favorite-type']) && Yii::app()->user->checkToolAccess(DbUserService::convertTool($_POST['favorite-type']), 'favorites')) {
            $valid = DbUserFavorites::model()->assign(Yii::app()->user->id, $_POST['favorite-type'], $_POST);

            if ($valid) {
                $chartOptions = array(
                    'view' => 'group',
                    'init' => false,
                    'showTable' => false,
                    'showModal' => false,
                    'showLegend' => false,
                    'showEmpty' => true,
                );
                foreach (DbUserFavorites::model()->listDataAttrs($_POST['favorite-type']) as $attr) {
                    if (!empty($_POST[$attr])) {
                        $chartOptions[$attr] = $_POST[$attr];
                    }
                }

                switch ($_POST['favorite-type']) {
                    case 'commodity':
                    case 'currency':
                    case 'equity':
                        $chart = $this->widget('application.widgets.MarketWidget', $chartOptions);
                        break;

                    case 'macro':
                        $chart = $this->widget('application.widgets.MacroWidget', $chartOptions);
                        break;

                    case 'trade':
                        $chart = $this->widget('application.widgets.TradeWidget', $chartOptions);
                        break;
                }

                if (!empty($chart->results)) {
                    $valid = true;
                    $return['html'] = $chart->results;
                    $return['chartId'] = $chart->chartId;
                } else {
                    $valid = false;
                    $return['error'] = $chart->error;
                }
            } else {
                $return['error'][] = 'Failed to add favorite, please check that the report is not already in your favorites';
            }
        } else {
            $return['error'][] = 'Your account does not allow access to add favorites.';
        }


        $return['valid'] = $valid;
        header('Content-Type: application/json');
        echo CJSON::encode($return);
    }

    public function actionDashFavorite(){
        if(!empty($_POST['type'])){
            if(!empty($_POST['old'])) {
                $valid = false;
                $favorites = DbUserDash::model()->findAllByAttributes([
                    'type' => $_POST['type'],
                    'userId' => Yii::app()->user->id,
                ]);
                if (!empty($favorites)) {
                    foreach ($favorites as $favorite) {
                        if (isset($_POST['old']['market']) && isset($favorite->data['market'])) {
                            if ($favorite->data['item'] == $_POST['old']['item'] && $favorite->data['market'] == $_POST['old']['market']) {
                                $data = $favorite->data;
                                $data['item'] = $_POST['new']['item'];
                                $data['market'] = $_POST['new']['market'];
                                $favorite->data = $data;
                                $favorite->save();
                                $valid = true;
                            }
                        } else if (isset($_POST['old']['macro']) && isset($favorite->data['macro'])) {
                            if ($favorite->data['macro'] == $_POST['old']['macro']) {
                                $data = $favorite->data;
                                $data['macro'] = $_POST['new']['macro'];
                                $favorite->data = $data;
                                $favorite->save();
                                $valid = true;
                            }
                        }
                    }
                }
            }

            if(!$valid){
                DbUserDash::model()->assign(Yii::app()->user->id, $_POST['type'], [
                    'item' => $_POST['new']['item'],
                    'market' => $_POST['new']['market'],
                ]);
            }
        }
    }

    public function actionServiceDelete ($id) {
        $this->checkAccess('user-manage');
        $service = DbUserService::model()->findByPk($id);
        if (empty($service)) {
            $this->redirect(array('site/error'));
        }
        $user = $service->user;
        if (!empty($user) && $service->delete()) {
            $user->save();
            Yii::app()->user->setFlash('success', 'Deleted');
            $this->redirect(array('user/edit', 'id' => $user->id));
        }
        Yii::app()->user->setFlash('danger', 'Delete Failed, invalid user');
        $this->redirect(array('user/index'));
    }


    public function actionServiceEdit ($id) {
        $this->checkAccess('user-manage');
        $service = DbUserService::model()->findByPk($id);
        if (empty($service)) {
            $this->redirect(array('site/error'));
        }

        $this->performAjaxValidation($service);

        if (!empty($_POST['DbUserService'])) {
            $service->attributes = $_POST['DbUserService'];

            if ($service->save() && $service->user->save()) {
                Yii::app()->user->setFlash('success', 'Saved');
                $this->refresh();
            }

        }

        $this->render('serviceEdit', array(
            'service' => $service,
        ));
    }

}