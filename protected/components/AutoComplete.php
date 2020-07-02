<?php

class AutoComplete extends CAction {

    public $model;
    public $strictOptions;
    public $likeOptions;
    public $notOptions;
    public $notLikeOptions;
    public $filters;
    public $order;

    public $search;
    public $display;
    public $return;

    public $terms;

    private $results = array();
    private $listLimit = 10;

    public function run () {
        if (isset($this->model) && isset($this->search)) {
            $criteria = new CDbCriteria();
            $criteria->limit = $this->listLimit;
            $criteria->order = $this->order;

            $this->model = new $this->model;

            //RESTRICT COUNTRIES FOR FREE ACCOUNTS
            $restrict = array();
            if(get_class($this->model) == 'DbReporters'){
                if(!Yii::app()->user->checkToolAccess('tra', 'country-full')){
                    $filter = array('FAIL');
                    $criteria2 = new CDbCriteria();
                    $criteria2->select = 'code';
                    $criteria2->group = 't.code';
                    $criteria2->compare('t.group', 'account');
                    $criteria2->compare('t.access', '<2');
                    $models = DbReports::model()->findAll($criteria2);
                    if(!empty($models)){
                        foreach($models as $model){
                            $filter[] = $model->code;
                        }
                    }
                    $criteria->compare('t.ccode3', $filter);
                }

                //filter out reporters with no data
                if(!empty($_REQUEST['period']) && !empty($_REQUEST['macro'])){
                    $filter = array('FAIL');
                    $criteria2 = new CDbCriteria();
                    $criteria2->select = 'code, hasData';
                    $criteria2->compare('t.group', 'macro');
                    $criteria2->compare('t.type', $_REQUEST['period']);
                    $criteria2->compare('t.partner', $_REQUEST['macro']);
                    $models = DbReports::model()->findAll($criteria2);
                    if(!empty($models)){
                        foreach($models as $model){
                            if(!empty($model->hasData)) {
                                $filter[] = $model->code;
                            }
                        }
                    }
                    $criteria->compare('t.ccode3', $filter);
                }
            }

            //filter out partners with no data
            if(get_class($this->model) == 'DbPartners' && !empty($_REQUEST['reporter'])){
                $filter = array('FAIL');

                $type = 'ttp';
                if(!empty($_REQUEST['filterType']) && $_REQUEST['filterType'] == 'partner-flow'){
                    $type = 'ttf-partner';
                }

                $filter = array();
                $criteria2 = new CDbCriteria();
                $criteria2->select = 'partner, hasData';
                $criteria2->compare('t.group', 'trade');
                $criteria2->compare('t.type', $type);
                $criteria2->compare('t.code', $_REQUEST['reporter']);
                $models = DbReports::model()->findAll($criteria2);
                if(!empty($models)){
                    foreach($models as $model){
                        if(!empty($model->hasData)) {
                            $filter[] = $model->partner;
                        }
                    }
                }
                $criteria->compare('t.ccode3', $filter);
            }

            //filter out sectors with no data
            if(get_class($this->model) == 'DbSectors' && !empty($_REQUEST['reporter'])){
                $filter = array('FAIL');

                $type = 'ttcc';
                if(!empty($_REQUEST['filterType']) && $_REQUEST['filterType'] == 'sector-flow'){
                    $type = 'ttf-sector';
                }

                $filter = array();
                $criteria2 = new CDbCriteria();
                $criteria2->select = 'partner, hasData';
                $criteria2->compare('t.group', 'trade');
                $criteria2->compare('t.type', $type);
                $criteria2->compare('t.code', $_REQUEST['reporter']);
                $models = DbReports::model()->findAll($criteria2);
                if(!empty($models)){
                    foreach($models as $model){
                        if(!empty($model->hasData)) {
                            $filter[] = $model->partner;
                        }
                    }
                }
                $criteria->compare('t.code', $filter);
            }

            if(get_class($this->model) == 'DbCommodities') {
                if (!Yii::app()->user->checkToolAccess('com', 'service-pro')) {
                    if(!Yii::app()->user->checkToolAccess('com', 'service-ess')){
                        $criteria->compare('t.access', '0');
                    }else{
                        $criteria->compare('t.access', '1');
                    }
                }
            }

            if(get_class($this->model) == 'DbCurrencies') {
                if (!Yii::app()->user->checkToolAccess('cur', 'service-pro')) {
                    if(!Yii::app()->user->checkToolAccess('cur', 'service-ess')){
                        $criteria->compare('t.access', '0');
                    }else{
                        $criteria->compare('t.access', '1');
                    }
                }
            }

            if(get_class($this->model) == 'DbEquities') {
                if (!Yii::app()->user->checkToolAccess('equ', 'service-pro')) {
                    if(!Yii::app()->user->checkToolAccess('equ', 'service-ess')){
                        $criteria->compare('t.access', '0');
                    }else{
                        $criteria->compare('t.access', '1');
                    }
                }
            }

            if(!empty($this->return)){
                //$criteria->select = $this->return;
            }

            //GET INPUT DATA AND CREATE SQL WHERE STATEMENT
            $this->terms = array('');
            if (!empty($_GET['term'])) {
                $this->terms = explode(' ', $_GET['term']);
            }
            foreach ($this->terms as $term) {
                $criteriaTerm = new CDbCriteria();
                if (!is_array($this->search)) {
                    $this->search = array($this->search);
                }
                foreach ($this->search as $searchGroup) {
                    if (!is_array($searchGroup)) {
                        $searchGroup = array($searchGroup);
                    }
                    foreach ($searchGroup as $searchAttribute) {
                        $criteriaTerm = $this->relatedTables($criteriaTerm, $searchAttribute);
                        if (strpos($searchAttribute, '.')) {
                            $criteriaTerm->compare($searchAttribute, $term, true, 'OR');
                        } else {
                            $criteriaTerm->compare('t.' . $searchAttribute, $term, true, 'OR');
                        }
                    }
                }
                $criteria->mergeWith($criteriaTerm);
            }

            //ADD HARD-CODED == CONDITIONS TO SQL WHERE STATEMENT
            if (!empty($this->strictOptions)) {
                foreach ($this->strictOptions as $attr => $option) {
                    $criteria = $this->relatedTables($criteria, $attr);
                    if ($option == 'model.value') {
                        $attrJSName = str_replace('.', '__', $attr);
                        if (!empty($_GET[$attrJSName])) {
                            $option = $_GET[$attrJSName];
                        } else {
                            $option = null;
                        }
                    }
                    if (strpos($attr, '.')) {
                        $criteria->compare($attr, $option);
                    } else {
                        $criteria->compare('t.' . $attr, $option);
                    }
                }
            }

            //ADD HARD-CODED LIKE CONDITIONS TO SQL WHERE STATEMENT
            if (!empty($this->likeOptions)) {
                foreach ($this->likeOptions as $attr => $option) {
                    $criteria = $this->relatedTables($criteria, $attr);
                    if(!is_array($option)){
                        $option = array($option);
                    }
                    foreach($option as $val) {
                        if (strpos($attr, '.')) {
                            $criteria->addSearchCondition($attr, $val, true, 'AND', 'LIKE');
                        } else {
                            $criteria->addSearchCondition('t.' . $attr, $val, true, 'AND', 'LIKE');
                        }
                    }
                }
            }

            //ADD HARD-CODED LIKE CONDITIONS TO SQL WHERE STATEMENT
            if (!empty($this->notOptions)) {
                foreach ($this->notOptions as $attr => $option) {
                    $criteria = $this->relatedTables($criteria, $attr);
                    if (!is_array($option)) {
                        $option = array($option);
                    }
                    if (strpos($attr, '.')) {
                        $criteria->addNotInCondition($attr, $option);
                    } else {
                        $criteria->addNotInCondition('t.' . $attr, $option);
                    }
                }
            }

            //ADD HARD-CODED LIKE CONDITIONS TO SQL WHERE STATEMENT
            if (!empty($this->notLikeOptions)) {
                foreach ($this->notLikeOptions as $attr => $option) {
                    $criteria = $this->relatedTables($criteria, $attr);
                    if (strpos($attr, '.')) {
                        $criteria->addCondition($attr . ' NOT LIKE "%' . $option . '%"');
                    } else {
                        $criteria->addCondition('t.' . $attr . ' NOT LIKE "%' . $option . '%"');
                    }
                }
            }

            //ADD CUSTOM FILTERS
            if(!empty($this->filters)){
                foreach($this->filters as $filter){
                    if($filter == 'activePartners'){
                        $criteria2 = new CDbCriteria();
                        $criteria2->compare('RISO3',(!empty($_REQUEST['reporter']) ? $_REQUEST['reporter'] : 'Real stupidity beats AI every time'));
                        $criteria2->select = 'PISO3';
                        $partners = DbAllMonTtpResults::model()->findAll($criteria2);
                        if(!empty($partners)){
                            $list = array();
                            foreach($partners as $partner){
                                $list[] = $partner->PISO3;
                            }
                            $criteria->compare('t.ccode3',$list);
                        }
                    }
                }
            }

            //ADD HARD-CODED == CONDITIONS TO SQL WHERE STATEMENT
            //var_dump($criteria);exit;
            $results = $this->model->findAll($criteria);
            foreach ($results as $model) {
                if (!is_array($this->display)) {
                    $this->display = array($this->display);
                }
                $displayAttributes = array();
                $break = false;
                foreach ($this->display as $i => $displayGroup) {
                    if (!is_array($displayGroup)) {
                        $displayGroup = $this->display;
                        $break = true;
                    }
                    foreach ($displayGroup as $displayAttribute) {
                        if (strpos($displayAttribute, 'format:') !== false) {
                            $displayAttributes[$i][] = $this->getFormattedData($model, $displayAttribute);
                        } elseif (strpos($displayAttribute, '.')) {
                            $relation = explode('.', $displayAttribute);
                            if (!empty($model->$relation[0])) {
                                if (is_array($model->$relation[0])) {
                                    $relationModel = $model->$relation[0];
                                    $displayAttributes[$i][] = $relationModel[0]->$relation[1];
                                } else {
                                    $displayAttributes[$i][] = $model->$relation[0]->$relation[1];
                                }
                            }
                        } else {
                            $displayAttributes[$i][] = $model->$displayAttribute;
                        }
                    }
                    if ($break === true) {
                        break;
                    }
                }

                if (is_array($this->return)) {
                    $return = array();
                    foreach ($this->return as $attribute) {
                        // Add relations if needed
                        if (strpos($attribute, 'format:') !== false) {
                            $return[str_replace('format:', '', $attribute)] = $this->getFormattedData($model, $attribute);
                        } elseif (strpos($attribute, '.')) {
                            $related = explode('.', $attribute);
                            $relatedModel = $model->$related[0];
                            if (!empty($relatedModel)) {
                                if (is_array($relatedModel)) {
                                    $return[$related[0]][$related[1]] = $relatedModel[0]->$related[1];
                                } else {
                                    $return[$related[0]][$related[1]] = $relatedModel->$related[1];
                                }
                            } else {
                                $return[$related[0]][$related[1]] = null;
                            }
                        } else {
                            $return[$attribute] = $model->$attribute;
                        }
                    }

                    $return['value'] = '';
                    foreach ($displayAttributes as $attribute) {
                        if (!empty($attribute)) {
                            $attrOutput = implode(' ', $attribute);
                            if (!empty($attrOutput) && $attrOutput != ' ') {
                                if (empty($return['value'])) {
                                    $return['value'] = implode(' ', $attribute);
                                } else {
                                    $return['value'] .= ', ' . implode(' ', $attribute);
                                }
                            }
                        }
                    }



                    if(get_class($this->model) == 'DbReporters') {
                        if($model->access == 1 && !Yii::app()->user->checkToolAccess('tra', 'service-ess')){
                            $return['value'] .= ' (Ess)';
                        }else if($model->access == 2 && !Yii::app()->user->checkToolAccess('tra', 'service-pro')){
                            $return['value'] .= ' (Pro)';
                        }
                    }
                }
                $this->results[] = $return;
            }
        }

        header('Content-Type: application/json');
        echo CJSON::encode($this->results);
    }

    private function relatedTables ($model, $attr) {
        if (strpos($attr, '.')) {
            $relations = explode('.', $attr);
            if (!empty($relations)) {
                $parent = '';
                foreach ($relations as $i => $relation) {
                    if (($i + 1) < count($relations) && $relation != '' && array_key_exists($relation, $this->model->relations())) {
                        $model->with[] = $parent . $relation;
                        $parent .= '.' . $relation;
                    }
                }
            }
        }

        return $model;
    }

    private function getFormattedData ($model, $ref) {
        $return = '';
        $ref = str_replace('format:', '', $ref);

        switch ($ref) {
            case 'dateCreated':
                if (!empty($model->created)) {
                    $return = Yii::app()->format->date($model->created);
                }
                break;

            case 'dateUpdated':
                if (!empty($model->updated)) {
                    $return = Yii::app()->format->date($model->updated);
                }
                break;
        }

        return $return;
    }


}