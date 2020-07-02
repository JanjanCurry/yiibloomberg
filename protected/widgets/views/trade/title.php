<?php
$compare = (isset($compare) ? $compare : true);

$isFavorite = false;
$favoriteAction = 'Add';
if ($this->isFavorite()) {
    $isFavorite = true;
    $favoriteAction = 'Remove';
}
$accessFavorites = false;
if (Yii::app()->user->checkToolAccess('tra','favorites')) {
    $accessFavorites = true;
}
?>

<div class="chart-item-header">
    <div class="row">
        <div class="col-xs-10">
            <?php
            $favoriteIcon = '';
            if ($isFavorite) {
                $favoriteIcon = '<span class="fa fa-star"></span> ';
            }

            if (strlen($title) > 28) {
                //$tooltip = ' data-toggle="tooltip" data-placement="bottom" title="' . $title . '"';
                $title = trim($title);
                $sortTitle = substr($title, 0, 28);
                $sortTitle .= '<span class="hidden-xs">' . substr($title, 28, 20) . '</span>';
                $sortTitle .= '<span class="hidden-sm hidden-xs">' . substr($title, 48, 20) . '</span>';
                $sortTitle .= '<span class="hidden-md hidden-sm hidden-xs">' . substr($title, 68, 20) . '</span>';
                $sortTitle .= (strlen($title) > 88 ? '&hellip;' : '');

                //var_dump($titleXs,$titleSm,$titleMd, $titleLg);
//                echo CHtml::link($favoriteIcon . $sortTitle, $this->url, array(
//                    'class' => 'chart-item-title h4',
//                    'data-toggle' => 'tooltip',
//                    'data-placement' => 'bottom',
//                    'title' => $title,
//                ));
                echo '<span class="chart-item-title h4" data-toggle="tooltip" data-placement="bottom" title="'.$title.'">'.$favoriteIcon . $sortTitle.'</span>';
            } else {
//                echo CHtml::link($favoriteIcon . $title, $this->url, array(
//                    'class' => 'chart-item-title h4',
//                    'title' => $title,
//                ));
                echo '<span class="chart-item-title h4">'.$favoriteIcon . $title.'</span>';
            }
            ?>
        </div>
        <div class="col-xs-2 text-right">
            <div class="btn-group" role="group">
                <button type="button" class="btn btn-trans btn-sm dropdown-toggle" data-toggle="dropdown" aria-label="Report Menu"><span class="fa fa-bars"></span></button>
                <ul class="dropdown-menu pull-right chart-btns">
                    <?php if ($this->editable) { ?>
                        <?php if ($compare) { ?>
                            <li><a class="dropdown-item chart-btn-update-id" href="#" data-toggle="modal" data-target="#chartCompareModal"><span class="fa fa-pencil"></span> Compare Country</a></li>
                        <?php } ?>
                        <li><a class="dropdown-item chart-btn-update-id chart-edit-btn" href="#" data-toggle="modal" data-target="#chartTradeModal"><span class="fa fa-pencil"></span> Edit Indicator</a></li>
                        <?php if (strpos($this->indicator, 'top10') === false) { ?>
                            <li><a class="dropdown-item chart-btn-update-id" href="#" data-toggle="modal" data-target="#chartTypeModal"><span class="fa fa-bar-chart"></span> Chart Type</a></li>
                        <?php } ?>
                        <li class="divider"></li>

                        <li><a class="dropdown-item chart-btn-update-id" href="#" data-toggle="modal" data-target="#chartTimeModal"><span class="fa fa-calendar"></span> Change Date</a></li>
                        <li><a class="dropdown-item chart-btn-reset" href="#"><span class="fa fa-refresh"></span> Reset</a></li>
                        <li><a class="dropdown-item chart-btn-remove" href="#"><span class="fa fa-trash"></span> Remove</a></li>
                        <li class="divider"></li>
                    <?php } ?>
                    <li><a class="dropdown-item download-btn" href="#" target="_blank"><span class="fa fa-file-image-o"></span> Download Image</a></li>
                    <?php if (Yii::app()->user->checkToolAccess('tra','download-csv') && !Yii::app()->user->isPromo('tra')) { ?>
                        <li><a class="dropdown-item csv-btn" href="#" target="_blank"><span class="fa fa-file-excel-o"></span> Download CSV</a></li>
                        <li><a class="dropdown-item pdf-btn" href="#" target="_blank"><span class="fa fa-file-pdf-o"></span> Download PDF</a></li>
                    <?php } else { ?>
                        <li><a class="dropdown-item" href="<?php echo Yii::app()->params['url-pricing']; ?>" data-toggle="tooltip" title="Pro Account Only" data-placement="left" target="_blank"><span class="fa fa-file-excel-o"></span> Download CSV</a></li>
                        <li><a class="dropdown-item" href="<?php echo Yii::app()->params['url-pricing']; ?>" data-toggle="tooltip" title="Pro Account Only" data-placement="left" target="_blank"><span class="fa fa-file-pdf-o"></span> Download PDF</a></li>
                    <?php } ?>
                    <li><a class="dropdown-item ci-share-btn" href="#"><span class="fa fa-share-alt"></span> Share</a></li>
                    <?php
                    if ($accessFavorites) {
                        echo '<li><a class="dropdown-item toggle-favorite" href="#" data-type="trade"><span class="fa fa-star"></span> ' . $favoriteAction . ' Favorite</a></li>';

                        //echo '<li><a class="dropdown-item watchlist-remove-btn" href="#" data-w-type="country" data-w-id="' . $this->reporter->SLNO . '"><span class="fa fa-star"></span> ' . $favoriteAction . ' Favorite</a></li>';
                    } else {
                        echo '<li><a class="dropdown-item" href="' . Yii::app()->params['url-pricing'] . '" data-toggle="tooltip" title="Pro Account Only" data-placement="left" target="_blank"><span class="fa fa-star"></span> ' . $favoriteAction . ' Favorite</a></li>';
                    }
                    ?>
                </ul>
            </div>
        </div>
    </div>
</div>
<?php $this->render('chart/legend'); ?>