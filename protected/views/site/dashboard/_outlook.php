<div class="well dash-group dash-group-outlook">
    <h3 class="mt-0">Category Outlook <span class="badge badge-primary">BETA</span></h3>
    <div class="dash-markets">
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
</div>