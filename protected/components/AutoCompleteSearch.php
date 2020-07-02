<?php

class AutoCompleteSearch extends CAction {

    private $terms = array();
    private $restrict = array();
    private $limit = 500;

    public function run () {
        $results = array();

        if (!empty($_GET['term'])) {
            $terms = explode(' ', strtolower(trim($_GET['term'])));
            if (!empty($terms)) {
                foreach($terms as $key => $val){
                    $terms[$key] = preg_replace('/[^A-Za-z0-9]/', '', $val);
                }
                $this->terms = $terms;
            }
        }

        if (!empty($_GET['term']) && $_GET['term'] == 'default') {
            $results = $this->searchDefault();
        } else {
            $results = $this->filterResults();
        }

        if(!empty($results)){
            ksort($results);
        }

        $return = DbReporters::model()->attributes;
        $return['value'] = "Advanced Search";
        $results['ZZZ'] = $return;

        header('Content-Type: application/json');
        echo CJSON::encode($results);
    }

    private function filterResults () {
        $results = array();

        $terms = $this->terms;
        //$this->terms = array();


        //$results = array_merge($results, $this->searchTrade());
        //$results = array_merge($results, $this->searchMacro());
        //$results = array_merge($results, $this->searchMacroReporter());
        $results = array_merge($results, $this->searchCommodity());
        $results = array_merge($results, $this->searchCurrency());
        $results = array_merge($results, $this->searchEquity());

        /*if(Yii::app()->user->checkAccess('tool-tra')) {
            $results = array_merge($results, $this->searchTrade());
            //$results = array_merge($results, $this->searchMacro());
            $results = array_merge($results, $this->searchMacroReporter());
        }
        if(Yii::app()->user->checkAccess('tool-com')) {
            $results = array_merge($results, $this->searchCommodity());
        }
        if(Yii::app()->user->checkAccess('tool-cur')) {
            $results = array_merge($results, $this->searchCurrency());
        }
        if(Yii::app()->user->checkAccess('tool-equ')) {
            $results = array_merge($results, $this->searchEquity());
        }*/

        if(!empty($results)){
            $temp = array();
            $i=0;
            foreach($results as $key => $data){
                $valid = true;
                /*if(!empty($terms)) {
                    $data['search'] = strtolower($data['search']);
                    foreach($terms as $term) {
                        if (strpos($data['search'], $term) === false) {
                            $valid = false;
                        }
                    }
                }*/
                if($valid){
                    $temp[$key] = $data;
                    $i++;
                    if($i >= $this->limit){
                        break;
                    }
                }
            }
            $results = $temp;
        }

        return $results;
    }

    public function getCommodities(){
        $companyId = [0];
        $user = Yii::app()->controller->user;
        if(!empty($user->companyId)){
            $companyId[] = $user->companyId;
        }

        $criteria = new CDbCriteria();
        $criteria->order = 'name ASC, code ASC';
        $criteria->compare('companyId', $companyId);
        if (!empty($this->terms)) {
            foreach ($this->terms as $term) {
                $criteria2 = new CDbCriteria();
                $criteria2->compare('name', $term, true, 'or');
                $criteria2->compare('code', $term, true, 'or');
                $criteria2->compare('figi', $term, true, 'or');
                $criteria2->compare('aka', $term, true, 'or');
                $criteria->mergeWith($criteria2);
            }
        }

        return DbCommodities::model()->findAll($criteria);
    }

    public function getCurrencies(){
        $companyId = [0];
        $user = Yii::app()->controller->user;
        if(!empty($user->companyId)){
            $companyId[] = $user->companyId;
        }

        $criteria = new CDbCriteria();
        $criteria->order = 'name ASC';
        $criteria->compare('companyId', $companyId);
        if (!empty($this->terms)) {
            foreach ($this->terms as $term) {
                $criteria2 = new CDbCriteria();
                $criteria2->compare('name', $term, true, 'or');
                $criteria2->compare('code', $term, true, 'or');
                $criteria2->compare('figi', $term, true, 'or');
                $criteria2->compare('aka', $term, true, 'or');
                $criteria->mergeWith($criteria2);
            }
        }
        return DbCurrencies::model()->findAll($criteria);
    }

    public function getEquities(){
        $companyId = [0];
        $user = Yii::app()->controller->user;
        if(!empty($user->companyId)){
            $companyId[] = $user->companyId;
        }

        $criteria = new CDbCriteria();
        $criteria->order = 'name ASC';
        $criteria->compare('companyId', $companyId);
        if (!empty($this->terms)) {
            foreach ($this->terms as $term) {
                $criteria2 = new CDbCriteria();
                $criteria2->compare('name', $term, true, 'or');
                $criteria2->compare('code', $term, true, 'or');
                $criteria2->compare('figi', $term, true, 'or');
                $criteria2->compare('aka', $term, true, 'or');
                $criteria->mergeWith($criteria2);
            }
        }
        return DbEquities::model()->findAll($criteria);
    }

    public function getReporters($type){
        $criteria = new CDbCriteria();
        $criteria->order = 'country ASC, ccode3 ASC';
        $criteria->compare('type', ','.$type.',', true);
        if (!empty($this->terms)) {
            foreach ($this->terms as $term) {
                $criteria2 = new CDbCriteria();
                $criteria2->compare('country', $term, true, 'or');
                $criteria2->compare('ccode3', $term, true, 'or');
                $criteria2->compare('aka', $term, true, 'or');
                $criteria->mergeWith($criteria2);
            }
        }
        return DbReporters::model()->findAll($criteria);
    }

    public function getMacros(){
        $criteria = new CDbCriteria();
        $criteria->order = 'assetName ASC';
        if (!empty($this->terms)) {
            foreach ($this->terms as $term) {
                $criteria->compare('assetName', $term, true);
            }
        }
        return DbMacroList::model()->findAll($criteria);
    }

    private function searchDefault () {
        $results = array();

        //$models = DbReporters::model()->findAllByAttributes(array('searchDef' => 1));
        if (!empty($models)) {
            foreach ($models as $model) {
                $results[$model->ccode3] = array(
                    'value' => $model->country,
                    'type' => 'trade',
                    'typeDisplay' => 'Trade',
                    'reporter' => $model->ccode3,
                    'premium' => false,
                    'premiumDisplay' => '',
                );
            }
        }

        return $results;
    }

    private function searchMacro () {
        $results = array();

        $macros = $this->getMacros();
        if (!empty($macros)) {
            foreach ($macros as $macro) {
                $results[$macro->assetName] = array(
                    'value' => $macro->assetName,
                    'type' => 'macro',
                    'typeDisplay' => 'Macroeconomics',
                    'macro' => $macro->assetId,
                    'premium' => false,
                    'premiumDisplay' => '',
                    'search' => implode(' ', array(
                        $macro->assetName
                    )),
                );
            }
        }

        return $results;
    }

    private function searchCommodity () {
        $results = array();

        $commodities = $this->getCommodities();
        if (!empty($commodities)) {
            foreach ($commodities as $commodity) {
                $results['4-'.$commodity->name] = array(
                    'value' => $commodity->name,
                    'type' => 'commodity',
                    'typeDisplay' => 'Commodities',
                    'commodity' => $commodity->code,
                    'search' => implode(' ', array(
                        $commodity->code,
                        $commodity->name,
                    )),
                );
            }
        }

        return $results;
    }

    private function searchCurrency () {
        $results = array();

        $currencies = $this->getCurrencies();
        if (!empty($currencies)) {
            foreach ($currencies as $currency) {
                $results['5-'.$currency->name] = array(
                    'value' => $currency->name,
                    'type' => 'currency',
                    'typeDisplay' => 'Currencies',
                    'currency' => $currency->code,
                    'search' => implode(' ', array(
                        $currency->code,
                        $currency->name,
                    )),
                );
            }
        }

        return $results;
    }

    private function searchEquity () {
        $results = array();

        $equities = $this->getEquities();
        if (!empty($equities)) {
            foreach ($equities as $equity) {
                $results['4-'.$equity->name] = array(
                    'value' => $equity->name,
                    'type' => 'equity',
                    'typeDisplay' => 'Equities',
                    'equity' => $equity->code,
                    'search' => implode(' ', array(
                        $equity->code,
                        $equity->name,
                    )),
                );
            }
        }

        return $results;
    }

    private function searchMacroReporter () {
        $results = array();

        $reporters = $this->getReporters('macro');
        $macros = $this->getMacros();

        if (!empty($reporters)) {
            $isPremium = Yii::app()->user->checkToolAccess('tra', 'country-full');

            $hasData = [];
            $criteria = new CDbCriteria();
            $criteria->select = 'code, partner';
            //$criteria->group = 'code, partner';
            $criteria->compare('t.group', 'macro');
            $criteria->compare('t.hasData', 1);
            $models = DbReports::model()->findAll($criteria);
            if(!empty($models)){
                foreach($models as $model){
                    $hasData[] = $model->code.$model->partner;
                }
            }

            foreach ($reporters as $reporter) {
                foreach ($macros as $macro) {
                    $valid = true;

                    if(!in_array($reporter->code.$macro->assetId, $hasData)){
                        $valid = false;
                    }
                    if ($valid) {
                        $premium = true;
                        $premiumDisplay = '';
                        if ($isPremium || $reporter->searchDef == 1) {
                            $premium = false;
                        } else {
                            $premiumDisplay = '(Pro)';
                        }

                        $results['3-' . $reporter->country . $macro->assetName] = array(
                            'value' => $reporter->country . ': ' . $macro->assetName,
                            'type' => 'macroReporter',
                            'typeDisplay' => 'Macroeconomics',
                            'macro' => $macro->assetId,
                            'reporter' => $reporter->ccode3,
                            'premium' => $premium,
                            'premiumDisplay' => $premiumDisplay,
                            'search' => implode(' ', array(
                                $macro->assetName,
                                $reporter->ccode3,
                                $reporter->country,
                                $reporter->aka,
                            )),
                        );
                    }
                }
            }
        }

        return $results;
    }

    private function searchTrade () {
        $results = array();

        $reporters = $this->getReporters('trade');
        if (!empty($reporters)) {
            foreach ($reporters as $reporter) {
                $valid = $premium = true;
                $premiumDisplay = '';
                if ($reporter->access == 0 || Yii::app()->user->checkToolAccess('tra', 'service-pro')  || (Yii::app()->user->checkToolAccess('tra', 'service-ess') && $reporter->access == 1)) {
                    $premium = false;
                } else {
                    if($reporter->access == 1){
                        $premiumDisplay = '(Ess)';
                    }else if($reporter->access ==2){
                        $premiumDisplay = '(Pro)';
                    }
                }

                if($valid) {
                    $results['1-' . $reporter->country] = array(
                        'value' => $reporter->country,
                        'type' => 'trade',
                        'typeDisplay' => 'Trade',
                        'reporter' => $reporter->ccode3,
                        'premium' => $premium,
                        'premiumDisplay' => $premiumDisplay,
                        'search' => implode(' ', array(
                            $reporter->ccode3,
                            $reporter->country,
                            $reporter->aka,
                        )),
                    );

                    foreach (ActiveRecordTradeData::model()->listIndicator() as $indicators) {
                        foreach ($indicators as $key => $label) {
                            $results['2-' . $reporter->country . $label] = array(
                                'value' => $reporter->country . ': ' . $label,
                                'type' => 'tradeIndicator',
                                'typeDisplay' => 'Trade',
                                'indicator' => $key,
                                'reporter' => $reporter->ccode3,
                                'premium' => $premium,
                                'premiumDisplay' => $premiumDisplay,
                                'search' => implode(' ', array(
                                    $reporter->ccode3,
                                    $reporter->country,
                                    $reporter->aka,
                                    $label,
                                )),
                            );
                        }
                    }
                }
            }
        }

        return $results;
    }

}