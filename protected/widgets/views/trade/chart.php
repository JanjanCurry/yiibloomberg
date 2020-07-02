<?php
$id = (!empty($id) ? $id : $this->chartId);
$compare = (isset($compare) ? $compare : false);
$share = (isset($share) ? $share : true);
$id = (!empty($id) ? $id : $this->chartId);
$col = (!empty($col) ? $col : 'col-sm-6');
?>

<div
    class="<?php echo $col; ?> chart-item"
    <?php echo (!empty($id)) ? 'data-id="'.$id.'"' : ''; ?>
    <?php echo (!empty($type)) ? 'data-type="'.$type.'"' : ''; ?>
    <?php echo (!empty($ref)) ? 'data-ref="'.$ref.'"' : ''; ?>
    <?php echo (!empty($indicator)) ? 'data-indicator="'.implode(',',$indicator).'"' : ''; ?>
    <?php echo (!empty($watchlist)) ? 'data-watch="'.$watchlist->id.'"' : ''; ?>
    <?php echo (!empty($showLegend)) ? 'data-show-legend="'.$showLegend.'"' : ''; ?>
>
    <div class="chart-item-container">
        <div class="chart-item-header">
            <div class="row">
                <div class="col-xs-8">
                    <?php echo (!empty($url) ? '<a class="h4 chart-item-title" href="'.$url.'">'.$title.'</a>' : '<h4 class="chart-item-title">'.$title.'</h4>'); ?>
                </div>
                <div class="col-xs-4 text-right">
                    <?php if($compare){ ?>
                        <div class="btn-group" role="group">
                            <button type="button" class="btn btn-trans btn-sm dropdown-toggle" data-toggle="dropdown" aria-label="Group Menu"><span class="fa fa-plus"></span></button>
                            <ul class="dropdown-menu pull-right chart-btns" data-chart="<?php echo $id; ?>">
                                <li><a class="dropdown-item chart-btn-update-id" href="#" data-toggle="modal" data-target="#chartCompareModal"><span class="fa fa-plus"></span> Compare Country</a></li>
                                <li><a class="dropdown-item chart-btn-update-id" href="#" data-toggle="modal" data-target="#chartTradeModal"><span class="fa fa-plus"></span> Add Indicator</a></li>
                                <li><a class="dropdown-item chart-btn-update-id" href="#" data-toggle="modal" data-target="#chartTimeModal"><span class="fa fa-calendar"></span> Change Date</a></li>
                                <li><a class="dropdown-item chart-btn-reset" href="#"><span class="fa fa-refresh"></span> Reset</a></li>
                            </ul>
                        </div>
                    <?php } ?>
                    <?php if($share){ ?>
                        <div class="btn-group" role="group">
                            <button type="button" class="btn btn-trans btn-sm dropdown-toggle" data-toggle="dropdown" aria-label="Report Menu"><span class="fa fa-bars"></span></button>
                            <ul class="dropdown-menu pull-right chart-btns" data-chart="<?php echo $id; ?>">
                                <li><a class="dropdown-item download-btn" href="#" target="_blank"><span class="fa fa-file-image-o"></span> Download PNG</a></li>
                                <li><a class="dropdown-item csv-btn" href="#" target="_blank"><span class="fa fa-file-excel-o"></span> Download CSV</a></li>
                                <li><a class="dropdown-item ci-share-btn" href="#"><span class="fa fa-share-alt"></span> Share</a></li>
                                <?php
                                if(!empty($watchlist)) {
                                    if (Yii::app()->user->checkToolAccess('tra','favorites')) {
                                        echo '<li><a class="dropdown-item watchlist-remove-btn" href="#" data-w-type="' . $watchlist->type . '" data-w-id="' . $watchlist->refId . '"><span class="fa fa-star"></span> Remove Favorite</a></li>';
                                    } else {
                                        echo '<li><a class="dropdown-item" href="#" data-toggle="tooltip" title="Pro Account Only"  data-placement="left"><span class="fa fa-star"></span> Remove Favorite</a></li>';
                                    }
                                }
                                ?>
                            </ul>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>

        <?php if($compare){ ?>
            <div class="chart-item-table"></div>
        <?php } ?>

        <div class="chart-item-chart" id="<?php echo $id; ?>"></div>

        <?php if($this->init){ ?>
            <div class="chart-item-indicators row"></div>
        <?php } ?>
    </div>
</div>

