<?php
if(empty($item) && !empty($this->item)){
    $item = array();
    foreach($this->item as $val){
        $item[] = $val->code;
    }
    $item = implode(',',$item);
}

if(empty($compare) && !empty($this->compare)){
    $compare = array();
    foreach($this->compare as $val){
        $compare[] = $val->code;
    }
    $compare = implode(',',$compare);
}

?>

<div class="chart-item-data"
    <?php echo (!empty($this->chartId)) ? ' data-chart-id="'.$this->chartId.'"' : ''; ?>
    <?php echo (!empty($this->chartType)) ? ' data-chart-type="'.$this->chartType.'"' : ''; ?>

    <?php echo (!empty($item)) ? ' data-item="'.$item.'"' : ''; ?>
    <?php echo (!empty($compare)) ? ' data-compare="'.$compare.'"' : ''; ?>
    <?php echo(!empty($this->market) ? ' data-market="' . $this->market . '"' : ''); ?>
    <?php echo(!empty($this->marketCompare) ? ' data-market-compare="' . $this->marketCompare . '"' : ''); ?>

    <?php echo (!empty($this->startTime)) ? ' data-start-time="'.date('M Y',$this->startTime).'"' : ''; ?>
    <?php echo (!empty($this->endTime)) ? ' data-end-time="'.date('M Y',$this->endTime).'"' : ''; ?>
    <?php echo(!empty($this->period) ? ' data-period="' . $this->period . '"' : ''); ?>

    <?php echo (!empty($this->editable)) ? ' data-editable="'.$this->editable.'"' : ''; ?>
    <?php echo (!empty($this->requireEditing)) ? ' data-require-editing="'.$this->requireEditing.'"' : ''; ?>
    <?php echo (!empty($this->session)) ? ' data-session="'.$this->session.'"' : ''; ?>
    <?php echo (!empty($this->report)) ? ' data-report="'.$this->report.'"' : ''; ?>
    <?php echo (!empty($this->ignorePermissions)) ? ' data-ignore-permissions="'.$this->ignorePermissions.'"' : ''; ?>
></div>