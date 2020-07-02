<div class="chart-group"
     data-chart-type="<?php echo $this->market; ?>"
     data-chart-id="<?php echo $this->chartId; ?>">
    <div class="chart-group-container">
        <div class="chart-group-data">
            <?php
            if(!empty($this->item)) {
                $this->render('market/data');
            }
            ?>
        </div>

        <div class="chart-group-chart row"></div>
    </div>
</div>

<?php
if($this->showModal) {
    if ($this->editable) {
        $this->render('chart/edit');
        $this->render('market/edit');
    }
}
?>
