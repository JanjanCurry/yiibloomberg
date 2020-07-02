<div class="well chart-spark">
    <div class="chart-spark-item text-center">
        <span class="spark-name"><a href="<?php echo $url; ?>"><?php echo $label; ?></a></span>
        <span class="spark-name-group"><?php echo $group; ?></span>
        <div class="row">
            <?php
            if (!empty($values)) {
                foreach ($values as $key => $val) {
                    $icon = 'fa-caret-up';
                    $color = 'success';
                    if ($val < 0) {
                        $icon = 'fa-caret-down';
                        $color = 'danger';
                    }

                    echo '<div class="col-xs-12">';
                    echo '<span class="spark-icon text-'.$color.'"><i class="fa ' . $icon . '"></i></span>';
                    echo '<span class="spark-value">' . number_format(round(abs($val), 2), 2) . '%</span>';
                    //echo '<span class="spark-date">' . $key . '</span>';
                    echo '</div>';
                }
            }
            ?>
        </div>
    </div>

    <?php
    /*if($value < 0){
        echo '<div class="chart-spark-item chart-spark-danger text-center">';
        echo '<span class="spark-name">'.$label.'</span>';
        echo '<span class="spark-icon"><i class="fa fa-caret-down"></i></span>';
        echo '<span class="spark-value">'.round($value, 2).'%</span>';

        echo '</div>';
    }else{
        echo '<div class="chart-spark-item chart-spark-success text-center">';
        echo '<span class="spark-name">'.$label.'</span>';
        echo '<span class="spark-icon"><i class="fa fa-caret-up"></i></span>';
        echo '<span class="spark-value">'.round($value, 2).'%</span>';
        echo '</div>';
    }*/
    ?>
</div>