<div class="well dash-group dash-group-yoy">
    <h3 class="mt-0">3 Month Forecast vs Last Year <span class="badge badge-primary">BETA</span></h3>
    <?php
    if (!empty($data)) {
        foreach ($data as $key => $chart) {
            if (!empty($chart['widget']->results)) {
                echo '<div class="dash-spark-item item">';
                echo $chart['widget']->results;
                echo '</div>';
            }
        }
    }
    ?>
</div>