<?php

class AdminController extends Controller {

    protected function beforeAction ($action) {
        $return = parent::beforeAction($action);
        $this->checkAccess('admin');

        return $return;
    }

    public function actionCleanFavorites () {
        $maintenance = new Maintenance();
        $results = $maintenance->cleanFavorites();

        Yii::app()->user->setFlash('success', 'Removed ' . $results['removed'] . ' and updated ' . $results['updated'] . ' of ' . $results['total'] . ' favorites');
        $this->redirect(array('admin/index'));
    }

    public function actionConfidence () {
        $model = DbVar::model()->findByAttributes(['name' => 'market-confidence']);
        if (empty($model)) {
            $model = DbVar::add('market-confidence', 'market', []);
        }

        if (!empty($_POST['DbVar'])) {
            $model->attributes = $_POST['DbVar'];
            if ($model->save()) {
                Yii::app()->user->setFlash('success', 'Saved');
                $this->refresh();
            }
        }

        $this->render('confidence', [
            'model' => $model,
        ]);
    }

    public function actionCountryColor () {
        $countries = DbPartners::model()->findAll(array('order' => 'country ASC'));
        $temp = array();
        if (!empty($countries)) {
            foreach ($countries as $country) {
                $temp[$country->ccode3] = $country;
            }
        }
        $countries = $temp;

        if (!empty($_POST['DbPartners'])) {
            foreach ($_POST['DbPartners'] as $code => $color) {
                if (!empty($color['color']) && !empty($countries[$code])) {
                    $countries[$code]->color = str_replace(array('#', ' '), '', $color['color']);
                    if ($countries[$code]->save()) {
                        $reporter = DbReporters::model()->findByAttributes(array('ccode3' => $code));
                        if (!empty($reporter)) {
                            $reporter->color = str_replace(array('#', ' '), '', $color['color']);
                            $reporter->save();
                        }
                    }
                }
            }
            $this->refresh();
        }

        $this->registerFile('css', 'https://cdnjs.cloudflare.com/ajax/libs/spectrum/1.8.0/spectrum.min.css');
        $this->registerFile('js', 'https://cdnjs.cloudflare.com/ajax/libs/spectrum/1.8.0/spectrum.min.js');

        $this->render('countryColor', array(
            'countries' => $countries,
        ));
    }

    public function actionDbDates () {
        $model = DbVar::model()->findByAttributes(['name' => 'dbDates']);
        if (empty($model)) {
            $model = new DbVar();
            $model->name = 'dbDates';
            $model->type = 'system';
        }

        if (!empty($_POST)) {
            $model->data = [
                'forecastDate' => $_POST['forecastDate']
            ];
            if ($model->save()) {
                $this->refresh();
            }
        }

        $this->render('dbDates', [
            'model' => $model,
        ]);
    }

    public function actionErrorLog () {
        $this->checkAccess('dev');
        $file = 'protected/runtime/application.log';
        $log = '';
        if (file_exists($file)) {
            if (!empty($_POST) && !empty($_POST['clearLog'])) {
                unlink($file);
                $this->refresh();
            }
            $log = file_get_contents($file);
        }
        $this->render('errorLog', array(
            'log' => $log,
        ));
    }

    public function actionFlushCache () {
        $this->checkAccess('dev');
        if (Yii::app()->cache->flush()) {
            Yii::app()->user->setFlash('success', 'Cache flushed successfully');
            $this->updateAssetVersion();
        } else {
            Yii::app()->user->setFlash('danger', 'Cache has not been flushed.');
        }
        $this->redirect(array('admin/index'));
    }

    public function actionIndex () {
        $this->render('index');
    }

    public function actionMacroColor () {
        $macros = DbMacroList::model()->search(array('returnFormat' => 'array'));
        $temp = array();
        if (!empty($macros)) {
            foreach ($macros as $macro) {
                $temp[$macro->assetId] = $macro;
            }
        }
        $macros = $temp;

        if (!empty($_POST['DbMacroList'])) {
            foreach ($_POST['DbMacroList'] as $code => $color) {
                if (!empty($color['color']) && !empty($macros[$code])) {
                    $macros[$code]->color = str_replace(array('#', ' '), '', $color['color']);
                    $macros[$code]->save();
                }
            }
            $this->refresh();
        }

        $this->registerFile('css', 'https://cdnjs.cloudflare.com/ajax/libs/spectrum/1.8.0/spectrum.min.css');
        $this->registerFile('js', 'https://cdnjs.cloudflare.com/ajax/libs/spectrum/1.8.0/spectrum.min.js');

        $this->render('macroColor', array(
            'macros' => $macros,
        ));
    }

    public function actionPartnerGroups ($page = null) {
        $criteria = new CDbCriteria();
        $criteria->order = 'country ASC';
        $partners = DbPartners::model()->findAll($criteria);
        $tmp = $list = [];
        if(!empty($partners)){
            foreach ($partners as $partner) {
                $list[$partner->id] = $partner->code.': '.$partner->name;
            }
        }

        if(!empty($page)) {
            $criteria->addSearchCondition('country', $page . '%', false);
        }
        $partners = DbPartners::model()->findAll($criteria);
        if(!empty($partners)){
            foreach ($partners as $partner) {
                $tmp[$partner->id] = $partner;
            }
            $partners = $tmp;
        }

        if (!empty($_POST['processing-trigger']) && !empty($partners)) {
            foreach ($partners as $partner) {
                $childIds = $ignore = $add = $remove = [];

                if (!empty($_POST['groups']) && !empty($_POST['groups'][$partner->id])) {
                    $childIds = $_POST['groups'][$partner->id];
                }

                if(!empty($partner->childrenIdx)) {
                    foreach ($partner->childrenIdx as $child) {
                        if (!empty($childIds)) {
                            if(!in_array($child->childId, $childIds)){
                                $remove[] = $child;;
                            }else{
                                $ignore[] = $child->childId;
                            }
                        }else{
                            $remove[] = $child;
                        }
                    }
                }

                if (!empty($childIds)) {
                    foreach ($childIds as $childId){
                        if(!in_array($childId, $ignore)){
                            $add[] = $childId;
                        }
                    }
                }

                if(!empty($remove)){
                    foreach($remove as $child){
                        $child->delete();
                    }
                }
                if(!empty($add)){
                    foreach($add as $childId){
                        $model = new DbPartnerGroup();
                        $model->parentId = $partner->id;
                        $model->childId = $childId;
                        $model->save();
                    }
                }
            }
            $this->refresh();
        }

        $this->render('partnerGroups', array(
            'list' => $list,
            'partners' => $partners,
        ));
    }

    public function actionReporterGroups () {
        $reporters = DbReporters::model()->findAll(array('order' => 'country ASC'));

        if (!empty($_POST['DbReporters'])) {
            foreach ($_POST['DbReporters'] as $id => $data) {
                $types = array();
                if (!empty($data['type'])) {
                    foreach ($data['type'] as $type => $val) {
                        if (!empty($val)) {
                            $types[] = $type;
                        }
                    }
                }

                $reporter = DbReporters::model()->findByPk($id);
                if (!empty($reporter)) {
                    $reporter->type = $types;
                    $reporter->save();
                }
            }
            $this->refresh();
        }

        $this->render('reporterGroups', array(
            'reporters' => $reporters,
        ));
    }

    public function actionReporterAvailability () {
        $reporters = DbReporters::model()->findAll(array('order' => 'ccode3 ASC'));

        $this->render('reporterAvailability', array(
            'reporters' => $reporters,
        ));
    }

    public function actionReporterAvailabilityUpdate () {
        $return = array(
            'valid' => false,
        );

        if (!empty($_POST['country'])) {
            $cmd = new Maintenance();

            switch ($_POST['country']) {
                case 'dates':
                    $return['valid'] = $cmd->dateAvailability();
                    header('Content-Type: application/json');
                    echo CJSON::encode($return);
                    Yii::app()->end();
                    break;

                case 'commodity':
                case 'currency':
                case 'equity':
                    $type = $_POST['country'];
                    break;

                default:
                    $type = 'reporter';
            }

            $type = $_POST['type'];

            $data = $cmd->reporterAvailability($_POST['country'], $type);
            if (!empty($data)) {
                if (is_array($data)) {
                    $return['valid'] = $data['valid'];
                    foreach ($data as $key => $val) {
                        if ($key != 'valid') {
                            if(is_array($val)) {
                                $return[$key] = count($val);
                            }else{
                                $return[$key] = $val;
                            }
                        }
                    }
                } else {
                    $return['valid'] = $data;
                }
            }
        }

        header('Content-Type: application/json');
        echo CJSON::encode($return);
    }

    public function actionSyncAssets () {
        $maintenance = new Maintenance();
        $results = $maintenance->syncAssetNames();

        Yii::app()->user->setFlash('success', 'Found ' . $results['existing'] . ', added ' . $results['added'] . ' and removed ' . $results['removed'] . ' asset names');
        $this->redirect(array('admin/index'));
    }

    public function actionTinyMceImages () {
        $list = [];
        $path = YiiBase::getPathOfAlias('root') . '/images/notification/';
        $images = scandir($path, SCANDIR_SORT_ASCENDING);
        if (!empty($images)) {
            foreach ($images as $i => $image) {
                $file = $path . $image;
                // ignore files younger than 15 days old
                if ($file != '.' && $file != '..' && is_file($file)) {
                    //var_dump('Delete: '.$file);
                    $list[] = [
                        'title' => $image,
                        'value' => YiiBase::getPathOfAlias('website') . '/images/notification/' . $image,
                    ];
                } else {
                    //var_dump('Ignore: '.$file);
                }
            }
        }

        header('Content-Type: application/json');
        echo CJSON::encode($list);
    }

    public function actionTopAssets(){
        $m = new Maintenance();
        $data = $m->topAssets();
        DbVar::add('dash-top-assets', 'dash-data', $data);
        Yii::app()->user->setFlash('success', 'Top/Worst assets calculated');
        $this->redirect(['admin/index']);
    }
}