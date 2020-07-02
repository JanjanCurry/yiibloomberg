<?php

class FormReportCountry extends FormReportPdf {

    public $reporter;

    public function rules () {
        return array(
            array('period, startTime, endTime, reporter, reports, format', 'safe'),
        );
    }

    public function attributeLabels () {
        return array(
            'email' => 'Email',
            'Company' => 'Company Name',
            'fName' => 'First Name',
            'message' => 'Message',
            'sName' => 'Last Name',
        );
    }

    public function getFilename ($i = 1) {
        if(empty($this->_filename)) {
            $ext = '.' . $this->format;
            if ($i > 1) {
                $ext = '-' . $i . $ext;
            }

            $filename = 'Country-Report-' . $this->reporter->ccode3 . '-' . ucfirst($this->period) . '-' . str_replace(' ', '', $this->startTime) . '-' . str_replace(' ', '', $this->endTime) . $ext;
            $file = $this->path . $filename;

            if (file_exists($this->path . $filename) && $i < 1000) {
                $i++;
                $filename = $this->getFilename($i);
            }

            $this->_filename = $filename;
        }

        return $this->_filename;
    }

    protected function getPdfViewData(){
        return [
            'type' => 'trade',
            'title' => $this->reporter->name,
            //'charts' => $this->chartImage
            'charts' => $this->charts
        ];
    }

    public function listReports () {
        if (empty($this->_listReports)) {
            $reports = array();
            foreach (ActiveRecordTradeData::model()->listIndicator() as $groupName => $groups) {
                if (in_array($groupName, array('Top 10', 'Total Trade'))) {
                    foreach ($groups as $key => $val) {
                        $report = array();
                        $report['indicator'] = $key;
                        $report['name'] = $val;
                        if (in_array($key, $this->reports)) {
                            $report['selected'] = true;
                        }
                        $reports['Trade: ' . $groupName][$key] = $report;
                    }
                }
            }

            $macroNames = DbMacroList::model()->findAll(array('order' => 'category, assetName'));
            if (!empty($macroNames)) {
                foreach ($macroNames as $macroName) {
                    $report = array();
                    $report['macro'] = $macroName->assetId;
                    $report['name'] = $macroName->assetName;
                    if (in_array($macroName->assetId, $this->reports)) {
                        $report['selected'] = true;
                    }
                    $reports['Economics: ' . $macroName->category][$macroName->assetId] = $report;
                }
            }

            $this->_listReports = $reports;
        }

        return $this->_listReports;
    }

    public function setDefaults () {
        parent::setDefaults();
        $chartOptions = Yii::app()->controller->chartOptionDefaults();

        $this->reports = array(
            //'trade-none-ev',

            'top10-partner-ev',
            'top10-partner-iv',
            'top10-sector-ev',
            'top10-sector-iv',

            //'GDP_AGR',
            //'INF_IRY',
            //'IP',
        );
    }

}