<div class="chart-item-header">
    <div class="row">
        <div class="col-xs-10">
            <?php
            if (strlen($title) > 28) {
                //$tooltip = ' data-toggle="tooltip" data-placement="bottom" title="' . $title . '"';
                $title = trim($title);
                $sortTitle = substr($title, 0, 28);
                $sortTitle .= '<span class="hidden-xs">'.substr($title, 28, 20).'</span>';
                $sortTitle .= '<span class="hidden-sm hidden-xs">'.substr($title, 48, 20).'</span>';
                $sortTitle .= '<span class="hidden-md hidden-sm hidden-xs">'.substr($title, 68, 20).'</span>';
                $sortTitle .= (strlen($title) > 88 ? '&hellip;' : '');

                //var_dump($titleXs,$titleSm,$titleMd, $titleLg);
                echo CHtml::link($sortTitle, $this->url, array(
                    'class' => 'chart-item-title h4',
                    'data-toggle' => 'tooltip',
                    'data-placement' => 'bottom',
                    'title' => $title,
                ));
            }else{
                echo CHtml::link($title, $this->url, array(
                    'class' => 'chart-item-title h4',
                    'title' => $title,
                ));
            }

            //echo '<h4 class="chart-item-title"' . $tooltip . '>' . $title . '</h4>';
            ?>
        </div>
        <div class="col-xs-2 text-right">
        </div>
    </div>
</div>

<?php $this->render('chart/legend', ['forecast' => false]); ?>