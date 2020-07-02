<?php
if(!isset($showPeriod)){
    $showPeriod = true;
}
?>

<div class="btn-toolbar" role="toolbar" aria-label="Chart Date Setting">
    <div class="btn-group chart-period" role="group" data-period="<?php echo $period; ?>">
        <span class="<?php echo (!$showPeriod && $period != 'month' ? 'hidden' : '') ?>">
            <button type="button" class="btn btn-sm <?php echo($period == 'month' ? 'btn-primary' : 'btn-default'); ?>" data-period="month" aria-label="Monthly">
                <span class="hidden-xs">Monthly</span>
                <span class="visible-xs">Mth</span>
            </button>
        </span>
        <span class="<?php echo (!$showPeriod && $period != 'quarter' ? 'hidden' : '') ?>">
            <button type="button" class="btn btn-sm <?php echo($period == 'quarter' ? 'btn-primary' : 'btn-default'); ?>" data-period="quarter" aria-label="Quarterly">
                <span class="hidden-xs">Quarterly</span>
                <span class="visible-xs">Qtr</span>
            </button>
        </span>
        <span class="<?php echo (!$showPeriod && $period != 'annual' ? 'hidden' : '') ?>">
            <button type="button" class="btn btn-sm <?php echo($period == 'annual' ? 'btn-primary' : 'btn-default'); ?>" data-period="annual" aria-label="Annual">
                <span class="hidden-xs">Annual</span>
                <span class="visible-xs">Ann</span>
            </button>
        </span>
    </div>

    <div class="btn-group chart-time" role="group" data-type="<?php echo $this->type; ?>">
        <a href="#" class="btn btn-sm btn-default chart-group-change-date"  data-toggle="modal" data-target="#chartTimeModal" aria-label="Change Date" data-start="<?php echo $this->formatDate($this->startTime, 'month'); ?>" data-end="<?php echo $this->formatDate($this->endTime, 'month'); ?>">
            <i class="fa fa-calendar"></i>
            <span class="month hidden-xxs <?php echo($period == 'month' ? '' : 'hidden'); ?>"><?php echo $this->formatDate($this->startTime, 'month') . ' - ' . $this->formatDate($this->endTime, 'month'); ?></span>
            <span class="quarter hidden-xxs <?php echo($period == 'quarter' ? '' : 'hidden'); ?>"><?php echo $this->formatDate($this->startTime, 'quarter') . ' - ' . $this->formatDate($this->endTime, 'quarter'); ?></span>
            <span class="annual hidden-xxs <?php echo($period == 'annual' ? '' : 'hidden'); ?>"><?php echo $this->formatDate($this->startTime, 'annual') . ' - ' . $this->formatDate($this->endTime, 'annual'); ?></span>
        </a>
    </div>
</div>