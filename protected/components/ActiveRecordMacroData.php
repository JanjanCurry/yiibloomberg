<?php class ActiveRecordMacroData extends ActiveRecord {

    public static function model ($className = __CLASS__) {
        return parent::model($className);
    }

    public $_listMacro;

    public function listMacro () {
        if (empty($this->_listMacro)) {
            $list = array();
            $criteria = new CDbCriteria();
            $criteria->order = 't.category ASC, t.orderId ASC, t.assetName ASC';
            $macros = DbMacroList::model()->findAll($criteria);
            if (!empty($macros)) {
                foreach ($macros as $macro) {
                    if (empty($list[$macro->category])) {
                        $list[$macro->category] = array();
                    }
                    $list[$macro->category][$macro->assetId] = $macro->assetName;
                }
            }
            $this->_listMacro = $list;
        }

        return $this->_listMacro;
    }

    public function listPeriod () {
        return array(
            'annual' => 'Annual',
            'quarter' => 'Quarterly',
        );
    }

    public function listType () {
        return array(
            'Percent' => 'Percentage',
            'Absolute' => 'USD',
        );
    }

    public function listVariant () {
        return array(
            'Absolute' => 'US Dollars',
            'Index' => 'Index points',
            'Percent' => '% Change',
        );
    }

}