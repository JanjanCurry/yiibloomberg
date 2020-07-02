<div class="well dash-group dash-group-performance">
    <h3 class="mt-0">Performance <span class="badge badge-primary">BETA</span></h3>
    <div class="row">
        <?php
        if (!empty($data)) {
            if (!empty($data['fav'])) {
                echo '<div class="col-sm-4"><h4>Selected Assets</h4>';
                foreach ($data['fav'] as $key => $chart) {
                    if (!empty($chart['widget']->results)) {
                        echo '<div class="dash-spark-item item">';
                        echo $chart['widget']->results;
                        echo '</div>';
                    }
                }
                echo '</div>';
            }
            if (!empty($data['top'])) {
                echo '<div class="col-sm-4"><h4>Top Assets</h4>';
                foreach ($data['top'] as $key => $chart) {
                    if (!empty($chart['widget']->results)) {
                        echo '<div class="dash-spark-item item">';
                        echo $chart['widget']->results;
                        echo '</div>';
                    }
                }
                echo '</div>';
            }
            if (!empty($data['bottom'])) {
                echo '<div class="col-sm-4"><h4>Worst Assets</h4>';
                foreach ($data['bottom'] as $key => $chart) {
                    if (!empty($chart['widget']->results)) {
                        echo '<div class="dash-spark-item item">';
                        echo $chart['widget']->results;
                        echo '</div>';
                    }
                }
                echo '</div>';
            }
        }
        ?>
    </div>
</div>