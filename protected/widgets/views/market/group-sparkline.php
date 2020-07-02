<div class="chart-group chart-group-spark"
     data-chart-type="<?php echo $this->market; ?>"
     data-chart-id="<?php echo $this->chartId; ?>">
    <div class="chart-group-container">
        <div class="chart-group-data">
            <?php $this->render('market/data'); ?>
        </div>
        <div class="chart-group-chart row"></div>
    </div>
</div>