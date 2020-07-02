<?php


$reporter = (!empty($this->reporter) ? $this->reporter->ccode3 : '');

$partner = $sector = $compare = '';

if (!empty($this->partner)) {
    $partner = array();
    foreach ($this->partner as $val) {
        $partner[] = $val->ccode3;
    }
    $partner = implode(',', $partner);
}

if (!empty($this->sector)) {
    $sector = array();
    foreach ($this->sector as $val) {
        $sector[] = $val->code;
    }
    $sector = implode(',', $sector);
}

if (!empty($this->compare)) {
    $compare = array();
    foreach ($this->compare as $val) {
        $compare[] = $val->ccode3;
    }
    $compare = implode(',', $compare);
}

if (!empty($this->indicator)) {
    if (!empty($partner) && strpos($this->indicator, 'partner') === false) {
        $partner = null;
    }
    if (!empty($sector) && strpos($this->indicator, 'sector') === false) {
        $sector = null;
    }
    if (strpos($this->indicator, 'none') !== false) {
        $partner = null;
        $sector = null;
    }
}
?>

<div class="chart-item-data"
    <?php echo(!empty($this->chartId) ? ' data-chart-id="' . $this->chartId . '"' : ''); ?>
    <?php echo(!empty($this->chartType) ? ' data-chart-type="' . $this->chartType . '"' : ''); ?>
    <?php echo(!empty($this->col) ? ' data-col="' . $this->col . '"' : ''); ?>

    <?php echo(!empty($this->startTime) ? ' data-start-time="' . date('M Y', $this->startTime) . '"' : ''); ?>
    <?php echo(!empty($this->endTime) ? ' data-end-time="' . date('M Y', $this->endTime) . '"' : ''); ?>
    <?php echo(!empty($this->period) ? ' data-period="' . $this->period . '"' : ''); ?>

    <?php echo(!empty($this->reportType) ? ' data-report-type="' . $this->reportType . '"' : ''); ?>
    <?php echo(!empty($this->reportValue) ? ' data-report-value="' . $this->reportValue . '"' : ''); ?>
    <?php echo(!empty($this->indicator) ? ' data-indicator="' . $this->indicator . '"' : ''); ?>

    <?php echo(!empty($reporter) ? ' data-reporter="' . $reporter . '"' : ''); ?>
    <?php echo(!empty($partner) ? ' data-partner="' . $partner . '"' : ''); ?>
    <?php echo(!empty($sector) ? ' data-sector="' . $sector . '"' : ''); ?>
    <?php echo(!empty($compare) ? ' data-compare="' . $compare . '"' : ''); ?>

    <?php echo(!empty($this->editable) ? ' data-editable="' . $this->editable . '"' : ''); ?>
    <?php echo(!empty($this->requireEditing) ? ' data-require-editing="' . $this->requireEditing . '"' : ''); ?>
    <?php echo (!empty($this->session)) ? ' data-session="'.$this->session.'"' : ''; ?>
    <?php echo (!empty($this->session)) ? ' data-report="'.$this->report.'"' : ''; ?>
    <?php echo (!empty($this->ignorePermissions)) ? ' data-ignore-permissions="'.$this->ignorePermissions.'"' : ''; ?>

></div>