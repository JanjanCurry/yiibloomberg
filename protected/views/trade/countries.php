<?php
$companyId = [0];
if(!empty($this->user->companyId)){
    $companyId[] = $this->user->companyId;
}
$criteria = new CDbCriteria();
$criteria->compare('companyId', $companyId);
$criteria->order = 'name ASC';

$items = array(
    'commodity' => array(),
    'currency' => array(),
    'equity' => array(),
    'reporter' => array(),
);
$commodities = DbCommodities::model()->findAll($criteria);
if (!empty($commodities)) {
    $i = 0;
    $total = ceil(count($commodities) / 3);
    $letter = '';
    $newRow = false;
    foreach ($commodities as $commodity) {
        if ($i == $total || $i == ($total * 2)) {
            $newRow = true;
        }
        $i++;
        if ($letter != $commodity->name[0]) {
            if ($i > 1) {
                $items['commodity'][] = '</div>'; //close tag for .chart-search-list-subgroup
            }
            if ($newRow) {
                $newRow = false;
                $items['commodity'][] = '</div><div class="col-md-4 chart-search-list-column">';
            }
            $letter = $commodity->name[0];
            $items['commodity'][] = '<div class="chart-search-list-subgroup" data-group="' . strtoupper($letter) . '">';
            $items['commodity'][] = '<h3>' . strtoupper($letter) . '</h3>';
        }

        $premium = '<span class="badge">Pro</span>';
        if (Yii::app()->user->checkToolAccess('com', 'country-full')) {
            $premium = '<span class="badge badge-primary">Pro</span>';
        }
        if ($commodity->access == 1) {
            $premium = '<span class="badge badge-primary">Essentials</span>';
        }

        $items['commodity'][] = '<a href="#" class="list-group-item" data-ref="' . $commodity->code . '" data-label="' . $commodity->name . '">' . $commodity->name . $premium . '</a>';

    }
    $items['commodity'][] = '</div>'; //close tag for .chart-search-list-subgroup
}

$currencies = DbCurrencies::model()->findAll($criteria);
if (!empty($currencies)) {
    $i = 0;
    $total = ceil(count($currencies) / 3);
    $letter = '';
    $newRow = false;
    foreach ($currencies as $currency) {
        if ($i == $total || $i == ($total * 2)) {
            $newRow = true;
        }
        $i++;
        if ($letter != $currency->name[0]) {
            if ($i > 1) {
                $items['currency'][] = '</div>'; //close tag for .chart-search-list-subgroup
            }
            if ($newRow) {
                $newRow = false;
                $items['currency'][] = '</div><div class="col-md-4 chart-search-list-column">';
            }
            $letter = $currency->name[0];
            $items['currency'][] = '<div class="chart-search-list-subgroup" data-group="' . strtoupper($letter) . '">';
            $items['currency'][] = '<h3>' . strtoupper($letter) . '</h3>';
        }

        $premium = '<span class="badge">Pro</span>';
        if (Yii::app()->user->checkToolAccess('cur', 'country-full')) {
            $premium = '<span class="badge badge-primary">Pro</span>';
        }
        if ($currency->access == 1) {
            $premium = '<span class="badge badge-primary">Essentials</span>';
        }

        $items['currency'][] = '<a href="#" class="list-group-item" data-ref="' . $currency->code . '" data-label="' . $currency->name . '">' . $currency->name . $premium . '</a>';

    }
    $items['currency'][] = '</div>'; //close tag for .chart-search-list-subgroup
}

$equities = DbEquities::model()->findAll($criteria);
if (!empty($equities)) {
    $i = 0;
    $total = ceil(count($equities) / 3);
    $letter = '';
    $newRow = false;
    foreach ($equities as $equity) {
        if ($i == $total || $i == ($total * 2)) {
            $newRow = true;
        }
        $i++;
        if ($letter != $equity->name[0]) {
            if ($i > 1) {
                $items['equity'][] = '</div>'; //close tag for .chart-search-list-subgroup
            }
            if ($newRow) {
                $newRow = false;
                $items['equity'][] = '</div><div class="col-md-4 chart-search-list-column">';
            }
            $letter = $equity->name[0];
            $items['equity'][] = '<div class="chart-search-list-subgroup" data-group="' . strtoupper($letter) . '">';
            $items['equity'][] = '<h3>' . strtoupper($letter) . '</h3>';
        }

        $premium = '<span class="badge">Pro</span>';
        if (Yii::app()->user->checkToolAccess('equ', 'country-full')) {
            $premium = '<span class="badge badge-primary">Pro</span>';
        }
        if ($equity->access == 1) {
            $premium = '<span class="badge badge-primary">Essentials</span>';
        }

        $items['equity'][] = '<a href="#" class="list-group-item" data-ref="' . $equity->code . '" data-label="' . $equity->name . '">' . $equity->name . $premium . '</a>';

    }
    $items['equity'][] = '</div>'; //close tag for .chart-search-list-subgroup
}

$reporters = DbReporters::model()->findAll(array('order' => 'country ASC'));
if (!empty($reporters)) {
    $i = 0;
    $total = ceil(count($reporters) / 3);
    $letter = '';
    $newRow = false;
    foreach ($reporters as $reporter) {
        if ($i == $total || $i == ($total * 2)) {
            $newRow = true;
        }
        $i++;
        if ($letter != $reporter->country[0]) {
            if ($i > 1) {
                $items['reporter'][] = '</div>'; //close tag for .chart-search-list-subgroup
            }
            if ($newRow) {
                $newRow = false;
                $items['reporter'][] = '</div><div class="col-md-4 chart-search-list-column">';
            }
            $letter = $reporter->country[0];
            $items['reporter'][] = '<div class="chart-search-list-subgroup" data-group="' . strtoupper($letter) . '">';
            $items['reporter'][] = '<h3>' . strtoupper($letter) . '</h3>';
        }

        $premium = '';
        if (Yii::app()->user->checkToolAccess('tra', 'service-pro')) {
            $premium = '<span class="badge badge-primary">Pro</span>';
        }
        if ($reporter->access == 1) {
            if(Yii::app()->user->checkToolAccess('tra', 'service-ess')){
                $premium = '<span class="badge badge-primary">Essentials</span>';
            } else {
                $premium = '<span class="badge">Essentials</span>';
            }
        } elseif ($reporter->access == 2) {
            if (Yii::app()->user->checkToolAccess('tra', 'service-pro')) {
                $premium = '<span class="badge badge-primary">Pro</span>';
            } else {
                $premium = '<span class="badge">Pro</span>';
            }
        }

        $items['reporter'][] = '<a href="#" class="list-group-item" data-ref="' . $reporter->ccode3 . '" data-label="' . $reporter->ccode3 . ', ' . $reporter->country . '">' . $reporter->ccode3 . ': ' . $reporter->country . $premium . '</a>';
    }
    $items['reporter'][] = '</div>'; //close tag for .chart-search-list-subgroup
}
?>

<div class="content-header">
    <div class="container">
        <h3>List All Available Countries</h3>
    </div>
</div>

<div class="container">
    <div class="search-list-nav">
        <div class="row">
            <div class="col-sm-4">
                <a class="btn btn-primary btn-block" data-list-target="reporter">By Country</a>
            </div>
            <div class="col-sm-4">
                <a class="btn btn-primary btn-block" data-list-target="indicator">By Economic Indicator</a>
            </div>
            <div class="col-sm-4">
                <a class="btn btn-primary btn-block" data-list-target="markets">By Market</a>
            </div>
        </div>

        <div class="row padding-top-30 hidden" data-list="markets">
            <div class="col-sm-4">
                <a class="btn btn-primary btn-block" data-list-target="commodity">Commodities</a>
            </div>
            <div class="col-sm-4">
                <a class="btn btn-primary btn-block" data-list-target="currency">Currencies</a>
            </div>
            <div class="col-sm-4">
                <a class="btn btn-primary btn-block" data-list-target="equity">Equities</a>
            </div>
        </div>
    </div>

    <div class="search-list-groups">
        <?php foreach ($items as $key => $subItems) { ?>
            <div class="search-list search-list-<?php echo $key; ?> hidden" data-list="<?php echo $key; ?>">
                <h3>All <?php echo $key; ?></h3>
                <div class="row">
                    <div class="col-md-4">
                        <?php
                        if (!empty($subItems)) {
                            foreach ($subItems as $item) {
                                echo $item;
                            }
                        }
                        ?>
                        <div class="clearfix"></div>
                    </div>
                </div>
            </div>
        <?php } ?>
    </div>

    <?php /*

    <div class="row">
        <div class="col-sm-4">
            <?php
            $countries = DbReporters::model()->trade()->findAll(array('order' => 'country ASC'));
            if (!empty($countries)) {
                $i = 0;
                $total = round(count($countries) / 3);
                $letter = '';
                $newRow = false;
                foreach ($countries as $country) {
                    if ($i == $total || $i == ($total * 2)) {
                        $newRow = true;
                    }
                    if ($letter != $country->country[0]) {
                        if ($newRow) {
                            $newRow = false;
                            echo '</div><div class="col-sm-4">';
                        }
                        $letter = $country->country[0];
                        echo '<h4>' . strtoupper($letter) . '</h4>';
                    }

                    $premium = '<span class="badge">Pro</span>';
                    $isPremium = true;
                    if (Yii::app()->user->checkToolAccess('tra', 'country-full')) {
                        $premium = '<span class="badge badge-primary">Pro</span>';
                        $isPremium = false;
                    }
                    if ($country->searchDef == 1) {
                        $premium = '<span class="badge badge-primary">Essential</span>';
                        $isPremium = false;
                    } elseif ($country->g20 == 1) {
                        if (Yii::app()->user->checkToolAccess('tra', 'country-full') || Yii::app()->user->checkToolAccess('tra', 'country-g20')) {
                            $premium = '<span class="badge badge-primary">Pro</span>';
                            $isPremium = false;
                        } else {
                            $isPremium = true;
                            $premium = '<span class="badge">Pro</span>';
                        }
                    }

                    if ($isPremium) {
                        echo CHtml::link($country->ccode3 . ': ' . $country->country . $premium, Yii::app()->params['url-pricing'], array('class' => 'list-group-item', 'target' => '_blank', 'rel' => 'noopener'));
                    } else {
                        echo CHtml::link($country->ccode3 . ': ' . $country->country . $premium, array('trade/country', 'id' => $country->ccode3), array('class' => 'list-group-item'));
                    }
                    $i++;
                }
            }
            ?>
            <div class="clearfix"></div>
        </div>
    </div>

 */ ?>
</div>