<div class="chart-item-header">
    <div class="row">
        <div class="col-xs-4 col-sm-7">
            <?php
            echo CHtml::link($title, $this->url, array(
                'class' => 'chart-item-title h4',
            ));
            ?>
        </div>
        <div class="col-xs-8 col-sm-5 text-right">
            <span class="dash-change-toggles">
                <a href="#" class="btn dash-change-toggle <?php echo ($this->report == 'dash-change' ? 'btn-disabled' : ''); ?>" data-report="dash-change" data-toggle="tooltip" title="Forecast Value"><i class="fa fa-hashtag"></i></a>
                <a href="#" class="btn dash-change-toggle <?php echo ($this->report == 'dash-change-percent' ? 'btn-disabled' : ''); ?>" data-report="dash-change-percent" data-toggle="tooltip" title="Percent Change"><i class="fa fa-percent"></i></a>
                <a href="#" class="btn yoy-edit-btn" data-toggle="tooltip" title="Change Asset"><i class="fa fa-pencil"></i></a>
            </span>
        </div>
    </div>
</div>

<?php $this->render('chart/legend', ['forecast' => false]); ?>