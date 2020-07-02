<div class="row chart-list-container">
    <div class="chart-list chart-list-favorites">
        <?php
        if (!empty($this->user) && !empty($this->user->favoritesMacro)) {
            foreach ($this->user->favoritesMacro as $i => $favorite) {
                $data = $favorite->data;
                $data['view'] = 'group';
                $data['init'] = $this->init;
                $data['showTable'] = false;
                $data['showModal'] = false;
                $data['showLegend'] = false;
                $data['showEmpty'] = true;
                $data['editable'] = false;
                $data['requireEditing'] = false;

                $chart = $this->widget('application.widgets.MacroWidget', $data, true);
                if(!empty($chart)) {
                    echo '<div class="col-sm-6 user-favorite">';
                    echo $chart;
                    echo '</div>';
                }
            }
        }
        ?>

        <?php if (Yii::app()->user->checkToolAccess('tra','favorites')) { ?>
            <div class="col-sm-6 chart-group add-fav macro-add-fav">
                <div class="chart-group-container">
                    <div class="chart-item-header">
                        <a class="h4 chart-item-title favorite-search-btn" href="#" data-toggle="modal" data-target="#chartMacroModal"><span class="fa fa-plus"></span> Add Macro Favorite</a>
                    </div>
                </div>
            </div>
        <?php } ?>
    </div>
</div>

<?php
$this->render('chart/share');
$this->render('macro/edit');
?>