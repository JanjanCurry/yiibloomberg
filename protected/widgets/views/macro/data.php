<?php
$chartId = (!empty($chartId) ? $chartId : $this->chartId);
$chartType = (!empty($chartType) ? $chartType : $this->chartType);

$period = (!empty($period) ? $period : $this->period);
$variant = (!empty($variant) ? $variant : $this->variant);
$macro = (!empty($macro) ? $macro : $this->macro);

if(empty($reporter) && !empty($this->reporter)){
    $reporter = array();
    foreach($this->reporter as $val){
        $reporter[] = $val->ccode3;
    }
    $reporter = implode(',',$reporter);
}

$startTime = (!empty($startTime) ? $startTime : $this->startTime);
$endTime = (!empty($endTime) ? $endTime : $this->endTime);

if(empty($compare) && !empty($this->compare)){
    $compare = $this->compare;
    if(is_array($compare)) {
        $compare = implode(',', $compare);
    }
}

$editable = (isset($editable) ? $editable : $this->editable);
$requireEditing = (isset($requireEditing) ? $requireEditing : $this->requireEditing);

?>

<div class="chart-item-data"
    <?php echo (!empty($chartId)) ? ' data-chart-id="'.$chartId.'"' : ''; ?>
    <?php echo (!empty($chartType)) ? ' data-chart-type="'.$chartType.'"' : ''; ?>

    <?php echo (!empty($period)) ? ' data-period="'.$period.'"' : ''; ?>
    <?php echo (!empty($variant)) ? ' data-variant="'.$variant.'"' : ''; ?>
    <?php echo (!empty($macro)) ? ' data-macro="'.$macro.'"' : ''; ?>
    <?php echo (!empty($reporter)) ? ' data-reporter="'.$reporter.'"' : ''; ?>

    <?php echo (!empty($compare)) ? ' data-compare="'.$compare.'"' : ''; ?>

    <?php echo (!empty($startTime)) ? ' data-start-time="'.date('M Y',$startTime).'"' : ''; ?>
    <?php echo (!empty($endTime)) ? ' data-end-time="'.date('M Y',$endTime).'"' : ''; ?>

    <?php echo (!empty($editable)) ? ' data-editable="'.$editable.'"' : ''; ?>
    <?php echo (!empty($requireEditing)) ? ' data-require-editing="'.$requireEditing.'"' : ''; ?>
    <?php echo (!empty($this->session)) ? ' data-session="'.$this->session.'"' : ''; ?>
    <?php echo (!empty($this->report)) ? ' data-report="'.$this->report.'"' : ''; ?>
    <?php echo (!empty($this->ignorePermissions)) ? ' data-ignore-permissions="'.$this->ignorePermissions.'"' : ''; ?>
></div>