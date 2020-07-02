<div class="chart-group"
     data-chart-type="bom"
     data-chart-id="<?php echo $this->chartId; ?>">
    <div class="chart-group-container">

        <div class="chart-group-data">
            <?php $this->render('bom/data'); ?>
        </div>

        <?php
        if($this->showTable){
            echo '<div class="chart-group-table"></div>';
        }
        if($this->showChart){
            echo '<div class="chart-group-chart row"></div>';
        }
        ?>

    </div>
</div>