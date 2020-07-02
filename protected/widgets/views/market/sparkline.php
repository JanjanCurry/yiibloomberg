<div class="chart-spark">
    <div class="chart-spark-item text-center">
        <?php
        if($this->editable){
            echo '<a href="#" class="spark-edit-btn" data-toggle="tooltip" title="Change Asset"><i class="fa fa-pencil"></i></a>';
        }

        if(strlen($label) > 30){
            echo '<span class="spark-name"><a href="'.$url.'" data-toggle="tooltip" title="'.$label.'">'.substr($label,0,30).'	&hellip;</a></span>';
        }else{
          echo '<span class="spark-name"><a href="'.$url.'">'.$label.'</a></span>';
        }
        ?>
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
</div>