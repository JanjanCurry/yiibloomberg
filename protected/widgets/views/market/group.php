<div class="chart-group"
     data-chart-type="<?php echo $this->market; ?>"
     data-chart-id="<?php echo $this->chartId; ?>"
     data-editable="<?php echo $this->editable; ?>"
     data-height="<?php echo $this->height; ?>"
     data-show-legend="<?php echo $this->showLegend; ?>">
    <div class="chart-group-container">
        <?php if($this->showTable){ ?>
            <div class="chart-group-header">
                <div class="row">
                    <div class="col-xs-10">
                        <?php $this->render('chart/period', array('period' => $this->period, 'showPeriod' => false)); ?>
                    </div>
                    <div class="col-xs-2 text-right">
                        <div class="btn-group" role="group">
                            <button type="button" class="btn btn-trans btn-sm dropdown-toggle" data-toggle="dropdown" aria-label="Group Menu"><span class="fa fa-plus"></span></button>
                            <ul class="dropdown-menu pull-right chart-btns">
                                <li><a class="dropdown-item chart-btn-update-id" href="#" data-toggle="modal" data-target="#chartMarketModal"><span class="fa fa-plus"></span> Add Asset</a></li>
                                <li class="divider"></li>
                                <li><a class="dropdown-item chart-group-change-date" href="#"><span class="fa fa-calendar"></span> Change Date Range</a></li>
                                <li class="divider"></li>
                                <li><a class="dropdown-item chart-group-png" href="#"><span class="fa fa-file-image-o"></span> Download Image of All</a></li>
                                <?php if (Yii::app()->user->checkToolAccess($this->getTool(),'download-csv') && !Yii::app()->user->isPromo($this->getTool())) { ?>
                                    <li><a class="dropdown-item chart-group-csv" href="#"><span class="fa fa-file-excel-o"></span> Download CSV of All</a></li>
                                    <li><a class="dropdown-item chart-group-pdf" href="#"><span class="fa fa-file-pdf-o"></span> Download PDF of All</a></li>
                                <?php }else{ ?>
                                    <li><a class="dropdown-item" href="<?php echo Yii::app()->params['url-pricing']; ?>" data-toggle="tooltip" title="Pro Account Only" data-placement="left" target="_blank"><span class="fa fa-file-excel-o"></span> Download CSV of All</a></li>
                                    <li><a class="dropdown-item" href="<?php echo Yii::app()->params['url-pricing']; ?>" data-toggle="tooltip" title="Pro Account Only" data-placement="left" target="_blank"><span class="fa fa-file-pdf-o"></span> Download PDF of All</a></li>
                                <?php } ?>
                                <li class="divider"></li>
                                <li><a class="dropdown-item chart-group-reset" href="#"><span class="fa fa-refresh"></span> Reset All</a></li>
                                <li><a class="dropdown-item chart-group-remove" href="#"><span class="fa fa-trash"></span> Remove All</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        <?php } ?>

        <div class="chart-group-data">
            <?php
            if($this->showEmpty) {
                if(!empty($this->item)) {
                    $this->render('market/data');
                }
            }else{
                $relation = 'favorites'.ucfirst($this->market);
                if(!empty($this->user) && !empty($this->user->$relation)){
                    foreach($this->user->$relation as $favorite){
                        $data = $favorite->data;
                        $data['view'] = 'data';
                        $data['startTime'] = $this->startTime;
                        $data['endTime'] = $this->endTime;
                        $data['period'] = $this->period;
                        $data['editable'] = $this->editable;
                        $data['col'] = $this->col;
                        $chart = $this->widget('application.widgets.MarketWidget', $data);
                        echo $chart->results['data'];
                    }
                    $this->showEmpty = true;
                }
            }
            ?>
        </div>

        <?php
        if($this->showTable){
            echo '<div class="chart-group-table"></div>';
        }
        if($this->showChart){
            echo '<div class="chart-group-chart row"></div>';
        }
        ?>


        <div class="row text-center margin-top-20 chart-group-empty <?php echo ($this->showEmpty ? 'hidden' : '') ?>">
            <div class="col-sm-6 col-sm-push-3">
                <h4>Adding an asset</h4>
                <p>To display asset data, click the Add Asset button. Then in the pop-up, select an asset from the list.</p>
                <a class="btn btn-primary jumbotron" href="#" data-toggle="modal" data-target="#chartMarketModal"><span class="fa fa-plus"></span> Add Asset</a>
            </div>
        </div>
    </div>
</div>

<?php
if($this->showModal) {
    $this->render('chart/share');
    if ($this->editable) {
        $this->render('chart/edit');
        $this->render('market/edit');
    }
}
?>
