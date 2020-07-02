<div class="chart-item-header hidden">
    <div class="row">
        <div class="col-xs-8">
            <?php
            echo CHtml::link($title, $this->url, array(
                'class' => 'chart-item-title h4',
            ));
            ?>
        </div>
        <div class="col-xs-4 text-right">
            <span class="dash-change-toggles">
                <a href="#" class="btn yoy-edit-btn" data-toggle="tooltip" title="Change Asset"><i class="fa fa-pencil"></i></a>
            </span>
        </div>
    </div>
</div>

<?php $this->render('chart/legend', ['forecast' => false]); ?>