<div class="chart-group"
     data-chart-type="<?php echo $this->market; ?>"
     data-chart-id="<?php echo $this->chartId; ?>">
    <div class="chart-group-container">
        <div class="chart-group-data">
            <?php
            if(!empty($this->item)) {
                $this->render('market/data');
            }
            ?>
        </div>

        <h4 class="chart-title h4"><?php echo $this->market.' % Change Outlook'; ?></h4>

        <div class="row">
            <div class="col-sm-5 col-md-4 col-lg-3">
                <?php
                if(!empty($this->item)) {
                    $items = $this->item;
                    if(!is_array($this->item)){
                        $items = array($this->item);
                    }
                    foreach($items as $i => $item){
                        echo '<div class="item">';
                        echo '<h4><a href="#" class="dash-outlook-item-btn" data-ref="'.$item->code.'"><span class="dash-outlook-item-label">'.$item->name.'</span><i class="fa fa-pencil"></i></a></h4>';
                        echo '</div>';
                    }
                    if($i < 4) {
                        for ($j = $i; $j < 4; $j++) {
                            echo '<div class="item">';
                            echo '<h4><a href="#" class="dash-outlook-item-btn" data-ref=""><span class="dash-outlook-item-label">Not Selected</span><i class="fa fa-pencil"></i></a></h4>';
                            echo '</div>';
                        }
                    }
                }
                ?>
            </div>
            <div class="col-sm-7 col-md-8 col-lg-9">
                <div class="chart-group-chart row"></div>
            </div>
        </div>
    </div>
</div>

<?php
if($this->showModal) {
    if ($this->editable) {
        $this->render('chart/edit');
        $this->render('market/edit');
    }
}
?>
