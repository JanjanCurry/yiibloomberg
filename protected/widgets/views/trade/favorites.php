<div class="row chart-list-container">
    <div class="chart-list" data-watch-type="country">
        <?php
        if(!empty($this->user) && !empty($this->user->favoritesTrade)) {
            foreach ($this->user->favoritesTrade as $i => $favorite) {
                $data = $favorite->data;
                $data['view'] = 'group';
                $data['init'] = $this->init;
                $data['showTable'] = $this->showTable;
                $data['showModal'] = false;
                $data['showLegend'] = false;
                $data['showEmpty'] = true;

                echo '<div class="col-sm-6 user-favorite">';
                $chart = $this->widget('application.widgets.TradeWidget', $data);
                echo '</div>';
            }
        }
        ?>

        <?php if(Yii::app()->user->checkToolAccess('tra','favorites')){ ?>
        <div class="col-sm-6 chart-group add-fav trade-add-fav">
            <div class="chart-group-container">
                <div class="chart-item-header">
                    <a class="h4 chart-item-title watchlist-search-btn" href="#"  data-toggle="modal" data-target="#chartTradeModal"><span class="fa fa-plus"></span> Add Trade Favorite</a>
                </div>
            </div>
        </div>
        <?php } ?>
    </div>
</div>

<?php
$this->render('chart/share');
$this->render('trade/edit');
?>