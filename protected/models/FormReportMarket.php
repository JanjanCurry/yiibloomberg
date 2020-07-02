<?php

class FormReportMarket extends FormReportPdf {

    public $assets;

    private $log;

    public function rules () {
        return array(
            array('period, startTime, endTime, reporter, assets, format', 'safe'),
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
        if (empty($this->_filename)) {
            $ext = '.' . $this->format;
            if ($i > 1) {
                $ext = '-' . $i . $ext;
            }

            $filename = 'Market-Report-' . date('Y-M-d-h-i') . $ext;
            $file = $this->path . $filename;

            if (file_exists($this->path . $filename) && $i < 1000) {
                $i++;
                $filename = $this->getFilename($i);
            }

            $this->_filename = $filename;
        }

        return $this->_filename;
    }

    protected function getPdfViewData () {
        $title = $subtitle = '';

        if(!empty($this->charts)){
            //$title = $this->charts[0]->getTypeLabel();
            //$subtitle = date('F Y', $this->charts[0]->startTime) . ' - ' . date('F Y', $this->charts[0]->endTime);
        }

        return [
            'type' => 'market',
            'title' => $title,
            'subtitle' => $subtitle,
            //'charts' => $this->chartImage
            'charts' => $this->charts
        ];
    }

    public function listMarkets () {
        return [
            'commodity' => 'Commodities',
            'currency' => 'Currencies',
            'equity' => 'Equities',
        ];
    }

    public function listMarketPlural () {
        return ActiveRecordMarketData::model()->listMarketPlural();
    }

    public function setDefaults () {
        parent::setDefaults();
        $chartOptions = Yii::app()->controller->chartOptionDefaults();
        $assets = [];

        $companyId = [0];
        $user = Yii::app()->controller->user;
        if(!empty($user->companyId)){
            $companyId[] = $user->companyId;
        }

        if (Yii::app()->user->checkAccess('tool-com')) {
            $models = DbCommodities::model()->findAllByAttributes(array(
                'access' => 1,
                'companyId' => $companyId,
            ));
            if (!empty($models)) {
                foreach ($models as $model) {
                    $assets['COM-' . $model->code] = 'Commodity: ' . $model->name;
                }
            }
        }

        if (Yii::app()->user->checkAccess('tool-cur')) {
            $models = DbCurrencies::model()->findAllByAttributes(array(
                'access' => 1,
                'companyId' => $companyId,
            ));
            if (!empty($models)) {
                foreach ($models as $model) {
                    $assets['CUR-' . $model->code] = 'Currency: ' . $model->name;
                }
            }
        }

        if (Yii::app()->user->checkAccess('tool-equ')) {
            $models = DbEquities::model()->findAllByAttributes(array(
                'access' => 1,
                'companyId' => $companyId,
            ));
            if (!empty($models)) {
                foreach ($models as $model) {
                    $assets['EQU-' . $model->code] = 'Equity: ' . $model->name;
                }
            }
        }

        asort($assets);
        $this->assets = $assets;
    }

}