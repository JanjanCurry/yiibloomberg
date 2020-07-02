<?php
$items = array(
    'reporter' => array(),
    'partner' => array(),
    'sector' => array(),
);
$reporters = DbReporters::model()->trade()->findAll(array('order' => 'country ASC'));
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

        $premium = '<span class="badge">Pro</span>';
        if (Yii::app()->user->checkToolAccess('tra', 'country-full')) {
            $premium = '<span class="badge badge-primary">Pro</span>';
        }
        if ($reporter->searchDef == 1) {
            $premium = '<span class="badge badge-primary">Essentials</span>';
        }

        $items['reporter'][] = '<a href="#" class="list-group-item" data-ref="' . $reporter->ccode3 . '" data-label="' . $reporter->ccode3 . ', ' . $reporter->country . '">' . $reporter->ccode3 . ': ' . $reporter->country . $premium . '</a>';
    }
    $items['reporter'][] = '</div>'; //close tag for .chart-search-list-subgroup
}

$partners = DbPartners::model()->findAll(array('order' => 'country ASC'));
if (!empty($partners)) {
    $i = 0;
    $total = ceil(count($partners) / 3);
    $letter = '';
    $newRow = false;
    foreach ($partners as $partner) {
        if ($i == $total || $i == ($total * 2)) {
            $newRow = true;
        }
        $i++;
        if ($letter != $partner->country[0]) {
            if($i > 1){
                $items['partner'][] = '</div>'; //close tag for .chart-search-list-subgroup
            }
            if ($newRow) {
                $newRow = false;
                $items['partner'][] = '</div><div class="col-md-4 chart-search-list-column">';
            }
            $letter = $partner->country[0];
            $items['partner'][] = '<div class="chart-search-list-subgroup" data-group="'.strtoupper($letter).'">';
            $items['partner'][] = '<h3>' . strtoupper($letter) . '</h3>';
        }

        $items['partner'][] = '<a href="#" class="list-group-item" data-ref="' . $partner->ccode3 . '" data-label="' . $partner->ccode3 . ', ' . $partner->country . '">' . $partner->ccode3 . ': ' . $partner->country . '</a>';
    }
    $items['partner'][] = '</div>'; //close tag for .chart-search-list-subgroup
}

$sectors = DbSectors::model()->findAll(array('order' => 'code ASC'));
if (!empty($sectors)) {
    $i = 0;
    $total = ceil(count($sectors) / 3);
    $letter = '';
    $newRow = false;
    foreach ($sectors as $sector) {
        if ($i == $total || $i == ($total * 2)) {
            $newRow = true;
        }
        $i++;
        if ($letter != $sector->code[0] . $sector->code[1]) {
            if($i > 1){
                $items['sector'][] = '</div>'; //close tag for .chart-search-list-subgroup
            }
            if ($newRow) {
                $newRow = false;
                $items['sector'][] = '</div><div class="col-md-4 chart-search-list-column">';
            }
            $letter = $sector->code[0] . $sector->code[1];
            $items['sector'][] = '<div class="chart-search-list-subgroup" data-group="'.strtoupper($letter).'">';
            $items['sector'][] = '<h3>' . strtoupper($letter) . '</h3>';
        }

        $items['sector'][] = '<a href="#" class="list-group-item" data-ref="' . $sector->code . '" data-label="' . $sector->code . ', ' . $sector->name . '">' . $sector->code . ': ' . $sector->name . '</a>';
    }
    $items['sector'][] = '</div>'; //close tag for .chart-search-list-subgroup
}
?>

<div class="modal fade-scale chart-modal chart-modal-compare" id="chartCompareModal" tabindex="-1" data-type="trade-compare">
    <div class="modal-dialog modal-fw" role="document">
        <div class="modal-content">
            <div class="modal-body">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><i class="fa fa-times"></i></button>
                <h3 class="model-title">Compare Country</h3>
                <p>Enter the name or ISO code of up to 2 countries that you would like to add as comparisons.</p>

                <div class="form-group form-typeahead ccmReporter">
                    <div class="input-group">
                        <?php echo CHtml::textField('ccmReporter-1', null, array(
                            'class' => 'form-control',
                            'placeholder' => 'Compare 1: Country name or country ISO code',
                            'maxlength' => 255
                        )); ?>
                        <span class="input-group-btn">
                            <a href="#" class="btn btn-primary toggleBtn" data-toggle="chart-search-list-reporter"><span class="fa fa-list"></span> List All</a>
                            <a href="#" class="btn btn-primary clearBtn hidden"><span class="fa fa-times"></span> Clear</a>
                        </span>
                    </div>
                </div>

                <div class="form-group form-typeahead ccmReporter">
                    <div class="input-group">
                        <?php echo CHtml::textField('ccmReporter-2', null, array(
                            'class' => 'form-control',
                            'placeholder' => 'Compare 2: Country name or country ISO code',
                            'maxlength' => 255
                        )); ?>
                        <span class="input-group-btn">
                            <a href="#" class="btn btn-primary toggleBtn" data-toggle="chart-search-list-reporter"><span class="fa fa-list"></span> List All</a>
                            <a href="#" class="btn btn-primary clearBtn hidden"><span class="fa fa-times"></span> Clear</a>
                        </span>
                    </div>
                </div>


                <div class="form-group margin-top-20">
                    <a href="#" class="btn btn-primary btn-block chart-submit-btn"><span class="fa fa-arrow-right"></span> Compare Country</a>
                </div>

                <div class="chart-search-list chart-search-list-reporter hidden">
                    <h3>All Country Reporters</h3>
                    <div class="row">
                        <div class="col-md-4 chart-search-list-column">
                            <?php
                            if (!empty($items['reporter'])) {
                                foreach ($items['reporter'] as $item) {
                                    echo $item;
                                }
                            }
                            ?>
                            <div class="clearfix"></div>
                        </div>
                    </div>
                </div>

                <div class="ccm-countries"></div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade-scale chart-modal chart-modal-add" id="chartTradeModal" tabindex="-1" data-type="trade">
    <div class="modal-dialog modal-fw" role="document">
        <div class="modal-content">
            <div class="modal-body">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><i class="fa fa-times"></i></button>
                <h3 class="model-title">Add Indicator</h3>

                <?php echo CHtml::hiddenField('aimId', null, array('class' => 'form-control')); ?>

                <div class="form-group form-typeahead aimReporter hidden">
                    <p>Select a country to base your report on</p>
                    <span class="input-group">
                        <?php echo CHtml::textField('aimReporter', null, array(
                            'class' => 'form-control',
                            'placeholder' => 'Reporter Country: Country name or ISO code',
                            'maxlength' => 255
                        )); ?>
                        <span class="input-group-btn">
                            <a href="#" class="btn btn-primary toggleBtn" data-toggle="chart-search-list-reporter"><span class="fa fa-list"></span> List All</a>
                            <a href="#" class="btn btn-primary clearBtn hidden"><span class="fa fa-times"></span> Clear</a>
                        </span>
                    </span>
                </div>


                <div class="form-group aimIndicator">
                    <p>Select an indicator form the list</p>
                    <?php echo CHtml::dropDownList('aimIndicator', null, $this->listIndicator(), array(
                        'class' => 'form-control selectpicker ',
                        'empty' => 'Nothing Selected'
                    )); ?>
                </div>

                <p class="aimPartnerLabel hidden"><?php echo(Yii::app()->user->checkToolAccess('tra', 'partner-multi') ? 'Enter the name or ISO code of up to <span class="count">3 partner countries</span> to add to your report.' : 'Enter the name or ISO code a partner country for your report.'); ?></p>
                <div class="form-group form-typeahead hidden aimPartner">
                    <span class="input-group">
                        <?php echo CHtml::textField('aimPartner-1', null, array(
                            'class' => 'form-control',
                            'placeholder' => (Yii::app()->user->checkToolAccess('tra', 'partner-multi') ? 'Partner 1: ' : '') . 'Country name or country ISO code',
                            'maxlength' => 255
                        )); ?>
                        <span class="input-group-btn">
                            <a href="#" class="btn btn-primary toggleBtn" data-toggle="chart-search-list-partner"><span class="fa fa-list"></span> List All</a>
                            <a href="#" class="btn btn-primary clearBtn hidden"><span class="fa fa-times"></span> Clear</a>
                        </span>
                    </span>
                </div>

                <?php if (Yii::app()->user->checkToolAccess('tra', 'partner-multi')) { ?>
                    <div class="form-group form-typeahead hidden aimPartner">
                    <span class="input-group">
                        <?php echo CHtml::textField('aimPartner-2', null, array(
                            'class' => 'form-control',
                            'placeholder' => 'Partner 2: Country name or country ISO code',
                            'maxlength' => 255
                        )); ?>
                        <span class="input-group-btn">
                            <a href="#" class="btn btn-primary toggleBtn" data-toggle="chart-search-list-partner"><span class="fa fa-list"></span> List All</a>
                            <a href="#" class="btn btn-primary clearBtn hidden"><span class="fa fa-times"></span> Clear</a>
                        </span>
                    </span>
                    </div>

                    <div class="form-group form-typeahead hidden aimPartner">
                    <span class="input-group">
                        <?php echo CHtml::textField('aimPartner-3', null, array(
                            'class' => 'form-control',
                            'placeholder' => 'Partner 3: Country name or country ISO code',
                            'maxlength' => 255
                        )); ?>
                        <span class="input-group-btn">
                            <a href="#" class="btn btn-primary toggleBtn" data-toggle="chart-search-list-partner"><span class="fa fa-list"></span> List All</a>
                            <a href="#" class="btn btn-primary clearBtn hidden"><span class="fa fa-times"></span> Clear</a>
                        </span>
                    </span>
                    </div>
                <?php } ?>

                <p class="aimSectorLabel hidden"><?php echo(Yii::app()->user->checkToolAccess('tra', 'partner-multi') ? 'Enter the name or ISO code of up to <span class="count">3 commodities</span> to add to your report.' : 'Enter the name or ISO code a commodity for your report.'); ?></p>
                <div class="form-group form-typeahead hidden aimSector">
                    <span class="input-group">
                        <?php echo CHtml::textField('aimSector-1', null, array(
                            'class' => 'form-control',
                            'placeholder' => (Yii::app()->user->checkToolAccess('tra', 'partner-multi') ? 'Sector 1: ' : '') . 'Search name (tea, chocolate, etc.) or code (0902, 1806, etc.)',
                            'maxlength' => 255
                        )); ?>
                        <span class="input-group-btn">
                            <a href="#" class="btn btn-primary toggleBtn" data-toggle="chart-search-list-sector"><span class="fa fa-list"></span> List All</a>
                            <a href="#" class="btn btn-primary clearBtn hidden"><span class="fa fa-times"></span> Clear</a>
                        </span>
                    </span>
                </div>

                <?php if (Yii::app()->user->checkToolAccess('tra', 'partner-multi')) { ?>
                    <div class="form-group form-typeahead hidden aimSector">
                    <span class="input-group">
                        <?php echo CHtml::textField('aimSector-2', null, array(
                            'class' => 'form-control',
                            'placeholder' => 'Sector 2: Search name (tea, chocolate, etc.) or code (0902, 1806, etc.)',
                            'maxlength' => 255
                        )); ?>
                        <span class="input-group-btn">
                            <a href="#" class="btn btn-primary toggleBtn" data-toggle="chart-search-list-sector"><span class="fa fa-list"></span> List All</a>
                            <a href="#" class="btn btn-primary clearBtn hidden"><span class="fa fa-times"></span> Clear</a>
                        </span>
                    </span>
                    </div>

                    <div class="form-group form-typeahead hidden aimSector">
                    <span class="input-group">
                        <?php echo CHtml::textField('aimSector-3', null, array(
                            'class' => 'form-control',
                            'placeholder' => 'Sector 3: Search name (tea, chocolate, etc.) or code (0902, 1806, etc.)',
                            'maxlength' => 255
                        )); ?>
                        <span class="input-group-btn">
                            <a href="#" class="btn btn-primary toggleBtn" data-toggle="chart-search-list-sector"><span class="fa fa-list"></span> List All</a>
                            <a href="#" class="btn btn-primary clearBtn hidden"><span class="fa fa-times"></span> Clear</a>
                        </span>
                    </span>
                    </div>
                <?php } ?>


                <div class="form-group margin-top-20">
                    <a href="#" class="btn btn-disabled btn-block chart-submit-btn"><span class="fa fa-plus"></span> Add Indicator</a>
                </div>

                <div class="chart-search-list chart-search-list-reporter hidden">
                    <h3>All Country Reporters</h3>
                    <div class="row">
                        <div class="col-md-4 chart-search-list-column">
                            <?php
                            if (!empty($items['reporter'])) {
                                foreach ($items['reporter'] as $item) {
                                    echo $item;
                                }
                            }
                            ?>
                            <div class="clearfix"></div>
                        </div>
                    </div>
                </div>

                <div class="chart-search-list chart-search-list-partner hidden">
                    <h3>All Country Partners</h3>
                    <div class="row">
                        <div class="col-md-4 chart-search-list-column">
                            <?php
                            if (!empty($items['partner'])) {
                                foreach ($items['partner'] as $item) {
                                    echo $item;
                                }
                            }
                            ?>
                            <div class="clearfix"></div>
                        </div>
                    </div>
                </div>

                <div class="chart-search-list chart-search-list-sector hidden">
                    <h3>All Commodities</h3>
                    <div class="row">
                        <div class="col-md-4 chart-search-list-column">
                            <?php
                            if (!empty($items['sector'])) {
                                foreach ($items['sector'] as $item) {
                                    echo $item;
                                }
                            }
                            ?>
                            <div class="clearfix"></div>
                        </div>
                    </div>
                </div>

                <div class="ccm-countries"></div>
            </div>
        </div>
    </div>
</div>