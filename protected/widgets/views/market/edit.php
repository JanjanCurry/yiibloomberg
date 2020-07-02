<?php
$companyId = [0];
$user = Yii::app()->controller->user;
if(!empty($user->companyId)){
    $companyId[] = $user->companyId;
}
$criteria = new CDbCriteria();
$criteria->compare('companyId', $companyId);
$criteria->order = 'name ASC';

$items = array(
    'commodity' => array(),
    'currency' => array(),
    'equity' => array(),
    'reporter' => array(),
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

$reporters = DbReporters::model()->macro()->findAll(array('order' => 'country ASC'));
if (!empty($reporters)) {
    $i = 0;
    $total = ceil(count($reporters) / 3);
    $letter = '';
    $newRow = false;
    foreach ($reporters as $reporter) {
        if ($i == $total || $i == ($total * 2)) {
            $newRow = true;
        }
        $i++;
        if ($letter != $reporter->country[0]) {
            if($i > 1){
                $items['reporter'][] = '</div>'; //close tag for .chart-search-list-subgroup
            }
            if ($newRow) {
                $newRow = false;
                $items['reporter'][] = '</div><div class="col-md-4 chart-search-list-column">';
            }
            $letter = $reporter->country[0];
            $items['reporter'][] = '<div class="chart-search-list-subgroup" data-group="'.strtoupper($letter).'">';
            $items['reporter'][] = '<h3>' . strtoupper($letter) . '</h3>';
        }

        $premium = '';
        if (Yii::app()->user->checkToolAccess('tra', 'service-pro')) {
            $premium = '<span class="badge badge-primary">Pro</span>';
        }
        if ($reporter->access == 1) {
            if(Yii::app()->user->checkToolAccess('tra', 'service-ess')){
                $premium = '<span class="badge badge-primary">Essentials</span>';
            } else {
                $premium = '<span class="badge">Essentials</span>';
            }
        } elseif ($reporter->access == 2) {
            if (Yii::app()->user->checkToolAccess('tra', 'service-pro')) {
                $premium = '<span class="badge badge-primary">Pro</span>';
            } else {
                $premium = '<span class="badge">Pro</span>';
            }
        }

        $items['reporter'][] = '<a href="#" class="list-group-item" data-ref="' . $reporter->ccode3 . '" data-label="' . $reporter->ccode3 . ', ' . $reporter->country . '">' . $reporter->ccode3 . ': ' . $reporter->country . $premium . '</a>';
    }
    $items['reporter'][] = '</div>'; //close tag for .chart-search-list-subgroup
}
?>

<div class="modal fade-scale chart-modal chart-modal-compare" id="chartMarketCompareModal" data-type="<?php echo $this->market; ?>">
    <div class="modal-dialog modal-fw" role="document">
        <div class="modal-content">
            <div class="modal-body">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><i class="fa fa-times"></i></button>
                <h3 class="model-title">Compare Asset</h3>
                <p>Enter the name of an asset that you would like to compare.</p>

                <div class="form-group market-compare-market">
                    <?php echo CHtml::dropDownList('market-compare-market', $this->market, $this->listMarket(), array(
                        'class' => 'form-control selectpicker ',
                    )); ?>
                </div>

                <div class="form-group form-typeahead market-compare commodity-compare hidden">
                    <div class="input-group">
                        <?php echo CHtml::textField('market-compare-1', null, array(
                            'class' => 'form-control',
                            'placeholder' => 'Compare 1: Commodity name or reference code',
                            'maxlength' => 255
                        )); ?>
                        <span class="input-group-btn">
                            <a href="#" class="btn btn-primary toggleBtn" data-toggle="chart-search-list-commodity"><span class="fa fa-list"></span> List All</a>
                            <a href="#" class="btn btn-primary clearBtn hidden"><span class="fa fa-times"></span> Clear</a>
                        </span>
                    </div>
                </div>

                <div class="form-group form-typeahead market-compare currency-compare hidden">
                    <div class="input-group">
                        <?php echo CHtml::textField('market-compare-2', null, array(
                            'class' => 'form-control',
                            'placeholder' => 'Compare 1: Currency name or reference code',
                            'maxlength' => 255
                        )); ?>
                        <span class="input-group-btn">
                            <a href="#" class="btn btn-primary toggleBtn" data-toggle="chart-search-list-currency"><span class="fa fa-list"></span> List All</a>
                            <a href="#" class="btn btn-primary clearBtn hidden"><span class="fa fa-times"></span> Clear</a>
                        </span>
                    </div>
                </div>

                <div class="form-group form-typeahead market-compare equity-compare hidden">
                    <div class="input-group">
                        <?php echo CHtml::textField('market-compare-3', null, array(
                            'class' => 'form-control',
                            'placeholder' => 'Compare 1: Equity name or reference code',
                            'maxlength' => 255
                        )); ?>
                        <span class="input-group-btn">
                            <a href="#" class="btn btn-primary toggleBtn" data-toggle="chart-search-list-equity"><span class="fa fa-list"></span> List All</a>
                            <a href="#" class="btn btn-primary clearBtn hidden"><span class="fa fa-times"></span> Clear</a>
                        </span>
                    </div>
                </div>


                <div class="form-group margin-top-20">
                    <a href="#" class="btn btn-primary btn-block chart-submit-btn"><span class="fa fa-arrow-right"></span> Compare Asset</a>
                </div>

                <?php foreach ($items as $key => $subItems) { ?>
                    <div class="chart-search-list chart-search-list-<?php echo $key; ?> hidden">
                        <h3>All <?php echo $this->getListLabel('listMarketPlural', $key); ?></h3>
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

<div class="modal fade-scale chart-modal chart-modal-add" id="chartMarketModal" tabindex="-1" data-type="<?php echo $this->market; ?>">
    <div class="modal-dialog modal-fw" role="document">
        <div class="modal-content">
            <div class="modal-body">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><i class="fa fa-times"></i></button>
                <h3 class="model-title">Add Asset</h3>
                <?php echo CHtml::hiddenField('marketChartId', null, array('class' => 'form-control')); ?>
                <p>Enter the name of a asset for your report.</p>

                <div class="form-group form-typeahead market-search commodity-search hidden">
                    <div class="input-group">
                        <?php echo CHtml::textField('market-search-1', null, array(
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
                        <?php echo CHtml::textField('market-search-2', null, array(
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
                        <?php echo CHtml::textField('market-search-3', null, array(
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
                    <a href="#" class="btn btn-disabled btn-block chart-submit-btn"><span class="fa fa-plus"></span> Add <?php echo ucfirst($this->market); ?></a>
                </div>

                <?php foreach ($items as $key => $subItems) { ?>
                    <div class="chart-search-list chart-search-list-<?php echo $key; ?> hidden">
                        <h3>All <?php echo $this->getListLabel('listMarketPlural', $key); ?></h3>
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