<?php
$items = array();
//$reporters = DbReporters::model()->countryReport()->findAll(array('order' => 'country ASC'));
$reporters = DbReporters::model()->findAll(array('order' => 'country ASC'));
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
                $items[] = '</div>'; //close tag for .chart-search-list-subgroup
            }
            if ($newRow) {
                $newRow = false;
                $items[] = '</div><div class="col-md-4 chart-search-list-column">';
            }
            $letter = $reporter->country[0];
            $items[] = '<div class="chart-search-list-subgroup" data-group="'.strtoupper($letter).'">';
            $items[] = '<h3>' . strtoupper($letter) . '</h3>';
        }

        $available = false;
        $premium = '';
        if (Yii::app()->user->checkToolAccess('tra', 'service-pro')) {
            $premium = '<span class="badge badge-primary">Pro</span>';
            $available = true;
        }
        if ($reporter->access == 1) {
            if(Yii::app()->user->checkToolAccess('tra', 'service-ess')){
                $premium = '<span class="badge badge-primary">Essentials</span>';
                $available = true;
            } else {
                $premium = '<span class="badge">Essentials</span>';
            }
        } elseif ($reporter->access == 2) {
            if (Yii::app()->user->checkToolAccess('tra', 'service-pro')) {
                $premium = '<span class="badge badge-primary">Pro</span>';
                $available = true;
            } else {
                $premium = '<span class="badge">Pro</span>';
            }
        }

        $items[] = CHtml::link($reporter->ccode3 . ': ' . $reporter->country . $premium, '#',
            array(
                'class' => 'list-group-item ' . (!$available ? 'list-group-item-disabled' : ''),
                'data-ref' => $reporter->ccode3,
                'data-label' => $reporter->name,
            )
        );
    }
    $items[] = '</div>'; //close tag for .chart-search-list-subgroup
}
?>

    <div class="content-header wow slideInDown">
        <div class="container">
            <h3>Country Reports</h3>
        </div>
    </div>

    <div class="container wow zoomIn">
        <div class="text-center margin-top-30">
            <h4>Downloading a Country Report</h4>
            <p>To download a pdf report of trade and economic data for this month,<br>select a time period and then select a country from below.</p>
            <p>Data availability is dependent upon the frequency selected.<br>Less data is available for higher frequency reports.</p>
        </div>

        <div class="sep sep-blue sep-md l-sep-40"></div>

        <?php $form = $this->beginWidget('CActiveForm', array(
            'enableAjaxValidation' => false,
            'enableClientValidation' => false,
        )); ?>

        <div class="text-center margin-bottom-20">
            <h4>Select frequency</h4>
            <div class="btn-group chart-period margin-bottom-20-xxs" role="group" data-period="<?php echo $model->period; ?>">
                <button type="button" class="btn btn-sm <?php echo($model->period == 'month' ? 'btn-primary' : 'btn-default'); ?>" data-period="month" aria-label="Monthly">
                    Monthly
                </button>
                <button type="button" class="btn btn-sm <?php echo($model->period == 'quarter' ? 'btn-primary' : 'btn-default'); ?>" data-period="quarter" aria-label="Quarterly">
                    Quarterly
                </button>
                <button type="button" class="btn btn-sm <?php echo($model->period == 'annual' ? 'btn-primary' : 'btn-default'); ?>" data-period="annual" aria-label="Annual">
                    Annual
                </button>
            </div>
        </div>
        <div class="text-center margin-bottom-20">
            <h4>Select a date range</h4>
            <div class="btn-group chart-time" role="group" data-start="<?php echo date('M Y', $model->startTime); ?>" data-end="<?php echo date('M Y', $model->endTime); ?>">
                <a href="#" class="btn btn-sm btn-default" data-toggle="modal" data-target="#chartTimeModal" aria-label="Change Date">
                    <i class="fa fa-calendar"></i>
                    <span class="month <?php echo($model->period == 'month' ? '' : 'hidden'); ?>"><?php echo date('M Y', $model->startTime) . ' - ' . date('M Y', $model->endTime); ?></span>
                    <span class="quarter <?php echo($model->period == 'quarter' ? '' : 'hidden'); ?>"><?php echo ActiveRecord::model()->getQuarter(date('m', $model->startTime)) . date(' Y', $model->startTime) . ' - ' . date('Y', $model->endTime); ?></span>
                    <span class="annual <?php echo($model->period == 'annual' ? '' : 'hidden'); ?>"><?php echo date('Y', $model->startTime) . ' - ' . date('Y', $model->endTime); ?></span>
                </a>
            </div>
        </div>

        <div class="row margin-bottom-20">
            <div class="col-md-6 col-md-push-3">
                <h4 class="text-center">Pick up to 10 indicators</h4>
                <div class="form-group">
                    <select name="reports" id="FormReportCountry_reports" class="selectpicker form-control" multiple title="Select which reports to include" data-max-options="10" data-selected-text-format="count > 3"
                            data-live-search="true">
                        <?php
                        foreach ($model->listReports() as $group => $groupReports) {
                            $cat = 'macro';
                            if (substr($group, 0, 6) === 'Trade:') {
                                $cat = 'trade';
                            }
                            echo '<optgroup label="' . $group . '">';
                            foreach ($groupReports as $reportId => $data) {
                                echo '<option value="' . $reportId . '" data-cat="' . $cat . '" ' . (!empty($data['selected']) ? 'selected="selected"' : '') . '>' . (!empty($data['name']) ? $data['name'] : $reportId) . '</option>';
                            }
                            echo '</optgroup>';
                        }
                        ?>
                    </select>
                </div>
            </div>
        </div>


        <div class="search-list-container" data-type="countryReport">
            <div class="row">
                <div class="col-md-6 col-md-push-3">
                    <h4 class="text-center reporter-field-label">Select a country (<span class="country-count"><?php echo count($items); ?></span> Available)</h4>
                    <div class="form-group form-typeahead">
                        <div class="input-group">
                            <?php echo $form->textField($model, 'reporter', array(
                                'class' => 'form-control',
                                'placeholder' => 'Country name or country ISO code',
                                'maxlength' => 255
                            )); ?>
                            <span class="input-group-btn">
                            <a href="#" class="btn btn-primary toggleBtn" data-toggle="chart-search-list-reporter"><span class="fa fa-list"></span> List All</a>
                            <a href="#" class="btn btn-primary clearBtn hidden"><span class="fa fa-times"></span> Clear</a>
                        </span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="chart-search-list chart-search-list-reporter hidden">
                <div class="row report-items">
                    <div class="col-md-4 chart-search-list-column">
                        <?php
                        if (!empty($items)) {
                            foreach ($items as $item) {
                                echo $item;
                            }
                        }
                        ?>
                        <div class="clearfix"></div>
                    </div>
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
                    <a href="#" class="btn btn-disabled btn-block cr-submit-btn"><span class="fa fa-download"></span> Download Your Report</a>
                </div>
            </div>
        </div>

        <?php $this->endWidget(); ?>

    </div>

    <div class="container country-report">
        <div class="report-data">
            <?php $chart = $this->widget('application.widgets.MacroWidget', array(
                'view' => 'group',
                'init' => true,
                'editable' => false,
                'showEmpty' => true,
                'showTable' => false,
            )); ?>
            <?php $chart = $this->widget('application.widgets.TradeWidget', array(
                'view' => 'landing',
                'init' => true,
                'editable' => false,
                'showEmpty' => true,
                'showTable' => false,
            )); ?>
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