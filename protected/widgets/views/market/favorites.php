<div class="row chart-list-container">
    <div class="chart-list" data-watch-type="country">
        <?php
        $relation = 'favorites'.ucfirst($this->market);
        if (!empty($this->user) && !empty($this->user->$relation)) {
            foreach ($this->user->$relation as $i => $favorite) {
                $data = $favorite->data;
                $data['view'] = 'group';
                $data['init'] = $this->init;
                $data['showTable'] = false;
                $data['showModal'] = false;
                $data['showLegend'] = false;
                $data['showEmpty'] = true;
                $data['editable'] = false;
                $data['requireEditing'] = false;

                $chart = $this->widget('application.widgets.MarketWidget', $data, true);
                if(!empty($chart)) {
                    echo '<div class="col-sm-6 user-favorite">';
                    echo $chart;
                    echo '</div>';
                }
            }
        }
        ?>

        <?php if (Yii::app()->user->checkToolAccess($this->getTool(),'favorites')) { ?>
            <div class="col-sm-6 chart-group add-fav <?php echo $this->market; ?>-add-fav">
                <div class="chart-group-container">
                    <div class="chart-item-header">
                        <a class="h4 chart-item-title watchlist-search-btn" href="#" data-toggle="modal" data-target="#chartMarketModal"><span class="fa fa-plus"></span> Add Macro Favorite</a>
                    </div>
                </div>
            </div>
        <?php } ?>
    </div>
</div>

<?php
$this->render('chart/share');
$this->render('market/edit');
?>