<?php
$companyId = [0];
if(!empty($this->user->companyId)){
    $companyId[] = $this->user->companyId;
}
$criteria = new CDbCriteria();
$criteria->compare('companyId', $companyId);
$criteria->order = 'name ASC';

$items = array(
    'commodity' => array(),
    'currency' => array(),
    'equity' => array(),
);
$commodities = DbCommodities::model()->findAll($criteria);
if (!empty($commodities)) {
    $i = 0;
    $total = ceil(count($commodities) / 3);
    $letter = '';
    $newRow = false;
    foreach ($commodities as $commodity) {
        if ($i == $total || $i == ($total * 2)) {
            $newRow = true;
        }
        $i++;
        if ($letter != $commodity->name[0]) {
            if($i > 1){
                $items['commodity'][] = '</div>'; //close tag for .chart-search-list-subgroup
            }
            if ($newRow) {
                $newRow = false;
                $items['commodity'][] = '</div><div class="col-md-4 chart-search-list-column">';
            }
            $letter = $commodity->name[0];
            $items['commodity'][] = '<div class="chart-search-list-subgroup" data-group="'.strtoupper($letter).'">';
            $items['commodity'][] = '<h3>' . strtoupper($letter) . '</h3>';
        }

        $premium = '<span class="badge">Pro</span>';
        if (Yii::app()->user->checkToolAccess('com', 'country-full')) {
            $premium = '<span class="badge badge-primary">Pro</span>';
        }
        if ($commodity->access == 1) {
            $premium = '<span class="badge badge-primary">Essentials</span>';
        }

        $items['commodity'][] = '<a href="#" class="list-group-item" data-ref="' . $commodity->code . '" data-label="' . $commodity->name . '">' . $commodity->name . $premium . '</a>';

    }
    $items['commodity'][] = '</div>'; //close tag for .chart-search-list-subgroup
}

$currencies = DbCurrencies::model()->findAll($criteria);
if (!empty($currencies)) {
    $i = 0;
    $total = ceil(count($currencies) / 3);
    $letter = '';
    $newRow = false;
    foreach ($currencies as $currency) {
        if ($i == $total || $i == ($total * 2)) {
            $newRow = true;
        }
        $i++;
        if ($letter != $currency->name[0]) {
            if($i > 1){
                $items['currency'][] = '</div>'; //close tag for .chart-search-list-subgroup
            }
            if ($newRow) {
                $newRow = false;
                $items['currency'][] = '</div><div class="col-md-4 chart-search-list-column">';
            }
            $letter = $currency->name[0];
            $items['currency'][] = '<div class="chart-search-list-subgroup" data-group="'.strtoupper($letter).'">';
            $items['currency'][] = '<h3>' . strtoupper($letter) . '</h3>';
        }

        $premium = '<span class="badge">Pro</span>';
        if (Yii::app()->user->checkToolAccess('cur', 'country-full')) {
            $premium = '<span class="badge badge-primary">Pro</span>';
        }
        if ($currency->access == 1) {
            $premium = '<span class="badge badge-primary">Essentials</span>';
        }

        $items['currency'][] = '<a href="#" class="list-group-item" data-ref="' . $currency->code . '" data-label="' . $currency->name . '">' . $currency->name . $premium . '</a>';

    }
    $items['currency'][] = '</div>'; //close tag for .chart-search-list-subgroup
}

$equities = DbEquities::model()->findAll($criteria);
if (!empty($equities)) {
    $i = 0;
    $total = ceil(count($equities) / 3);
    $letter = '';
    $newRow = false;
    foreach ($equities as $equity) {
        if ($i == $total || $i == ($total * 2)) {
            $newRow = true;
        }
        $i++;
        if ($letter != $equity->name[0]) {
            if($i > 1){
                $items['equity'][] = '</div>'; //close tag for .chart-search-list-subgroup
            }
            if ($newRow) {
                $newRow = false;
                $items['equity'][] = '</div><div class="col-md-4 chart-search-list-column">';
            }
            $letter = $equity->name[0];
            $items['equity'][] = '<div class="chart-search-list-subgroup" data-group="'.strtoupper($letter).'">';
            $items['equity'][] = '<h3>' . strtoupper($letter) . '</h3>';
        }

        $premium = '<span class="badge">Pro</span>';
        if (Yii::app()->user->checkToolAccess('equ', 'country-full')) {
            $premium = '<span class="badge badge-primary">Pro</span>';
        }
        if ($equity->access == 1) {
            $premium = '<span class="badge badge-primary">Essentials</span>';
        }

        $items['equity'][] = '<a href="#" class="list-group-item" data-ref="' . $equity->code . '" data-label="' . $equity->name . '">' . $equity->name . $premium . '</a>';

    }
    $items['equity'][] = '</div>'; //close tag for .chart-search-list-subgroup
}
?>

<div class="content-header wow slideInDown">
    <div class="container">
        <h3>Market Reports</h3>
    </div>
</div>

<div class="container wow zoomIn">
    <div class="text-center margin-top-30">
        <h4>Downloading a Market Report</h4>
        <p>To download a pdf report of market data for this month,<br>select up to 10 market assets from below.</p>
    </div>

    <div class="sep sep-blue sep-md l-sep-40"></div>

    <?php $form = $this->beginWidget('CActiveForm', array(
        'enableAjaxValidation' => false,
        'enableClientValidation' => false,
    )); ?>

    <div class="text-center margin-bottom-20">
        <h4>Select a date range</h4>
        <div class="btn-group chart-time" role="group" data-start="<?php echo date('M Y', $model->startTime); ?>" data-end="<?php echo date('M Y', $model->endTime); ?>" data-type="commodity">
            <a href="#" class="btn btn-sm btn-default" data-toggle="modal" data-target="#chartTimeModal" aria-label="Change Date">
                <i class="fa fa-calendar"></i>
                <span class="month <?php echo($model->period == 'month' ? '' : 'hidden'); ?>"><?php echo date('M Y', $model->startTime) . ' - ' . date('M Y', $model->endTime); ?></span>
                <span class="quarter <?php echo($model->period == 'quarter' ? '' : 'hidden'); ?>"><?php echo ActiveRecord::model()->getQuarter(date('m', $model->startTime)) . date(' Y', $model->startTime) . ' - ' . date('Y', $model->endTime); ?></span>
                <span class="annual <?php echo($model->period == 'annual' ? '' : 'hidden'); ?>"><?php echo date('Y', $model->startTime) . ' - ' . date('Y', $model->endTime); ?></span>
            </a>
        </div>
    </div>

    <h4 class="text-center">Select up to 10 Market Assets</h4>

    <div class="row margin-bottom-20 market-asset-add-btns">
        <?php
        $tools = [];
        if(Yii::app()->user->checkAccess('tool-com')){
            $tools[] = 'commodity';
        }
        if(Yii::app()->user->checkAccess('tool-cur')){
            $tools[] = 'currency';
        }
        if(Yii::app()->user->checkAccess('tool-equ')){
            $tools[] = 'equity';
        }
        if(count($tools) == 1){
            echo '<div class="col-md-4 col-md-push-4">';
            echo '<a hreef="#" class="btn btn-primary btn-block" data-market="'.$tools[0].'">Add '.ucfirst($tools[0]).'</a>';
            echo '</div>';
        }else if(count($tools) == 2){
            echo '<div class="col-md-6 col-md-push-3"><div class="row">';
            foreach($tools as $tool){
                echo '<div class="col-sm-6"><a hreef="#" class="btn btn-primary btn-block" data-market="'.$tool.'">Add '.ucfirst($tool).'</a></div>';
            }
            echo '</div></div>';
        }else{
            echo '<div class="col-md-6 col-md-push-3"><div class="row">';
            foreach($tools as $tool){
                echo '<div class="col-sm-4"><a hreef="#" class="btn btn-primary btn-block" data-market="'.$tool.'">Add '.ucfirst($tool).'</a></div>';
            }
            echo '</div></div>';
        }
        ?>
    </div>

    <div class="market-asset-selection-container">
        <div class="row">
            <div class="col-md-6 col-md-push-3 market-asset-selection">

                <?php
                if(!empty($model->assets)){
                    foreach($model->assets as $key => $name){
                        ?>
                        <div class="form-group">
                            <div class="input-group">
                                <input type="text" class="form-control" readonly maxlength="255" name="assets[<?php echo $key; ?>]" value="<?php echo $name; ?>" id="assets_<?php echo $key; ?>" data-ref="<?php echo $key; ?>" />
                                <span class="input-group-btn">
                                    <a href="#" class="btn btn-accent clearBtn"><span class="fa fa-times"></span> Remove</a>
                                </span>
                            </div>
                        </div>
                        <?php
                    }
                }
                ?>

            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6 col-md-push-3">
            <h4 class="text-center">Select a file format</h4>
            <div class="form-group">
                <?php echo $form->dropDownList($model, 'format', $model->listFormat(), array('class' => 'selectpicker form-control')); ?>
            </div>
        </div>
    </div>


    <div class="row">
        <div class="col-md-4 col-md-push-4">
            <div class="form-group margin-top-20">
                <a href="#" class="btn btn-disabled btn-block mr-submit-btn"><span class="fa fa-download"></span> Download Your Report</a>
            </div>
        </div>
    </div>

    <?php $this->endWidget(); ?>

</div>

<div class="container country-report">
    <div class="report-data">
        <?php $chart = $this->widget('application.widgets.MarketWidget', array(
            'view' => 'group',
            'market' => 'commodity',
            'init' => true,
            'editable' => false,
            'showEmpty' => true,
            'showTable' => true,
        )); ?>
        <?php $chart = $this->widget('application.widgets.MarketWidget', array(
            'view' => 'group',
            'market' => 'currency',
            'init' => true,
            'editable' => false,
            'showEmpty' => true,
            'showTable' => true,
        )); ?>
        <?php $chart = $this->widget('application.widgets.MarketWidget', array(
            'view' => 'group',
            'market' => 'equity',
            'init' => true,
            'editable' => false,
            'showEmpty' => true,
            'showTable' => true,
        )); ?>
    </div>

</div>

<div class="modal fade-scale chart-modal chart-modal-add" id="chartMarketModal" tabindex="-1" data-type="">
    <div class="modal-dialog modal-fw" role="document">
        <div class="modal-content">
            <div class="modal-body">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><i class="fa fa-times"></i></button>
                <h3 class="model-title">Add Asset</h3>
                <p>Enter the name of a asset for your report.</p>

                <div class="form-group form-typeahead market-search commodity-search hidden">
                    <div class="input-group">
                        <?php echo CHtml::textField('market-search-com', null, array(
                            'class' => 'form-control',
                            'placeholder' => 'Commodity name or reference code',
                            'maxlength' => 255
                        )); ?>
                        <span class="input-group-btn">
                            <a href="#" class="btn btn-primary toggleBtn" data-toggle="chart-search-list-commodity"><span class="fa fa-list"></span> List All</a>
                            <a href="#" class="btn btn-primary clearBtn hidden"><span class="fa fa-times"></span> Clear</a>
                        </span>
                    </div>
                </div>

                <div class="form-group form-typeahead market-search currency-search hidden">
                    <div class="input-group">
                        <?php echo CHtml::textField('market-search-cur', null, array(
                            'class' => 'form-control',
                            'placeholder' => 'Currency name or reference code',
                            'maxlength' => 255
                        )); ?>
                        <span class="input-group-btn">
                            <a href="#" class="btn btn-primary toggleBtn" data-toggle="chart-search-list-currency"><span class="fa fa-list"></span> List All</a>
                            <a href="#" class="btn btn-primary clearBtn hidden"><span class="fa fa-times"></span> Clear</a>
                        </span>
                    </div>
                </div>

                <div class="form-group form-typeahead market-search equity-search hidden">
                    <div class="input-group">
                        <?php echo CHtml::textField('market-search-equ', null, array(
                            'class' => 'form-control',
                            'placeholder' => 'Equity name or reference code',
                            'maxlength' => 255
                        )); ?>
                        <span class="input-group-btn">
                            <a href="#" class="btn btn-primary toggleBtn" data-toggle="chart-search-list-equity"><span class="fa fa-list"></span> List All</a>
                            <a href="#" class="btn btn-primary clearBtn hidden"><span class="fa fa-times"></span> Clear</a>
                        </span>
                    </div>
                </div>


                <div class="form-group margin-top-20">
                    <a href="#" class="btn btn-disabled btn-block chart-submit-btn"><span class="fa fa-plus"></span> Add Asset</a>
                </div>

                <?php foreach ($items as $key => $subItems) { ?>
                    <div class="chart-search-list chart-search-list-<?php echo $key; ?> hidden">
                        <h3>All <?php echo $model->getListLabel('listMarketPlural', $key); ?></h3>
                        <div class="row">
                            <div class="col-md-4 chart-search-list-column">
                                <?php
                                if (!empty($subItems)) {
                                    foreach ($subItems as $item) {
                                        echo $item;
                                    }
                                }
                                ?>
                                <div class="clearfix"></div>
                            </div>
                        </div>
                    </div>
                <?php } ?>

                <div class="ccm-countries"></div>
            </div>
        </div>
    </div>
</div>

<div class="container">
    <div class="modal fade-scale chart-modal chart-modal-config" id="chartTimeModal" tabindex="-1" data-type="chartTime">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-body">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><i class="fa fa-times"></i></button>
                    <h3 class="model-title text-center">Date Range</h3>

                    <div class="row">
                        <div class="col-sm-6">
                            <h4 class="text-center">Start Date</h4>
                            <div class="form-group">
                                <?php echo CHtml::hiddenField('ctmStartDate', date('M Y', $model->startTime), array(
                                    'class' => 'form-control monthPicker',
                                    'placeholder' => date('M Y'),
                                )); ?>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <h4 class="text-center">End Date</h4>
                            <div class="form-group">
                                <?php echo CHtml::hiddenField('ctmEndDate', date('M Y', $model->endTime), array(
                                    'class' => 'form-control monthPicker',
                                    'placeholder' => date('M Y'),
                                )); ?>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <a href="#" class="btn btn-disabled btn-block chart-submit-btn"><span class="fa fa-calendar"></span> Set Date Range</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php $this->renderPartial('//common/feedback'); ?>