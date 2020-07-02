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
    'reporter-trade' => array(),
    'reporter-macro' => array(),
    'trade' => array(),
    'macro' => array(),
);

$lists = [
    'commodity' => DbCommodities::model()->findAll($criteria),
    'currency' => DbCurrencies::model()->findAll($criteria),
    'equity' => DbEquities::model()->findAll($criteria),
];
foreach ($lists as $key => $list) {
    if (!empty($list)) {
        $i = 0;
        $total = ceil(count($list) / 3);
        $letter = '';
        $newRow = false;
        foreach ($list as $item) {
            if ($i == $total || $i == ($total * 2)) {
                $newRow = true;
            }
            $i++;
            if ($letter != $item->name[0]) {
                if ($i > 1) {
                    $items[$key][] = '</div>'; //close tag for .chart-search-list-subgroup
                }
                if ($newRow) {
                    $newRow = false;
                    $items[$key][] = '</div><div class="col-md-4 chart-search-list-column">';
                }
                $letter = $item->name[0];
                $items[$key][] = '<div class="chart-search-list-subgroup" data-group="' . strtoupper($letter) . '">';
                $items[$key][] = '<h3>' . strtoupper($letter) . '</h3>';
            }

            $premium = '<span class="badge">Pro</span>';
            if (Yii::app()->user->checkToolAccess('com', 'country-full')) {
                $premium = '<span class="badge badge-primary">Pro</span>';
            }
            if ($item->access == 1) {
                $premium = '<span class="badge badge-primary">Essentials</span>';
            }

            $items[$key][] = '<a href="#" class="list-group-item" data-ref="' . $item->code . '" data-label="' . $item->name . '">' . $item->name . $premium . '</a>';

        }
        $items[$key][] = '</div>'; //close tag for .chart-search-list-subgroup
    }
}

$lists = [
    'reporter-trade' => DbReporters::model()->trade()->findAll(array('order' => 'country ASC')),
    'reporter-macro' => DbReporters::model()->macro()->findAll(array('order' => 'country ASC')),
];
foreach ($lists as $key => $list) {
    if (!empty($list)) {
        $i = 0;
        $total = ceil(count($list) / 3);
        $letter = '';
        $newRow = false;
        foreach ($list as $item) {
            if ($i == $total || $i == ($total * 2)) {
                $newRow = true;
            }
            $i++;
            if ($letter != $item->name[0]) {
                if ($i > 1) {
                    $items[$key][] = '</div>'; //close tag for .chart-search-list-subgroup
                }
                if ($newRow) {
                    $newRow = false;
                    $items[$key][] = '</div><div class="col-md-4 chart-search-list-column">';
                }
                $letter = $item->name[0];
                $items[$key][] = '<div class="chart-search-list-subgroup" data-group="' . strtoupper($letter) . '">';
                $items[$key][] = '<h3>' . strtoupper($letter) . '</h3>';
            }

            $premium = '';
            if (Yii::app()->user->checkToolAccess('tra', 'service-pro')) {
                $premium = '<span class="badge badge-primary">Pro</span>';
            }
            if ($item->access == 1) {
                if(Yii::app()->user->checkToolAccess('tra', 'service-ess')){
                    $premium = '<span class="badge badge-primary">Essentials</span>';
                } else {
                    $premium = '<span class="badge">Essentials</span>';
                }
            } elseif ($item->access == 2) {
                if (Yii::app()->user->checkToolAccess('tra', 'service-pro')) {
                    $premium = '<span class="badge badge-primary">Pro</span>';
                } else {
                    $premium = '<span class="badge">Pro</span>';
                }
            }

            //$items[$key][] = '<a href="' . Yii::app()->baseUrl . '/trade/country/' . $item->code . '" class="list-group-item" data-ref="' . $item->ccode . '" data-label="' . $item->code . ', ' . $item->name . '">' . $item->code . ': ' . $item->name . $premium . '</a>';
            $items[$key][] = '<a href="#" class="list-group-item" data-ref="' . $item->code . '" data-label="' . $item->code . ', ' . $item->name . '">' . $item->code . ': ' . $item->name . $premium . '</a>';
        }
        $items[$key][] = '</div>'; //close tag for .chart-search-list-subgroup
    }
}

$lists = [
    'trade' => ActiveRecordTradeData::model()->listIndicator(),
    'macro' => ActiveRecordMacroData::model()->listMacro(),
];
foreach ($lists as $key => $list) {
    if (!empty($list)) {
        $i = 0;
        $total = ceil(count($list) / 3);
        $letter = '';
        $newRow = false;
        foreach ($list as $group => $item) {
            if ($i == $total || $i == ($total * 2)) {
                $newRow = true;
            }
            $i++;

            if ($letter != $group) {
                if ($i > 1) {
                    $items[$key][] = '</div>'; //close tag for .chart-search-list-subgroup
                }
                if ($newRow) {
                    $newRow = false;
                    $items[$key][] = '</div><div class="col-md-4 chart-search-list-column">';
                }
                $letter = $group;
                $items[$key][] = '<div class="chart-search-list-subgroup" data-group="' . strtoupper($letter) . '">';
                $items[$key][] = '<h3>' . strtoupper($letter) . '</h3>';
            }

            foreach ($item as $itemKey => $itemName) {
                $items[$key][] = '<a href="#" class="list-group-item" data-ref="' . $itemKey . '" data-label="' . $itemName . '">' . $itemName . '</a>';
            }
        }
        $items[$key][] = '</div>'; //close tag for .chart-search-list-subgroup
    }
}
/*$macros = ActiveRecordMacroData::model()->listMacro();
if (!empty($macros)) {
    $i = 0;
    $total = ceil(count($macros) / 3);
    $letter = '';
    $newRow = false;
    foreach ($macros as $macro) {
        if ($i == $total || $i == ($total * 2)) {
            $newRow = true;
        }
        $i++;
        if ($letter != $macro->country[0]) {
            if ($i > 1) {
                $items['reporter'][] = '</div>'; //close tag for .chart-search-list-subgroup
            }
            if ($newRow) {
                $newRow = false;
                $items['reporter'][] = '</div><div class="col-md-4">';
            }
            $letter = $macro->country[0];
            $items['reporter'][] = '<div class="chart-search-list-subgroup" data-group="' . strtoupper($letter) . '">';
            $items['reporter'][] = '<h3>' . strtoupper($letter) . '</h3>';
        }

        $premium = '<span class="badge">Pro</span>';
        if (Yii::app()->user->checkToolAccess('tra', 'country-full')) {
            $premium = '<span class="badge badge-primary">Pro</span>';
        }
        if ($macro->searchDef == 1) {
            $premium = '<span class="badge badge-primary">Essentials</span>';
        } elseif ($macro->g20 == 1) {
            if (Yii::app()->user->checkToolAccess('tra', 'country-full') || Yii::app()->user->checkToolAccess('tra', 'country-g20')) {
                $premium = '<span class="badge badge-primary">Pro</span>';
            } else {
                $premium = '<span class="badge">Pro</span>';
            }
        }

        $items['reporter'][] = '<a href="'.Yii::app()->baseUrl.'/trade/country/'.$macro->code.'" class="list-group-item" data-ref="' . $macro->ccode . '" data-label="' . $reporter->ccode3 . ', ' . $macro->country . '">' . $macro->ccode3 . ': ' . $macro->country . $premium . '</a>';
    }
    $items['reporter'][] = '</div>'; //close tag for .chart-search-list-subgroup
}*/

?>

<div class="content-header">
    <div class="container">

    </div>
</div>

<div class="container advanced-search">

    <div class="text-center section-30">
        <h3>Advanced Search</h3>
        <p>Please select from options and sub-options below to construct your report.</p>
    </div>

    <div class="hidden row search-list-nav">
        <div class="col-sm-4">
            <a class="btn btn-primary btn-block search-list-nav-btn" data-list-target="country">Search By Country</a>
        </div>
        <div class="col-sm-4">
            <a class="btn btn-primary btn-block search-list-nav-btn" data-list-target="report">Search By Economic Concept</a>
        </div>
        <div class="col-sm-4">
            <a class="btn btn-primary btn-block search-list-nav-btn active" data-list-target="market">Search By Market Price</a>
        </div>
    </div>

    <div class="hidden search-list-nav search-list-nav-country">
        <div class="sep sep-sm"></div>
        <div class="row">
            <div class="col-sm-4 col-sm-push-2">
                <a class="btn btn-primary btn-block search-list-nav-btn" data-list-target="reporter-trade">Search By Country Trade</a>
            </div>
            <div class="col-sm-4 col-sm-push-2">
                <a class="btn btn-primary btn-block search-list-nav-btn" data-list-target="reporter-macro">Search By Country Economics</a>
            </div>
        </div>
    </div>

    <div class="hidden search-list-nav search-list-nav-report">
        <div class="sep sep-sm"></div>
        <div class="row">
            <div class="col-sm-4 col-sm-push-2">
                <a class="btn btn-primary btn-block search-list-nav-btn" data-list-target="trade">Search By Trade Indicator</a>
            </div>
            <div class="col-sm-4 col-sm-push-2">
                <a class="btn btn-primary btn-block search-list-nav-btn" data-list-target="macro">Search By Economic Indicator</a>
            </div>
        </div>
    </div>

    <div class="search-list-nav search-list-nav-market">

        <div class="row">
            <div class="col-sm-4">
                <a class="btn btn-primary btn-block search-list-nav-btn" data-list-target="commodity">Search By Commodity</a>
            </div>
            <div class="col-sm-4">
                <a class="btn btn-primary btn-block search-list-nav-btn" data-list-target="currency">Search By Currency</a>
            </div>
            <div class="col-sm-4">
                <a class="btn btn-primary btn-block search-list-nav-btn" data-list-target="equity">Search By Equity</a>
            </div>
        </div>
    </div>

    <div class="search-list-headers">

    </div>


    <div class="search-list-groups">
        <?php foreach ($items as $key => $subItems) { ?>
            <div class="search-list search-list-<?php echo $key; ?> hidden" data-list="<?php echo $key; ?>">
                <div class="search-list-header">
                    <div class="sep sep-sm"></div>
                    <?php
                    $label = '';
                    switch($key){
                        case 'commodity':
                        case 'currency':
                        case 'equity':
                            $label = 'Selected '.ucfirst($key);
                            break;

                        case 'reporter-trade':
                        case 'reporter-macro':
                            $label = 'Selected Country';
                            break;

                        case 'trade':
                        case 'macro':
                            $label = 'Selected Indicator';
                            break;
                    }
                    echo '<h3 class="text-center">'.$label.': <span class="search-list-selected" data-ref="">None</span> </h3>';
                    ?>
                </div>
                <div class="search-list-body">
                    <p class="text-center">Please make a selection from one of the available items below.</p>
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
            </div>
        <?php } ?>
    </div>

    <div class="hidden search-submit">
        <div class="sep sep-sm"></div>
        <div class="row">
            <div class="col-sm-4 col-sm-push-4">
                <a class="btn btn-primary btn-block search-submit-btn">Load Report</a>
            </div>
        </div>
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