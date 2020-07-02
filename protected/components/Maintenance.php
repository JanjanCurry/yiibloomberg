<?php class Maintenance extends CComponent {

    public function cleanCsv () {
        $path = YiiBase::getPathOfAlias('root') . '/images/csv/';
        $images = scandir($path, SCANDIR_SORT_DESCENDING);
        if (!empty($images)) {
            foreach ($images as $i => $image) {
                $file = $path . $image;
                // ignore files younger than 1 hour old
                if ($file != '.' && $file != '..' && is_file($file) && preg_match("/csv$/", $file) && filectime($file) < (strtotime('-1 hour'))) {
                    //var_dump('Delete: '.$file);
                    unlink($file);  //remove the image
                } else {
                    //var_dump('Ignore: '.$file);
                }
            }
        }
    }

    public function cleanFavorites ($userId = null) {
        $total = $removed = $updated = 0;
        if (!empty($userId)) {
            $models = DbUserFavorites::model()->findAllByAttributes(['userId' => $userId]);
        } else {
            $models = DbUserFavorites::model()->findAll();
        }
        if (!empty($models)) {
            $total = count($models);
            foreach ($models as $model) {
                $valid = true;
                $update = false;
                $data = $model->data;

                if (!Yii::app()->authManager->checkAccess('tool-' . DbUserService::convertTool($model->type), $model->userId)) {
                    $valid = false;
                }

                if (in_array($model->type, array('commodity', 'currency', 'equity'))) {
                    if ($valid && !empty($data[$model->type])) {
                        $data['item'] = $data[$model->type];
                        unset($data[$model->type]);
                        $model->data = $data;
                        $model->save();
                    }

                    if ($valid && empty($data['item'])) {
                        $valid = false;
                    }


                    if ($valid && !empty($data['item'])) {
                        $criteria = new CDbCriteria();
                        $criteria->compare('code', $data['item']);

                        $temp = DbCommodities::model()->find($criteria);
                        if (empty($temp)) {
                            $temp = DbCurrencies::model()->find($criteria);
                            if (empty($temp)) {
                                $temp = DbEquities::model()->find($criteria);
                                if (empty($temp)) {
                                    $valid = false;
                                }
                            }
                        }
                    }

                    if ($valid && !empty($data['compare'])) {
                        $compareUpdate = array();
                        if (!is_array($data['compare'])) {
                            $data['compare'] = explode(',', $data['compare']);
                        }
                        foreach ($data['compare'] as $compare) {
                            $criteria = new CDbCriteria();
                            $criteria->compare('code', $compare);

                            $temp = DbCommodities::model()->find($criteria);
                            if (empty($temp)) {
                                $temp = DbCurrencies::model()->find($criteria);
                                if (empty($temp)) {
                                    $temp = DbEquities::model()->find($criteria);
                                }
                            }
                            if (!empty($temp)) {
                                $compareUpdate[] = $compare;
                            } else {
                                $update = true;
                            }
                        }
                        $data['compare'] = implode(',', $compareUpdate);
                    }
                }

                if ($model->type == 'macro') {
                    if ($valid && (empty($data['reporter']) || empty($data['macro']))) {
                        $valid = false;
                    }

                    if ($valid && !empty($data['reporter'])) {
                        $temp = DbReporters::model()->macro()->findByAttributes(array('ccode3' => $data['reporter']));
                        if (empty($temp)) {
                            $valid = false;
                        }
                    }

                    if ($valid && !empty($data['macro'])) {
                        $temp = DbMacroList::model()->findByAttributes(array('assetId' => $data['macro']));
                        if (empty($temp)) {
                            $valid = false;
                        }
                    }

                    if ($valid && !empty($data['reporter']) && !empty($data['macro'])) {
                        $temp = DbAllAnnMacros::model()->findByAttributes(array(
                            'RISO3' => $data['reporter'],
                            'Asset_ID' => $data['macro'],
                        ));
                        if (empty($temp)) {
                            $valid = false;
                        }
                    }

                    if ($valid && !empty($data['compare'])) {
                        $compareUpdate = array();
                        if (!is_array($data['compare'])) {
                            $data['compare'] = explode(',', $data['compare']);
                        }
                        foreach ($data['compare'] as $compare) {
                            $temp = DbMacroList::model()->findByAttributes(array('assetId' => $compare));
                            if (!empty($temp)) {
                                $compareUpdate[] = $compare;
                            } else {
                                $update = true;
                            }
                        }
                        $data['compare'] = implode(',', $compareUpdate);
                    }

                }

                if ($model->type == 'trade') {
                    if ($valid && empty($data['reporter'])) {
                        $valid = false;
                    }

                    if ($valid && !empty($data['reporter'])) {
                        $temp = DbReporters::model()->trade()->findByAttributes(array('ccode3' => $data['reporter']));
                        if (empty($temp)) {
                            $valid = false;
                        }
                    }

                    if ($valid && !empty($data['compare'])) {
                        $compareUpdate = array();
                        if (!is_array($data['compare'])) {
                            $data['compare'] = explode(',', $data['compare']);
                        }
                        foreach ($data['compare'] as $compare) {
                            $temp = DbReporters::model()->trade()->findByAttributes(array('ccode3' => $compare));
                            if (!empty($temp)) {
                                $compareUpdate[] = $compare;
                            } else {
                                $update = true;
                            }
                        }
                        $data['compare'] = implode(',', $compareUpdate);
                    }
                }

                if (!$valid) {
                    if ($model->delete()) {
                        $removed++;
                    }
                } else if ($update) {
                    $model->data = $data;
                    if ($model->save()) {
                        $updated++;
                    }
                }
            }
        }

        if (!empty($userId)) {
            $models = DbUserDash::model()->findAllByAttributes(['userId' => $userId]);
        } else {
            $models = DbUserDash::model()->findAll();
        }
        if(!empty($models)){
            foreach ($models as $model){
                $total++;
                $item = null;
                if(!empty($model->data['market'])){
                    switch ($model->data['market']){
                        case 'commodity':
                            $item = DbCommodities::model()->findByAttributes(['code' => $model->data['item']]);
                            break;
                        case 'currency':
                            $item = DbCurrencies::model()->findByAttributes(['code' => $model->data['item']]);
                            break;
                        case 'equity':
                            $item = DbEquities::model()->findByAttributes(['code' => $model->data['item']]);
                            break;
                    }
                    if(empty($item)){
                        $model->delete();
                        $removed++;
                    }
                }
            }
        }

        return array(
            'removed' => $removed,
            'updated' => $updated,
            'total' => $total,
        );
    }

    public function cleanImages () {
        $path = YiiBase::getPathOfAlias('root') . '/images/charts/';
        $images = scandir($path, SCANDIR_SORT_DESCENDING);
        if (!empty($images)) {
            foreach ($images as $i => $image) {
                $file = $path . $image;
                // ignore files younger than 15 days old
                if ($file != '.' && $file != '..' && is_file($file) && preg_match("/png$/", $file) && filectime($file) < (strtotime('-15 days'))) {
                    //var_dump('Delete: '.$file);
                    unlink($file);  //remove the image
                } else {
                    //var_dump('Ignore: '.$file);
                }
            }
        }
    }

    public function cleanLog () {
        if (file_exists('/home/millerst/public_html/error_log')) {
            unlink('/home/millerst/public_html/error_log');
        }
    }

    public function cleanReports () {
        $path = YiiBase::getPathOfAlias('root') . '/images/reports/';
        $images = scandir($path, SCANDIR_SORT_DESCENDING);
        if (!empty($images)) {
            foreach ($images as $i => $image) {
                $file = $path . $image;
                // ignore files younger than 1 hour old
                if ($file != '.' && $file != '..' && is_file($file) && preg_match("/pdf$/", $file) && filectime($file) < (strtotime('-30 min'))) {
                    //var_dump('Delete: '.$file);
                    unlink($file);  //remove the image
                } else {
                    //var_dump('Ignore: '.$file);
                }
            }
        }
    }

    public function cleanUserLog () {
        $criteria = new CDbCriteria;
        $criteria->compare('created', '<=' . strtotime('-48 hours'));
        DbUserLoginAttempt::model()->deleteAll($criteria);

        $criteria = new CDbCriteria;
        $criteria->compare('updated', '<=' . strtotime('-1 hour'));
        DbUserLoginLock::model()->deleteAll($criteria);

        $criteria = new CDbCriteria;
        $criteria->compare('updated', '<=' . strtotime('-14 days'));
        DbMailHash::model()->deleteAll($criteria);
    }

    public function topAssets () {
        $limit = 5;
        $current = 'M' . date('Ym');
        $prev = 'M' . date('Ym', strtotime('-1 month'));
        $results = $data = [
            'com' => [],
            'cur' => [],
            'equ' => [],
        ];
        $results['all'] = [];

        $criteria = new CDbCriteria();
        $criteria->select = ['Asset_ID', $current, $prev];
        $criteria->compare('type', 'Mid');

        $models = DbAllMrkComResults::model()->findAll($criteria);
        if (!empty($models)) {
            foreach ($models as $model) {
                if (!empty($model->commodity) && !empty($model->$prev) && $model->$prev != 0) {
                    $data['com'][$model->Asset_ID] = (($model->$current - $model->$prev) / $model->$prev) * 100;
                }
            }
        }

        $models = DbAllMrkCurResults::model()->findAll($criteria);
        if (!empty($models)) {
            foreach ($models as $model) {
                if (!empty($model->currency) && !empty($model->$prev) && $model->$prev != 0) {
                    //restrict to USD currencies only
                    if (strpos($model->Asset_ID, 'USD') !== false) {
                        $data['cur'][$model->Asset_ID] = (($model->$current - $model->$prev) / $model->$prev) * 100;
                    }
                }
            }
        }

        $models = DbAllMrkEqiResults::model()->findAll($criteria);
        if (!empty($models)) {
            foreach ($models as $model) {
                if (!empty($model->equity) && !empty($model->$prev) && $model->$prev != 0) {
                    $data['equ'][$model->Asset_ID] = (($model->$current - $model->$prev) / $model->$prev) * 100;
                }
            }
        }

        foreach ($data as $group => $items) {
            $results[$group] = [
                'top' => [],
                'bottom' => [],
            ];

            asort($items);
            $i = 0;
            foreach ($items as $key => $val) {
                $results[$group]['bottom'][$key] = $val;
                $results['all']['bottom'][$group . '|' . $key] = $val;
                $i++;
                if ($i >= $limit) {
                    break;
                }
            }

            $items = array_reverse($items);
            $i = 0;
            foreach ($items as $key => $val) {
                $results[$group]['top'][$key] = $val;
                $results['all']['top'][$group . '|' . $key] = $val;
                $i++;
                if ($i >= $limit) {
                    break;
                }
            }

        }

        //Yii::app()->format->debug($results, true);
        return $results;
    }

    public function dateAvailability () {
        $data = array();
        $types = array(
            'commodity' => array(
                'DbAllMrkComResults',
            ),
            'currency' => array(
                'DbAllMrkCurResults',
            ),
            'equity' => array(
                'DbAllMrkEqiResults',
            ),
            'macro' => array(
                'DbAllMonMacros',
                'DbAllQtrMacros',
                'DbAllAnnMacros',
            ),
            'trade' => array(
                'DbAllMonTtccResults',
                'DbAllMonTtfResults',
                'DbAllMonTtpResults',
                'DbAllMonTtResults',
            ),
        );

        foreach ($types as $type => $modelNames) {
            $minDate = $maxDate = $min = $max = null;

            foreach ($modelNames as $modelName) {
                if (class_exists($modelName)) {
                    $model = new $modelName;

                    foreach ($model->attributes as $attr => $val) {
                        $parts = $model->listAttrParts($attr);
                        $time = $date = '';

                        if (!empty($parts['year']) && is_numeric($parts['year'])) {
                            $date .= $parts['year'];
                            $time .= $parts['year'];
                        }

                        if (!empty($parts['month']) && is_numeric($parts['month'])) {
                            $date .= $parts['month'];
                            $time .= '-' . $parts['month'];
                        }

                        if (!empty($date) && is_numeric($date)) {
                            $time = date('j M Y', strtotime($time . '-01'));

                            if (empty($minDate) || $date < $minDate) {
                                $minDate = $date;
                                $min = $time;
                            }

                            if (empty($maxDate) || $date > $maxDate) {
                                $maxDate = $date;
                                $max = $time;
                            }
                        }
                    }
                }
            }

            $data[$type] = array(
                'min' => $min,
                'max' => $max,
            );
        }

        $log = DbVar::model()->findByAttributes(array('name' => 'chart-date-limit'));
        if (empty($log)) {
            $log = new DbVar();
            $log->name = 'chart-date-limit';
            $log->type = 'system';
        }
        $log->data = $data;

        return $log->save();
    }

    public function reporterAvailability ($iso = null, $type = 'reporter') {
        ini_set('memory_limit', '250M');
        $iso = ($iso == 'commodity' || $iso == 'currency' || $iso == 'equity' ? null : $iso);
        $data = [
            'commodity' => [],
            'currency' => [],
            'equity' => [],
            'account' => [],
            'trade-tt' => 0,
            'trade-ttp' => [],
            'trade-ttcc' => [],
            'trade-ttf-partner' => [],
            'trade-ttf-sector' => [],
            'macro-annual' => [],
            'macro-month' => [],
            'macro-quarter' => [],
        ];
        $valid = false;

        $markets = [
            'commodity' => [new DbAllMrkComResults(), new DbCommodities()],
            'currency' => [new DbAllMrkCurResults(), new DbCurrencies()],
            'equity' => [new DbAllMrkEqiResults(), new DbEquities()],
        ];

        foreach ($markets as $market => $search) {
            if ($type == $market) {
                $criteria = new CDbCriteria();
                $criteria->select = 'Asset_ID';
                $criteria->group = 'Asset_ID';

                if (!empty($iso)) {
                    $criteria->compare('Asset_ID', $iso);
                }

                $models = $search[0]->findAll($criteria);
                if (!empty($models)) {
                    $valid = true;
                    $found = [];
                    foreach ($models as $model) {
                        if (!empty($model->$market)) {
                            $res = DbReports::add([
                                'group' => $market,
                                'type' => 'asset',
                                'code' => $model->Asset_ID,
                                'hasData' => 1,
                                'access' => $model->$market->access,
                            ]);
                            if (!empty($res)) {
                                $data[$market][] = $model->Asset_ID;
                            }
                            $found[] = $model->Asset_ID;
                        }
                    }
                    $criteria = new CDbCriteria();
                    $criteria->addNotInCondition('code', $found);
                    $models = $search[1]->findAll($criteria);
                    if (!empty($models)) {
                        foreach ($models as $model) {
                            DbReports::add([
                                'group' => $market,
                                'type' => 'asset',
                                'code' => $model->code,
                                'hasData' => 0,
                                'access' => $model->access,
                            ]);
                        }
                    }
                }
            }
        }

        if (in_array($type, array('ttp', 'ttcc', 'ttf-partner', 'ttf-sector', 'tt', 'macro-annual', 'macro-quarter', 'macro-month'))) {
            if (!empty($iso)) {
                $reporters = DbReporters::model()->findAllByAttributes(array('ccode3' => $iso));
            } else {
                $reporters = DbReporters::model()->findAll();
            }

            $macros = DbMacroList::model()->findAll();
            $partners = DbPartners::model()->findAll();
            $sectors = DbSectors::model()->findAll();

            if (!empty($reporters)) {
                foreach ($reporters as $reporter) {

                    DbReports::add([
                        'group' => 'account',
                        'type' => 'asset',
                        'code' => $reporter->code,
                        'hasData' => 0,
                        'access' => ($reporter->searchDef == 1 ? 1 : 2),
                    ]);

                    $reports = [
                        'ttp' => [
                            'model' => new DbAllMonTtpResults,
                            'attr' => 'PISO3',
                        ],
                        'ttcc' => [
                            'model' => new DbAllMonTtccResults,
                            'attr' => 'Commodity_Code',
                        ],
                        'ttf-partner' => [
                            'model' => new DbAllMonTtfResults,
                            'attr' => 'PISO3',
                        ],
                        'ttf-sector' => [
                            'model' => new DbAllMonTtfResults,
                            'attr' => 'Commodity_Code',
                        ],
                    ];
                    foreach ($reports as $key => $search) {
                        if ($type == $key) {
                            $valid = true;
                            $found = [];
                            $attr = $search['attr'];
                            $criteria = new CDbCriteria();
                            $criteria->select = 'RISO3, ' . $attr;
                            $criteria->group = $attr;
                            $criteria->compare('RISO3', $reporter->ccode3);
                            $models = $search['model']->findAll($criteria);
                            if (!empty($models)) {
                                foreach ($models as $model) {
                                    $res = DbReports::add([
                                        'group' => 'trade',
                                        'type' => $key,
                                        'code' => $reporter->code,
                                        'partner' => $model->$attr,
                                        'hasData' => 1,
                                        'access' => ($reporter->searchDef == 1 ? 1 : 2),
                                    ]);
                                    $found[] = $model->$attr;
                                    if (!empty($res)) {
                                        $data['trade-' . $key][] = $model->$attr;
                                    }
                                }
                            }

                            $items = $partners;
                            if ($attr == 'Commodity_Code') {
                                $items = $sectors;
                            }
                            if (!empty($items) && in_array('trade', $reporter->type)) {
                                foreach ($items as $item) {
                                    if (!in_array($item->code, $found)) {
                                        $hasData = 0;
                                        if (in_array($key, array('ttp', 'ttf-partner'))) {
                                            $childIds = [];
                                            if (!empty($item->children)) {
                                                foreach ($item->children as $child) {
                                                    $childIds[] = $child->code;
                                                }
                                            }

                                            $criteria = new CDbCriteria();
                                            $criteria->compare('RISO3', $reporter->code);
                                            $criteria->compare('PISO3', $childIds);
                                            $tmp = $search['model']->count($criteria);
                                            if (!empty($tmp)) {
                                                $hasData = 1;
                                            }
                                        }

                                        $res = DbReports::add([
                                            'group' => 'trade',
                                            'type' => $key,
                                            'code' => $reporter->code,
                                            'partner' => $item->code,
                                            'hasData' => $hasData,
                                            'access' => ($reporter->searchDef == 1 ? 1 : 2),
                                        ]);
                                        if (!empty($res) && !empty($hasData)) {
                                            $data['trade-' . $key][] = $item->code;
                                        }
                                    }
                                }
                            }
                        }
                    }


                    if ($type == 'tt') {
                        $valid = true;
                        $model = DbAllMonTtResults::model()->findByAttributes(array('RISO3' => $reporter->ccode3));
                        $hasData = (in_array('trade', $reporter->type) && !empty($model) ? 1 : 0);
                        $res = DbReports::add([
                            'group' => 'trade',
                            'type' => 'tt',
                            'code' => $reporter->code,
                            'hasData' => $hasData,
                            'access' => ($reporter->searchDef == 1 ? 1 : 2),
                        ]);
                        if (!empty($res)) {
                            $data['trade-' . $type] = $hasData;
                        }
                    }


                    $reports = [
                        'annual' => new DbAllAnnMacros(),
                        'month' => new DbAllMonMacros(),
                        'quarter' => new DbAllQtrMacros(),
                    ];
                    foreach ($reports as $key => $search) {
                        if ($type == 'macro-' . $key) {
                            $valid = true;
                            $found = [];
                            $criteria = new CDbCriteria();
                            $criteria->select = 'Asset_ID, RISO3';
                            $criteria->compare('RISO3', $reporter->ccode3);
                            $models = $search->findAll($criteria);
                            if (!empty($models)) {
                                foreach ($models as $model) {
                                    $res = DbReports::add([
                                        'group' => 'macro',
                                        'type' => $key,
                                        'code' => $reporter->code,
                                        'partner' => $model->Asset_ID,
                                        'hasData' => 1,
                                        'access' => ($reporter->searchDef == 1 ? 1 : 2),
                                    ]);
                                    $found[] = $model->Asset_ID;
                                    if (!empty($res)) {
                                        $data['macro-' . $key][] = $model->Asset_ID;
                                    }
                                }
                            }

                            if (!empty($macros)) {
                                foreach ($macros as $macro) {
                                    if (!in_array($macro->assetId, $found)) {
                                        $res = DbReports::add([
                                            'group' => 'macro',
                                            'type' => $key,
                                            'code' => $reporter->code,
                                            'partner' => $macro->assetId,
                                            'hasData' => 0,
                                            'access' => ($reporter->searchDef == 1 ? 1 : 2),
                                        ]);
                                    }
                                }
                            }
                        }
                    }

                }
            }
        }


        $data['valid'] = $valid;

        return $data;
    }

    /*public function reporterAvailability ($iso = null, $type = 'reporter') {
        ini_set('memory_limit', '250M');
        $iso = ($iso == 'commodity' || $iso == 'currency' || $iso == 'equity' ? null : $iso);

        $dataHolder = array(
            'reporter' => array(),
            'commodity' => array(),
            'currency' => array(),
            'equity' => array(),
            'account' => array(
                'free' => array(),
                'g20' => array(),
                'premium' => array(),
            ),
        );

        $log = DbVar::model()->findByAttributes(array(
            'name' => 'validReporters',
            'type' => 'searchProcessing',
        ));
        if (empty($log)) {
            $log = new DbVar();
            $log->name = 'validReporters';
            $log->type = 'searchProcessing';
        } else {
            $dataHolder = Yii::app()->format->options($dataHolder, $log->data);
        }

        if ($type == 'commodity') {
            $dataHolder['commodity'] = array(
                'premium' => array(),
                'free' => array(),
            );

            $criteria = new CDbCriteria();
            $criteria->select = 'Asset_ID';
            $criteria->group = 'Asset_ID';

            if (!empty($iso)) {
                $criteria->compare('Asset_ID', $iso);
            }

            $models = DbAllMrkComResults::model()->findAll($criteria);
            if (!empty($models)) {
                $found = [];
                foreach ($models as $model) {
                    if (!empty($model->commodity)) {
                        if ($model->commodity->access > 1) {
                            $dataHolder['commodity']['premium'][] = $model->Asset_ID;
                        } else {
                            $dataHolder['commodity']['free'][] = $model->Asset_ID;
                        }
                        $found[] = $model->Asset_ID;
                    }
                }
                $criteria = new CDbCriteria();
                $criteria->addNotInCondition('code', $found);
                $models = DbCommodities::model()->findAll($criteria);
                if(!empty($models)){
                    foreach($models as $model){
                        $dataHolder['commodity']['no-data'][] = $model->code;
                    }
                }
            }
        }

        if ($type == 'currency') {
            $dataHolder['currency'] = array(
                'premium' => array(),
                'free' => array(),
            );

            $criteria = new CDbCriteria();
            $criteria->select = 'Asset_ID';
            $criteria->group = 'Asset_ID';

            if (!empty($iso)) {
                $criteria->compare('Asset_ID', $iso);
            }

            $models = DbAllMrkCurResults::model()->findAll($criteria);
            if (!empty($models)) {
                $found = [];
                foreach ($models as $model) {
                    if (!empty($model->currency)) {
                        if ($model->currency->access > 1) {
                            $dataHolder['currency']['premium'][] = $model->Asset_ID;
                        } else {
                            $dataHolder['currency']['free'][] = $model->Asset_ID;
                        }
                        $found[] = $model->Asset_ID;
                    }
                }
                $criteria = new CDbCriteria();
                $criteria->addNotInCondition('code', $found);
                $models = DbCurrencies::model()->findAll($criteria);
                if(!empty($models)){
                    foreach($models as $model){
                        $dataHolder['currency']['no-data'][] = $model->code;
                    }
                }
            }
        }

        if ($type == 'equity') {
            $dataHolder['equity'] = array(
                'premium' => array(),
                'free' => array(),
            );

            $criteria = new CDbCriteria();
            $criteria->select = 'Asset_ID';
            $criteria->group = 'Asset_ID';

            if (!empty($iso)) {
                $criteria->compare('Asset_ID', $iso);
            }

            $models = DbAllMrkEqiResults::model()->findAll($criteria);
            if (!empty($models)) {
                $found = [];
                foreach ($models as $model) {
                    if (!empty($model->equity)) {
                        if ($model->equity->access > 1) {
                            $dataHolder['equity']['premium'][] = $model->Asset_ID;
                        } else {
                            $dataHolder['equity']['free'][] = $model->Asset_ID;
                        }
                        $found[] = $model->Asset_ID;
                    }
                }
                $criteria = new CDbCriteria();
                $criteria->addNotInCondition('code', $found);
                $models = DbEquities::model()->findAll($criteria);
                if(!empty($models)){
                    foreach($models as $model){
                        $dataHolder['equity']['no-data'][] = $model->code;
                    }
                }
            }
        }

        if ($type == 'reporter') {
            if (!empty($iso)) {
                $reporters = DbReporters::model()->findAllByAttributes(array('ccode3' => $iso));
            } else {
                $reporters = DbReporters::model()->findAll();
            }

            if (!empty($reporters)) {
                foreach ($reporters as $reporter) {
                    $data = array(
                        'trade' => array(
                            'tt' => false,
                            'ttcc' => array(),
                            'ttp' => array(),
                            'ttf-partner' => array(),
                            'ttf-sector' => array(),
                        ),
                        'macro' => array(
                            'annual' => array(),
                            'month' => array(),
                            'quarter' => array(),
                        ),
                    );

                    $dataHolder['account']['premium'][] = $reporter->code;
                    if ($reporter->searchDef == 1) {
                        $dataHolder['account']['free'][] = $reporter->code;
                    }
                    if ($reporter->g20 == 1) {
                        $dataHolder['account']['g20'][] = $reporter->code;
                    }

                    if (!empty($reporter->type)) {
                        if (in_array('trade', $reporter->type)) {
                            $criteria = new CDbCriteria();
                            $criteria->select = 'RISO3, PISO3';
                            $criteria->compare('RISO3', $reporter->ccode3);
                            $models = DbAllMonTtpResults::model()->findAll($criteria);
                            if (!empty($models)) {
                                foreach ($models as $model) {
                                    $data['trade']['ttp'][] = $model->PISO3;
                                }
                            }

                            $criteria = new CDbCriteria();
                            $criteria->select = 'RISO3, Commodity_Code';
                            $criteria->compare('RISO3', $reporter->ccode3);
                            $models = DbAllMonTtccResults::model()->findAll($criteria);
                            if (!empty($models)) {
                                foreach ($models as $model) {
                                    $data['trade']['ttcc'][] = $model->Commodity_Code;
                                }
                            }

                            $criteria = new CDbCriteria();
                            $criteria->select = 'RISO3, PISO3';
                            $criteria->compare('RISO3', $reporter->ccode3);
                            $criteria->group = 'PISO3';
                            $models = DbAllMonTtfResults::model()->findAll($criteria);
                            if (!empty($models)) {
                                foreach ($models as $model) {
                                    $data['trade']['ttf-partner'][] = $model->PISO3;
                                }
                            }

                            $criteria = new CDbCriteria();
                            $criteria->select = 'RISO3, Commodity_Code';
                            $criteria->compare('RISO3', $reporter->ccode3);
                            $criteria->group = 'Commodity_Code';
                            $models = DbAllMonTtfResults::model()->findAll($criteria);
                            if (!empty($models)) {
                                foreach ($models as $model) {
                                    $data['trade']['ttf-sector'][] = $model->Commodity_Code;
                                }
                            }

                            $criteria = new CDbCriteria();
                            $criteria->compare('RISO3', $reporter->ccode3);
                            $model = DbAllMonTtResults::model()->find($criteria);
                            if (!empty($model)) {
                                $data['trade']['tt'] = true;
                            }
                        }

                        if (in_array('macro', $reporter->type)) {
                            $criteria = new CDbCriteria();
                            $criteria->select = 'Asset_ID, RISO3';
                            $criteria->compare('RISO3', $reporter->ccode3);

                            $models = DbAllAnnMacros::model()->findAll($criteria);
                            if (!empty($models)) {
                                foreach ($models as $model) {
                                    $data['macro']['annual'][] = $model->Asset_ID;
                                }
                            }

                            $models = DbAllMonMacros::model()->findAll($criteria);
                            if (!empty($models)) {
                                foreach ($models as $model) {
                                    $data['macro']['month'][] = $model->Asset_ID;
                                }
                            }

                            $models = DbAllQtrMacros::model()->findAll($criteria);
                            if (!empty($models)) {
                                foreach ($models as $model) {
                                    $data['macro']['quarter'][] = $model->Asset_ID;
                                }
                            }
                        }
                    }

                    $dataHolder['reporter'][$reporter->ccode3] = $data;
                }
            }
        }

        $log->data = $dataHolder;
        $valid = $log->save();

        if (!empty($iso)) {
            if (!empty($data)) {
                $data['valid'] = $valid;

                return $data;
            }
        } else {
            return $valid;
        }
    }*/

    public function syncAssetNames () {
        $return = array(
            'existing' => 0,
            'added' => 0,
            'removed' => 0,
        );
        $tables = array(
            'commodity' => array(
                'assetModel' => new DbCommodities(),
                'resultsModel' => new DbAllMrkComResults(),
                'group' => 'Asset_ID',
            ),
            'currency' => array(
                'assetModel' => new DbCurrencies(),
                'resultsModel' => new DbAllMrkCurResults(),
                'group' => 'Asset_ID',
            ),
            'equities' => array(
                'assetModel' => new DbEquities(),
                'resultsModel' => new DbAllMrkEqiResults(),
                'group' => 'Asset_ID',
            ),
        );

        foreach ($tables as $table) {

            $valid = $existing = array();
            $criteria = new CDbCriteria();
            $criteria->group = $table['group'];

            //get list of asset names from the results table
            $resultsItems = $table['resultsModel']->findAll($criteria);
            if (!empty($resultsItems)) {

                //format the existing assets to be more searchable
                $existingItems = $table['assetModel']->findAll();
                if (!empty($existingItems)) {
                    foreach ($existingItems as $existingItem) {
                        $existing[$existingItem->code] = $existingItem;
                    }
                }

                //compare the results and asset name tables, add to the asset name table if asset is missing
                foreach ($resultsItems as $resultsItem) {
                    if (!empty($existing[$resultsItem->Asset_ID])) {
                        $valid[] = $resultsItem->Asset_ID;
                        $return['existing']++;
                    } else {
                        $model = clone $table['assetModel'];
                        $model->code = $resultsItem->Asset_ID;
                        $model->name = $resultsItem->Asset_Name;
                        if ($model->save()) {
                            $valid[] = $resultsItem->Asset_ID;
                            $return['added']++;
                        }
                    }
                }

                //delete item from the asset name table if its not in the results table
                if (!empty($existing)) {
                    foreach ($existing as $key => $existingItem) {
                        if (!in_array($key, $valid)) {
                            //$existingItem->delete();
                            $return['removed']++;
                        }
                    }
                }
            }
        }

        return $return;
    }


    public function migratePermissions () {
        ini_set('memory_limit', '10000M');
        ini_set('max_execution_time', 2000);

        $labels = [
            'date-full' => 'Date: Access to full date range',
            'user' => 'User type, standard basic user',
            'admin' => 'User type, has access to admin panel',
            'dev' => 'User type, has access to admin panel and site maintenance tools',
            'service-ess' => 'Service Level: Essential',
            'service-pro' => 'Service Level: Pro',
            'service-ent' => 'Service Level: Enterprise',
            'tool-tra' => 'Tool: Trade and Economics',
            'tool-com' => 'Tool: Commodities',
            'tool-cur' => 'Tool: Currencies',
            'tool-equ' => 'Tool: Equities',
            'sub-tra-ess' => 'Subscription: Trade Essential',
            'sub-tra-pro' => 'Subscription: Trade Pro',
            'sub-tra-ent' => 'Subscription: Trade Enterprise',
            'sub-com-ess' => 'Subscription: Commodities Essential',
            'sub-com-pro' => 'Subscription: Commodities Pro',
            'sub-com-ent' => 'Subscription: Commodities Enterprise',
            'sub-cur-ess' => 'Subscription: Currencies Essential',
            'sub-cur-pro' => 'Subscription: Currencies Pro',
            'sub-cur-ent' => 'Subscription: Currencies Enterprise',
            'sub-equ-ess' => 'Subscription: Equities Essential',
            'sub-equ-pro' => 'Subscription: Equities Pro',
            'sub-equ-ent' => 'Subscription: Equities Enterprise',
        ];

        $data = [
            CAuthItem::TYPE_TASK => [
                'date-full' => [],

                'service-ess' => [],
                'service-pro' => ['service-ess', 'date-full', 'country-full', 'download-csv', 'favorites', 'partner-multi'],
                'service-ent' => ['service-pro'],

                'tool-tra' => [],
                'tool-com' => [],
                'tool-cur' => [],
                'tool-equ' => [],
            ],
            CAuthItem::TYPE_ROLE => [
                'user' => [],
                'admin' => ['login-multi', 'menu-admin', 'user-manage', 'user'],
                'dev' => ['admin', 'access-manage'],

                'sub-tra-ess' => ['tool-tra', 'service-ess'],
                'sub-tra-pro' => ['tool-tra', 'service-pro'],
                'sub-tra-ent' => ['tool-tra', 'service-ent'],

                'sub-com-ess' => ['tool-com', 'service-ess'],
                'sub-com-pro' => ['tool-com', 'service-pro'],
                'sub-com-ent' => ['tool-com', 'service-ent'],

                'sub-cur-ess' => ['tool-cur', 'service-ess'],
                'sub-cur-pro' => ['tool-cur', 'service-pro'],
                'sub-cur-ent' => ['tool-cur', 'service-ent'],

                'sub-equ-ess' => ['tool-equ', 'service-ess'],
                'sub-equ-pro' => ['tool-equ', 'service-pro'],
                'sub-equ-ent' => ['tool-equ', 'service-ent'],
            ],
        ];

        foreach ($data as $type => $groups) {
            foreach ($groups as $key => $assigns) {
                $authItem = Yii::app()->authManager->getAuthItem($key);
                if (!empty($authItem)) {
                    Yii::app()->authManager->removeAuthItem($key);
                }
                $authItem = Yii::app()->authManager->createAuthItem($key, $type, $labels[$key]);
                if (!empty($assigns)) {
                    foreach ($assigns as $assign) {
                        $authItem->addChild($assign);
                    }
                }
            }
        }

        $users = DbUser::model()->findAll();
        foreach ($users as $user) {
            $admin = false;
            if (Yii::app()->authManager->isAssigned('trial', $user->id)) {

                /*if (Yii::app()->authManager->isAssigned('economic', $user->id)) {
                    DbUserService::add($user->id, 'tra', 'ess');
                }*/
                /*if (Yii::app()->authManager->isAssigned('market', $user->id)) {
                    DbUserService::add($user->id, 'com', 'ess');
                    DbUserService::add($user->id, 'cur', 'ess');
                    DbUserService::add($user->id, 'equ', 'ess');
                }*/
                DbUserService::add($user->id, 'tra', 'ess');
                //Yii::app()->authManager->revoke('economic', $user->id);
                //Yii::app()->authManager->revoke('market', $user->id);
                Yii::app()->authManager->revoke('trial', $user->id);
            }

            if (Yii::app()->authManager->isAssigned('g20', $user->id)) {
                /*if (Yii::app()->authManager->isAssigned('economic', $user->id)) {
                    DbUserService::add($user->id, 'tra', 'pro');
                }*/
                /*if (Yii::app()->authManager->isAssigned('market', $user->id)) {
                    DbUserService::add($user->id, 'com', 'pro');
                    DbUserService::add($user->id, 'cur', 'pro');
                    DbUserService::add($user->id, 'equ', 'pro');
                }*/
                DbUserService::add($user->id, 'tra', 'pro');
                //Yii::app()->authManager->revoke('economic', $user->id);
                //Yii::app()->authManager->revoke('market', $user->id);
                Yii::app()->authManager->revoke('g20', $user->id);
            }

            if (Yii::app()->authManager->isAssigned('premium', $user->id)) {
                /*if (Yii::app()->authManager->isAssigned('economic', $user->id)) {
                    DbUserService::add($user->id, 'tra', 'pro');
                }*/
                /*if (Yii::app()->authManager->isAssigned('market', $user->id)) {
                    DbUserService::add($user->id, 'com', 'pro');
                    DbUserService::add($user->id, 'cur', 'pro');
                    DbUserService::add($user->id, 'equ', 'pro');
                }*/
                DbUserService::add($user->id, 'tra', 'ess');
                //Yii::app()->authManager->revoke('economic', $user->id);
                //Yii::app()->authManager->revoke('market', $user->id);
                Yii::app()->authManager->revoke('premium', $user->id);
            }

            if ($user->type == 'admin' || $user->type == 'dev') {
                DbUserService::add($user->id, 'tra', 'ent');
                DbUserService::add($user->id, 'com', 'ent');
                DbUserService::add($user->id, 'cur', 'ent');
                DbUserService::add($user->id, 'equ', 'ent');
            } else {
                $user->type = 'user';
            }

            $user->save();
            //Yii::app()->authManager->revoke('economic', $user->id);
            //Yii::app()->authManager->revoke('market', $user->id);
            Yii::app()->authManager->revoke('premium', $user->id);
            Yii::app()->authManager->revoke('g20', $user->id);
            Yii::app()->authManager->revoke('trial', $user->id);
        }

        //Yii::app()->authManager->removeAuthItem('economic');
        Yii::app()->authManager->removeAuthItem('g20');
        //Yii::app()->authManager->removeAuthItem('market');
        Yii::app()->authManager->removeAuthItem('premium');
        Yii::app()->authManager->removeAuthItem('trial');
        Yii::app()->authManager->removeAuthItem('date-1-year');
    }

    public function migratePermissions2 () {
        $users = DbUser::model()->findAll();
        foreach ($users as $user) {
            $user->setDefaultFavorites(true);
            $user->save();
        }
    }

    /*public function test () {
        echo '<pre>';
        $test = "0123";
        var_dump($test < 1000, strlen($test));
        $test = 0123;
        var_dump($test < 1000, strlen($test));
        $test = "123";
        var_dump($test < 1000, strlen($test));

        $models = DbSectors::model()->findAll('code < 1000');
        if(!empty($models)){
            foreach ($models as $model){
                var_dump('NEXT------------------------');
                var_dump('old', $model->code);
                if($model->code < 1000 && strlen($model->code) < 4){
                    $model->code = "0".$model->code;
                    if(strlen($model->code) == 4) {
                        $valid = $model->update();
                        var_dump($valid);
                        var_dump($model->errors);
                        var_dump('new', $model->code);
                        //break;
                    }
                }
            }
        }
        echo '</pre>';
    }*/
}

