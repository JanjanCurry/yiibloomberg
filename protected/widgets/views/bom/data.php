<div class="chart-item-data"
    <?php echo (!empty($this->chartId)) ? ' data-chart-id="'.$this->chartId.'"' : ''; ?>
    <?php echo (!empty($this->chartType)) ? ' data-chart-type="'.$this->chartType.'"' : ''; ?>

    <?php echo (!empty($this->startTime)) ? ' data-start-time="'.date('M Y',$this->startTime).'"' : ''; ?>
    <?php echo (!empty($this->endTime)) ? ' data-end-time="'.date('M Y',$this->endTime).'"' : ''; ?>
    <?php echo(!empty($this->period) ? ' data-period="' . $this->period . '"' : ''); ?>
></div>