<?php

use PhpOffice\PhpPresentation\PhpPresentation;
use PhpOffice\PhpPresentation\Shape\Drawing;
use PhpOffice\PhpPresentation\Shape\Media;
use PhpOffice\PhpPresentation\DocumentLayout;
use PhpOffice\PhpPresentation\Style\Alignment;
use PhpOffice\PhpPresentation\Style\Color;

class FormReportPdf extends FormModel {

    public $period;
    public $reports;
    public $format;
    public $charts;

    protected $resend;
    protected $_chartImage;
    protected $_filename;
    protected $_listReports;

    protected function createPdf(){
        $valid = false;
        
        //init pdf
        //Yii::import('webroot.vendor.mpdf.mpdf.mPDF');
        $mpdf = new \mPDF('utf-8', 'A4-L', 0, 'Open-Sans', 0, 0, 0, 0);

        //generate pdf html content
        $controller = Yii::app()->controller;
        $controller->registerLayout('pdf');
        $this->setCharts($this->charts);
        $params = $this->getPdfViewData();
        $html = $controller->render('pdf/'.$params['type'], $params, true);
        $css = file_get_contents(YiiBase::getPathOfAlias('root') . '/css/pdf.css');

        //create pdf
        $mpdf->WriteHTML($css, 1);
        $mpdf->WriteHTML($html);
        //$mpdf->output();exit;
        $mpdf->Output($this->file, 'F');

        return file_exists($this->file);
    }

    protected function createPpt(){
        $valid = false;
        $this->setCharts($this->charts);
        $data = $this->getPdfViewData();

        $ppt = new PhpPresentation();
        $ppt->getLayout()->setDocumentLayout(DocumentLayout::LAYOUT_A4, true);

        $this->pptAddFront($ppt);

        $pageNum = 2;
        if(!empty($this->charts)){
            foreach ($this->charts as $group) {
                if(!empty($group)) {
                    if (is_array($group)) {
                        foreach ($group as $chart) {
                            if (!empty($chart)) {
                                if (is_array($chart)) {
                                    if(!empty($chart['title'])) {
                                        $this->pptAddTitle($ppt, $chart['title'], $pageNum);
                                        $pageNum++;
                                    }
                                } else {
                                    //$this->pptAddTable($ppt, $chart);
                                    $this->pptAddChart($ppt, $chart, $pageNum);
                                    $pageNum++;
                                }
                            }
                        }
                    }
                }
            }
        }

        /*if(!empty($data['title'])) {
            $this->pptAddTitle($ppt, $data['title']);
        }
        if(!empty($this->chartImage)) {
            foreach ($this->chartImage as $group => $groupCharts) {
                if(!empty($groupCharts)) {
                    $this->pptAddTitle($ppt, ucfirst($group));
                    foreach($groupCharts as $chart) {
                        $this->pptAddChart($ppt, $chart);
                    }
                }
            }
        }*/
        $this->pptAddBack($ppt);

        $xmlWriter = \PhpOffice\PhpPresentation\IOFactory::createWriter($ppt, 'PowerPoint2007');
        $xmlWriter->save($this->file);

        return file_exists($this->file);
    }

    public function deleteImages(){
        //delete temporary images
        if(!empty($this->chartImage)) {
            foreach ($this->chartImage as $group) {
                if (!empty($group)) {
                    foreach ($group as $data) {
                        if (!empty($data['image'])) {
                            $data['image']->deleteImage();
                        }
                    }
                }
            }
        }
    }

    public function getChartImage(){
        if(empty($this->_chartImage)) {
            $charts = array();
            if (!empty($this->charts)) {
                foreach ($this->charts as $group => $chartsDatas) {
                    if (!empty($chartsDatas)) {
                        $charts[$group] = array();
                        foreach ($chartsDatas as $key => $chartData) {
                            if (empty($chartData['img']) || empty($chartData['legend'])) {
                                $this->resend = true;
                            }
                        }
                        if (!$this->resend) {
                            foreach ($chartsDatas as $key => $chartData) {
                                $filenameData = array();
                                $parts = array('macro', 'indicator', 'reporter', 'commodity', 'currency', 'equity');
                                foreach ($parts as $part) {
                                    if (!empty($chartData[$part])) {
                                        $filenameData[] = $chartData[$part];
                                    }
                                }

                                $image = new Image();
                                $image->bgColor = 'F1F1F1';
                                if ($image->createImage($chartData['img'], array(
                                    'filenameData' => $filenameData,
                                    'legend' => (!empty($chartData['legend']) ? $chartData['legend'] : null),
                                    'source' => (!empty($chartData['source']) ? $chartData['source'] : null),
                                ))) {
                                    $charts[$group][$key] = array(
                                        'data' => $chartData,
                                        'image' => $image,
                                    );
                                }
                            }
                        }
                    }
                }
            }

            $this->_chartImage = $charts;
        }

        return $this->_chartImage;
    }

    protected function getDir () {
        return '/images/reports/';
    }

    public function getFile () {
        return $this->path . $this->filename;
    }

    public function getPath () {
        return YiiBase::getPathOfAlias('root') . $this->getDir() . '/';
    }

    public function getResend () {
        return $this->resend;
    }

    public function getUrl () {
        return Yii::app()->getBaseUrl(true) . $this->getDir() . $this->filename;
        //return YiiBase::getPathOfAlias('website') . $this->getDir() . $this->filename;
    }

    public function listFormat () {
        return array(
            'pdf' => 'PDF',
            'pptx' => 'Power Point Presentation',
        );
    }

    public function listPeriod () {
        return array(
            'month' => 'Monthly',
            'quarter' => 'Quarterly',
            'year' => 'Annual',
        );
    }

    /**
     * @param PhpPresentation $ppt
     * @return PhpPresentation
     */
    protected function pptAddBack ($ppt) {
        $data = $this->getPdfViewData();

        if($data['type'] == 'country') {
            $slide = $ppt->createSlide();
            if($data['type'] == 'market') {
                $slide->setBackground($this->pptSetBg('middle-logo-2'));
            }else{
                $slide->setBackground($this->pptSetBg('middle-icon'));
            }
            $this->pptAddSlideTitle($slide, 'Helpful definitions');

            $links = array(
                'Consumer credit' => 'https://en.wikipedia.org/wiki/Credit_(finance)',
                'Consumer price index' => 'https://en.wikipedia.org/wiki/Consumer_price_index',
                'Consumer spending' => 'https://en.wikipedia.org/wiki/Consumer_spending',
                'Core inflation' => 'https://en.wikipedia.org/wiki/Core_inflation',
                'Disposable income' => 'https://en.wikipedia.org/wiki/Disposable_and_discretionary_income',
                'Export' => 'https://en.wikipedia.org/wiki/Export',
                'GDP' => 'https://en.wikipedia.org/wiki/Gross_domestic_product',
                'Goods and Services' => 'https://en.wikipedia.org/wiki/Goods_and_services',
                'Government revenues' => 'https://en.wikipedia.org/wiki/Government_revenue',
                'Government spending' => 'https://en.wikipedia.org/wiki/Government_spending',
                'Gross fixed capital formation' => 'https://en.wikipedia.org/wiki/Gross_fixed_capital_formation',
                'Imports' => 'https://en.wikipedia.org/wiki/Import',
                'Industrial production' => 'https://en.wikipedia.org/wiki/Industrial_production',
                'Inflation' => 'https://en.wikipedia.org/wiki/Inflation',
                'Producer price index' => 'https://en.wikipedia.org/wiki/Producer_price_index',
                'Trade' => 'https://en.wikipedia.org/wiki/Trade',
                'Trade balance' => 'https://en.wikipedia.org/wiki/Balance_of_trade',
                'Unemployment' => 'https://en.wikipedia.org/wiki/Unemployment',
                'Wages' => 'https://en.wikipedia.org/wiki/Wage',
            );

            $i = 1;
            foreach ($links as $name => $url) {
                if ($i == 1 || $i == ceil(count($links) / 2)) {
                    $shape = $slide->createRichTextShape()
                        ->setHeight(600)
                        ->setWidth(500)
                        ->setOffsetX(($i == 1 ? 70 : 570))
                        ->setOffsetY(200);
                    $shape->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT)
                        ->setMarginLeft(25)
                        ->setIndent(-25);
                    $shape->getActiveParagraph()->getFont()->setSize(12);
                    $shape->getActiveParagraph()->getBulletStyle()->setBulletType(\PhpOffice\PhpPresentation\Style\Bullet::TYPE_BULLET);
                } else {
                    $shape->createParagraph();
                }
                $shape->createTextRun($name . ': ');
                $shape->createTextRun($url)->getFont()->setColor(new Color('b71c1c'));
                $i++;
            }
        }


        $slide = $ppt->createSlide();
        if($data['type'] == 'market') {
            $slide->setBackground($this->pptSetBg('back-2'));
        }else{
            $slide->setBackground($this->pptSetBg('back'));
        }

        return $ppt;
    }

    /**
     * @param PhpPresentation $ppt
     * @return PhpPresentation
     */
    protected function pptAddChart ($ppt, $chart, $pageNum) {
        $data = $this->getPdfViewData();

        $slide = $ppt->createSlide();
        if($data['type'] == 'market') {
            $slide->setBackground($this->pptSetBg('middle-logo-2'));
        }else{
            $slide->setBackground($this->pptSetBg('middle-icon'));
        }

        $this->pptAddSlideTitle($slide, $chart->addTitle());

        $shape = new Drawing\File();

        $shape->setPath($chart->image->file)
            ->setHeight(516)
            ->setWidth(980)
            ->setOffsetX(70)
            ->setOffsetY(150);
        $slide->addShape($shape);

        $shape = $slide->createRichTextShape()
            ->setHeight(100)
            ->setWidth(500)
            ->setOffsetX(50)
            ->setOffsetY(730);
        $shape->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
        $shape->createTextRun('© Complete Intelligence'.date('Y'))->getFont()->setSize(10)->setColor(new Color('ffffff'));

        $shape = $slide->createRichTextShape()
            ->setHeight(100)
            ->setWidth(100)
            ->setOffsetX(950)
            ->setOffsetY(730);
        $shape->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $shape->createTextRun($pageNum)->getFont()->setSize(10)->setColor(new Color('ffffff'));


        return $ppt;
    }

    /**
     * @param PhpPresentation $ppt
     * @return PhpPresentation
     */
    protected function pptAddFront ($ppt) {
        $data = $this->getPdfViewData();
        $slide = $ppt->getActiveSlide();
        if($data['type'] == 'market') {
            $slide->setBackground($this->pptSetBg('front'));
            $shape = $slide->createRichTextShape()
                ->setHeight(400)
                ->setWidth(1000)
                ->setOffsetX(110)
                ->setOffsetY(275);
            $shape->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
            $shape->createTextRun(ucfirst($data['type']).' Report Forecast Update')->getFont()->setSize(36)->setColor(new Color('ffffff'));

            $shape = $slide->createRichTextShape()
                ->setHeight(200)
                ->setWidth(1000)
                ->setOffsetX(110)
                ->setOffsetY(350);
            $shape->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
            $shape->createTextRun(date('F Y'))->getFont()->setSize(28)->setColor(new Color('ffffff'));

        }else {
            $slide->setBackground($this->pptSetBg('front'));
            $shape = $slide->createRichTextShape()
                ->setHeight(400)
                ->setWidth(1000)
                ->setOffsetX(110)
                ->setOffsetY(275);
            $shape->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
            $shape->createTextRun(ucfirst($data['type']).' Report Forecast Update')->getFont()->setSize(36)->setColor(new Color('ffffff'));


            $shape = $slide->createRichTextShape()
                ->setHeight(200)
                ->setWidth(1000)
                ->setOffsetX(110)
                ->setOffsetY(350);
            $shape->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
            $shape->createTextRun(date('F Y'))->getFont()->setSize(28)->setColor(new Color('ffffff'));
        }

        $shape = $slide->createRichTextShape()
            ->setHeight(100)
            ->setWidth(500)
            ->setOffsetX(50)
            ->setOffsetY(730);
        $shape->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
        $shape->createTextRun('© Complete Intelligence'.date('Y'))->getFont()->setSize(10)->setColor(new Color('ffffff'));

        return $ppt;
    }

    /**
     * @param \PhpOffice\PhpPresentation\Slide $slide
     * @return \PhpOffice\PhpPresentation\Slide
     */
    protected function pptAddSlideTitle ($slide, $title) {
        $data = $this->getPdfViewData();

        if($data['type'] == 'market') {
            $shape = $slide->createRichTextShape()
                ->setHeight(100)
                ->setWidth(800)
                ->setOffsetX(70)
                ->setOffsetY(30);
        }else{
            $shape = $slide->createRichTextShape()
                ->setHeight(100)
                ->setWidth(800)
                ->setOffsetX(70)
                ->setOffsetY(30);
        }
        $shape->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
        $shape->createTextRun($title)->getFont()->setSize(32);


        return $slide;
    }

    /**
     * @param PhpPresentation $ppt
     * @param string $title
     * @return PhpPresentation
     */
    protected function pptAddTable ($ppt, $chart) {
        $data = $this->getPdfViewData();
        $slide = $ppt->createSlide();
        if($data['type'] == 'market') {
            $slide->setBackground($this->pptSetBg('middle-logo-3'));
        }else{
            $slide->setBackground($this->pptSetBg('middle-logo'));
        }

        $cols = [];

        header('Content-Type: text/html');
        //echo CJSON::encode(array($cols));
        //exit;

        if(!empty($chart->tableData)) {
            if(!empty($chart->tableData['cols'])) {
                foreach ($chart->tableData['cols'] as $dataCol) {
                    $cols[] = $dataCol;
                }
            }else if(!empty($chart->tableData['vertical'])) {
                foreach ($chart->tableData['vertical'] as $dataRow) {
                    foreach ($dataRow as $dataCell){
                        $cols[] = '';
                    }
                    break;
                }
            }
        }

        //$shape = $slide->createTableShape([$cols]);

        /*if(!empty($chart->tableData)) {
            if(!empty($chart->tableData['rows'])) {
                foreach ($chart->tableData['rows'] as $dataRow) {
                    $row = $shape->createRow();
                    foreach ($dataRow as $dataCell){
                        $cell = $row->nextCell();
                        $cell->addText($dataCell);
                    }
                }
            }else if(!empty($chart->tableData['vertical'])) {
                foreach ($chart->tableData['vertical'] as $dataRow) {
                    $row = $shape->createRow();
                    foreach ($dataRow as $dataCell){
                        $cell = $row->nextCell();
                        $cell->addText($dataCell);
                    }
                }
            }
        }*/

        $shape = $slide->createRichTextShape()
            ->setHeight(100)
            ->setWidth(500)
            ->setOffsetX(50)
            ->setOffsetY(730);
        $shape->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
        $shape->createTextRun('© Complete Intelligence'.date('Y'))->getFont()->setSize(10)->setColor(new Color('ffffff'));


        return $ppt;
    }

    /**
     * @param PhpPresentation $ppt
     * @param string $title
     * @return PhpPresentation
     */
    protected function pptAddTitle ($ppt, $title, $pageNum) {
        $data = $this->getPdfViewData();
        $slide = $ppt->createSlide();
        if($data['type'] == 'market') {
            $slide->setBackground($this->pptSetBg('middle-logo-3'));
        }else{
            $slide->setBackground($this->pptSetBg('middle-logo'));
        }

        $shape = $slide->createRichTextShape()
            ->setHeight(200)
            ->setWidth(980)
            ->setOffsetX(70)
            ->setOffsetY(320);
        $shape->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $shape->createTextRun($title)->getFont()->setSize(54);

        $shape = $slide->createRichTextShape()
            ->setHeight(100)
            ->setWidth(500)
            ->setOffsetX(50)
            ->setOffsetY(730);
        $shape->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
        $shape->createTextRun('© Complete Intelligence'.date('Y'))->getFont()->setSize(10)->setColor(new Color('ffffff'));

        $shape = $slide->createRichTextShape()
            ->setHeight(100)
            ->setWidth(100)
            ->setOffsetX(950)
            ->setOffsetY(730);
        $shape->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $shape->createTextRun($pageNum)->getFont()->setSize(10)->setColor(new Color('ffffff'));

        return $ppt;
    }

    /**
     * @param string $type
     * @return Image
     */
    protected function pptSetBg ($type) {
        $shape = new \PhpOffice\PhpPresentation\Slide\Background\Image();
        $shape->setPath('./images/ppt/report-landscape-' . $type . '.png');

        return $shape;
    }

    public function save(){
        $valid = false;

        $this->getChartImage();
        if(!$this->resend) {
            switch ($this->format) {
                case 'pdf':
                    $valid = $this->createPdf();
                    break;

                case 'pptx':
                    $valid = $this->createPpt();
                    break;
            }
            $this->deleteImages();
        }

        return $valid;
    }

    private function setCharts($chartData){
        $data = [];
        $attrs = array(
            'period',
            'startTime',
            'endTime',
            'reporter',
            'sector',
            'partner',
            'indicator',

            'macro',

            'compare',
            'item',
        );
        $i = 0;
        $prevGroup = '';

        if(!empty($chartData)){
            foreach($chartData as $market => $assets){
                if(!empty($assets)) {
                    foreach ($assets as $asset) {
                        $options = [
                            'view' => 'pdf'
                        ];
                        foreach ($attrs as $attr) {
                            if (!empty($asset[$attr])) {
                                $options[$attr] = $asset[$attr];
                            }
                        }

                        if (!empty($asset['img'])) {
                            $image = new Image();
                            $image->createImage($asset['img'], array(
                                'filenameData' => array(),
                                'legend' => (!empty($asset['legend']) ? $asset['legend'] : null),
                                'source' => (!empty($asset['source']) ? $asset['source'] : null),
                                //'notes' => (!empty($asset['notes']) ? $asset['notes'] : null),
                                //'title' => (!empty($asset['title']) ? $asset['title'] : null),
                            ));
                            $options['image'] = $image;
                        }

                        switch ($market){
                            case 'commodity':
                            case 'currency':
                            case 'equity':
                            case 'market':
                            $widget = Yii::app()->controller->widget('application.widgets.MarketWidget', $options);
                                break;

                            case 'trade':
                                $widget = Yii::app()->controller->widget('application.widgets.TradeWidget', $options);
                                break;

                            case 'macro':
                                $widget = Yii::app()->controller->widget('application.widgets.MacroWidget', $options);
                                break;
                        }

                        if(!empty($widget)) {
                            if (empty($data[$widget->type])) {
                                $data[$widget->type] = [];
                            }

                            $group = $widget->getTypeLabel();
                            if ($group != $prevGroup) {
                                $data[$widget->type]['title'] = ['title' => $group];
                            }
                            $prevGroup = $group;

                            $data[$widget->type][] = $widget;
                        }
                    }
                }
            }
            ksort($data);
        }

        $this->charts = $data;
    }

    public function setDefaults () {
        $chartOptions = Yii::app()->controller->chartOptionDefaults();
        $this->period = $chartOptions['period'];
        $this->startTime = $chartOptions['startTime'];
        $this->endTime = $chartOptions['endTime'];

        $this->format = 'pdf';
    }

}