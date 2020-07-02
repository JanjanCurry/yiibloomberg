<div class="chart-item-legend text-center" id="chart-legend-<?php echo $this->chartId; ?>">
    <ul class="list-inline">
        <?php
        if (!empty($this->chartData['cols'])) {
            foreach ($this->chartData['cols'] as $i => $col) {
                if ($i > 0 && empty($col['role'])) {
                    echo '<li class="chart-legend-item active" data-col="' . $i . '"><span class="fa fa-minus" style="color: #' . $col['color'] . '"></span> ' . $col['label'] . '</li>';
                }
            }
        }
        ?>
    </ul>
    <?php if (!empty($forecast)) { ?>
    <div class="chart-item-sublegend">
        <ul class="list-inline">
            <li><span class='fa fa-minus'></span> Historical</li>
            <li><span class='fa fa-ellipsis-h'></span> Forecast</li>
        </ul>
    </div>
    <?php } ?>
</div>
