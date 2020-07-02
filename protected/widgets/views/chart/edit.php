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
                            <?php echo CHtml::hiddenField('ctmStartDate', date('M Y', $this->startTime), array(
                                'class' => 'form-control monthPicker',
                                'placeholder' => date('M Y'),
                            )); ?>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <h4 class="text-center">End Date</h4>
                        <div class="form-group">
                            <?php echo CHtml::hiddenField('ctmEndDate', date('M Y', $this->endTime), array(
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

<div class="modal fade-scale chart-modal chart-modal-config" id="chartTypeModal" tabindex="-1" data-type="chartType">
    <div class="modal-dialog modal-fw" role="document">
        <div class="modal-content">
            <div class="modal-body">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><i class="fa fa-times"></i></button>
                <h3 class="model-title">Change Chart Type</h3>

                <p class="chartTypeFieldLabel hidden">Select a chart type</p>
                <div class="form-group chartTypeField">
                    <?php
                    $temp = array();
                    foreach($this->chartTypes as $type){
                        $temp[$type] = ucfirst($type).' Chart';
                    }
                    echo CHtml::radioButtonList('chartType', $this->chartType, $temp, array(
                        'separator' => '',
                        'template' => '<label class="checkbox-inline btn btn-default">{input} {labelTitle}</label>',
                    )); ?>
                </div>

                <div class="form-group">
                    <a href="#" class="btn btn-primary btn-block chart-submit-btn"><span class="fa fa-line-chart"></span> Change Chart Type</a>
                </div>
            </div>
        </div>
    </div>
</div>