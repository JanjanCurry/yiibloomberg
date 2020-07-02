<?php
$list = array();
$macros = DbMacroList::model()->findAll();
if(!empty($macros)){
    foreach($macros as $macro){
        if(empty($list[$macro->variant])){
            $list[$macro->variant] = array();
        }
        $list[$macro->variant][] = $macro->assetId;
    }

    if(!empty($list)){
        echo '<span class="hidden macro-variants" ';
        foreach($list as $variant => $assetIds){
            echo 'data-'.strtolower($variant).'="'.implode(',',$assetIds).'"';
        }
        echo '></span>';
    }
}

$items = array(
    'reporter' => array(),
);
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

<div class="modal fade-scale chart-modal chart-modal-compare" id="chartCompareModal" tabindex="-1" data-type="macro-compare">
    <div class="modal-dialog modal-fw" role="document">
        <div class="modal-content">
            <div class="modal-body">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><i class="fa fa-times"></i></button>
                <h3 class="model-title">Compare Macro</h3>

                <p>Select an macro from the list to use as a comparison</p>
                <div class="form-group compareMacroSelect">
                    <?php echo CHtml::dropDownList('compareMacro-1', null, $this->listMacro(), array(
                        'class' => 'form-control selectpicker ',
                        'empty' => 'Nothing Selected'
                    )); ?>
                </div>

                <div class="form-group compareMacroSelect">
                    <?php echo CHtml::dropDownList('compareMacro-2', null, $this->listMacro(), array(
                        'class' => 'form-control selectpicker ',
                        'empty' => 'Nothing Selected'
                    )); ?>
                </div>

                <div class="form-group margin-top-20">
                    <a href="#" class="btn btn-primary btn-block chart-submit-btn"><span class="fa fa-pencil"></span> Compare Macro</a>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade-scale chart-modal chart-modal-add" id="chartMacroModal" tabindex="-1"  data-type="macro">
    <div class="modal-dialog modal-fw" role="document">
        <div class="modal-content">
            <div class="modal-body">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><i class="fa fa-times"></i></button>
                <h3 class="model-title">Add Macro</h3>
                <?php echo CHtml::hiddenField('macroChartId', null, array('class' => 'form-control')); ?>

                <p>Select an macro from the list</p>
                <div class="form-group">
                    <?php echo CHtml::dropDownList('macroType', null, $this->listMacro(), array(
                        'class' => 'form-control selectpicker ',
                        'empty' => 'Nothing Selected'
                    )); ?>
                </div>

                <div class="macro-country">
                    <p>Enter the name or ISO code of a country for your report.</p>
                    <div class="form-group form-typeahead cmReporter">
                        <span class="input-group">
                            <?php echo CHtml::textField('macroCountry-1', null, array(
                                'class' => 'form-control',
                                'placeholder' => 'Country 1: Country name or country ISO code',
                                'maxlength' => 255
                            )); ?>
                            <span class="input-group-btn">
                                <a href="#" class="btn btn-primary toggleBtn" data-toggle="chart-search-list-reporter"><span class="fa fa-list"></span> List All</a>
                                <a href="#" class="btn btn-primary clearBtn hidden"><span class="fa fa-times"></span> Clear</a>
                            </span>
                        </span>
                    </div>
                </div>

                <div class="form-group form-typeahead macro-country macro-country-compare cmReporter">
                    <div class="input-group">
                        <?php echo CHtml::textField('macroCountry-2', null, array(
                            'class' => 'form-control',
                            'placeholder' => 'Country 2: Country name or country ISO code',
                            'maxlength' => 255
                        )); ?>
                        <span class="input-group-btn">
                            <a href="#" class="btn btn-primary toggleBtn" data-toggle="chart-search-list-reporter"><span class="fa fa-list"></span> List All</a>
                            <a href="#" class="btn btn-primary clearBtn hidden"><span class="fa fa-times"></span> Clear</a>
                        </span>
                    </div>
                </div>

                <div class="form-group form-typeahead macro-country macro-country-compare cmReporter">
                    <div class="input-group">
                        <?php echo CHtml::textField('macroCountry-3', null, array(
                            'class' => 'form-control',
                            'placeholder' => 'Country 3: Country name or country ISO code',
                            'maxlength' => 255
                        )); ?>
                        <span class="input-group-btn">
                            <a href="#" class="btn btn-primary toggleBtn" data-toggle="chart-search-list-reporter"><span class="fa fa-list"></span> List All</a>
                            <a href="#" class="btn btn-primary clearBtn hidden"><span class="fa fa-times"></span> Clear</a>
                        </span>
                    </div>
                </div>


                <div class="form-group margin-top-20">
                    <a href="#" class="btn btn-disabled btn-block chart-submit-btn"><span class="fa fa-plus"></span> Add Macro</a>
                </div>

                <div class="chart-search-list chart-search-list-reporter hidden">
                    <h3>All Country Reporters</h3>
                    <p>Only countries with data, for the selected macro indicator, are shown below</p>
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