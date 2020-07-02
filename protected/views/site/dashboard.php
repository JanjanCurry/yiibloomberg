<?php
$withNav = (isset($withNav) ? $withNav : false);

$sparklines = [
    'fav' => [],
    'top' => [],
    'bottom' => [],
];
$lastYear = [];

$types = [];
if (Yii::app()->user->checkAccess('tool-com')) {
    $types[] = 'commodity';
}
if (Yii::app()->user->checkAccess('tool-cur')) {
    $types[] = 'currency';
}
if (Yii::app()->user->checkAccess('tool-equ')) {
    $types[] = 'equity';
}

///////////////////////////////////////////////////
// SPARKLINES
///////////////////////////////////////////////////
$criteria = new CDbCriteria();
$criteria->order = 'orderId ASC';
$criteria->limit = 5;
$criteria->compare('userId', $this->user->id);
$criteria->compare('type', 'spark');
$favorites = DbUserDash::model()->findAll($criteria);
if (!empty($favorites)) {
    foreach ($favorites as $i => $favorite) {
        $data = $favorite->data;
        $data['init'] = false;
        $data['session'] = 'ignore';
        $data['ignorePermissions'] = true;
        $data['period'] = 'month';
        $data['editable'] = true;
        $data['showTable'] = false;
        $data['view'] = 'sparkline';
        $data['report'] = 'sparkline';
        $data['startTime'] = strtotime('now');
        $data['endTime'] = strtotime('now');
        $widget = $this->widget('application.widgets.MarketWidget', $data);
        $key = Yii::app()->format->safeUrlString($widget->name);
        $sparklines['fav'][] = [
            'name' => $widget->name,
            'data' => $data,
            'asset' => $data['item'],
            'market' => $widget->market,
            'widget' => $widget,
        ];
    }
}
if(count($sparklines['fav']) < 5){
    for($i = 0; $i < 5 - count($sparklines['fav']); $i++){
        $sparklines['fav'][] = [
            'name' => $widget->name,
            'data' => $data,
            'asset' => $data['item'],
            'market' => $widget->market,
            'widget' => $widget,
        ];
    }
}

$topAssets = DbVar::model()->findByAttributes(['name' => 'dash-top-assets']);
if (!empty($topAssets) && !empty($topAssets->data) && !empty($topAssets->data['all'])) {
    if (!empty($topAssets->data['all']['top'])) {
        $i = 0;
        foreach ($topAssets->data['all']['top'] as $key => $val) {
            $parts = explode('|', $key);
            $data = [
                'item' => $parts[1],
                'market' => $parts[0],
                'editable' => false,
                'session' => 'ignore',
                'ignorePermissions' => true,
                'init' => false,
                'period' => 'month',
                'showTable' => false,
                'view' => 'sparkline',
                'startTime' => strtotime('now'),
                'endTime' => strtotime('now'),
            ];
            $widget = $this->widget('application.widgets.MarketWidget', $data);
            $sparklines['top'][$parts[1]] = [
                'name' => $widget->name,
                'data' => $data,
                'widget' => $widget,
            ];

            $i++;
            if ($i >= 5) {
                break;
            }
        }
    }
    if (!empty($topAssets->data['all']['bottom'])) {
        $i = 0;
        foreach ($topAssets->data['all']['bottom'] as $key => $val) {
            $parts = explode('|', $key);
            $data = [
                'item' => $parts[1],
                'market' => $parts[0],
                'editable' => false,
                'session' => 'ignore',
                'ignorePermissions' => true,
                'init' => false,
                'period' => 'month',
                'showTable' => false,
                'view' => 'sparkline',
                'startTime' => strtotime('now'),
                'endTime' => strtotime('now'),
            ];
            $widget = $this->widget('application.widgets.MarketWidget', $data);
            $sparklines['bottom'][$parts[1]] = [
                'name' => $widget->name,
                'data' => $data,
                'widget' => $widget,
            ];

            $i++;
            if ($i >= 5) {
                break;
            }
        }
    }
}

///////////////////////////////////////////////////
// YOY
///////////////////////////////////////////////////
$criteria = new CDbCriteria();
$criteria->order = 'orderId ASC';
$criteria->limit = 3;
$criteria->compare('userId', $this->user->id);
$criteria->compare('type', 'yoy');
$favorites = DbUserDash::model()->findAll($criteria);
if (!empty($favorites)) {
    foreach ($favorites as $i => $favorite) {
        $data = $favorite->data;
        $data['init'] = true;
        $data['period'] = 'month';
        $data['session'] = 'ignore';
        $data['showTable'] = false;
        $data['ignorePermissions'] = true;
        $data['view'] = 'dash-change';
        $data['report'] = 'dash-change';
        $data['chartType'] = 'column';
        $data['startTime'] = strtotime('now');
        $data['endTime'] = strtotime('+2 months');
        $widget = $this->widget('application.widgets.MarketWidget', $data);
        $key = Yii::app()->format->safeUrlString($widget->name);
        $lastYear[$key] = [
            'name' => $widget->name,
            'data' => $data,
            'widget' => $widget,
        ];
    }
}


///////////////////////////////////////////////////
// CATEGORY OUTLOOK
///////////////////////////////////////////////////
$catOutlook = $markets = [
    'commodity' => [],
    'currency' => [],
    'equity' => [],
];
$criteria = new CDbCriteria();
$criteria->order = 'orderId ASC';
$criteria->compare('userId', $this->user->id);
$criteria->compare('type', 'outlook');
$favorites = DbUserDash::model()->findAll($criteria);
if (!empty($favorites)) {
    foreach ($favorites as $i => $favorite) {
        $market = $favorite->data['market'];
        if(count($markets[$market]) < 5) {
            $markets[$market][] = $favorite->data['item'];
        }
    }
}

foreach($markets as $market => $items) {
    if (!empty($market)) {
        $data = [];
        $data['market'] = $market;
        $data['item'] = $items;
        $data['init'] = true;
        $data['period'] = 'month';
        $data['session'] = 'ignore';
        $data['showTable'] = false;
        $data['ignorePermissions'] = true;
        $data['view'] = 'dash-outlook';
        $data['report'] = 'dash-outlook';
        $data['startTime'] = strtotime('now');
        $data['endTime'] = strtotime('+2 months');
        $widget = $this->widget('application.widgets.MarketWidget', $data);
        $catOutlook[$market] = [
            'name' => $widget->name,
            'data' => $data,
            'widget' => $widget,
        ];
    }
}

///////////////////////////////////////////////////
// EDIT LISTS
///////////////////////////////////////////////////
$companyId = [0];
if(!empty($this->user->companyId)){
    $companyId[] = $this->user->companyId;
}
$criteria = new CDbCriteria();
$criteria->compare('companyId', $companyId);
$criteria->order = 'name ASC';

$listAll = array(
    'commodity' => array(),
    'currency' => array(),
    'equity' => array(),
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
                $listAll['commodity'][] = '</div>'; //close tag for .chart-search-list-subgroup
            }
            if ($newRow) {
                $newRow = false;
                $listAll['commodity'][] = '</div><div class="col-md-4 chart-search-list-column">';
            }
            $letter = $commodity->name[0];
            $listAll['commodity'][] = '<div class="chart-search-list-subgroup" data-group="' . strtoupper($letter) . '">';
            $listAll['commodity'][] = '<h3>' . strtoupper($letter) . '</h3>';
        }

        $premium = '<span class="badge">Pro</span>';
        if (Yii::app()->user->checkToolAccess('com', 'country-full')) {
            $premium = '<span class="badge badge-primary">Pro</span>';
        }
        if ($commodity->access == 1) {
            $premium = '<span class="badge badge-primary">Essentials</span>';
        }

        $listAll['commodity'][] = '<a href="#" class="list-group-item" data-ref="' . $commodity->code . '" data-label="' . $commodity->name . '">' . $commodity->name . $premium . '</a>';

    }
    $listAll['commodity'][] = '</div>'; //close tag for .chart-search-list-subgroup
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
                $listAll['currency'][] = '</div>'; //close tag for .chart-search-list-subgroup
            }
            if ($newRow) {
                $newRow = false;
                $listAll['currency'][] = '</div><div class="col-md-4 chart-search-list-column">';
            }
            $letter = $currency->name[0];
            $listAll['currency'][] = '<div class="chart-search-list-subgroup" data-group="' . strtoupper($letter) . '">';
            $listAll['currency'][] = '<h3>' . strtoupper($letter) . '</h3>';
        }

        $premium = '<span class="badge">Pro</span>';
        if (Yii::app()->user->checkToolAccess('cur', 'country-full')) {
            $premium = '<span class="badge badge-primary">Pro</span>';
        }
        if ($currency->access == 1) {
            $premium = '<span class="badge badge-primary">Essentials</span>';
        }

        $listAll['currency'][] = '<a href="#" class="list-group-item" data-ref="' . $currency->code . '" data-label="' . $currency->name . '">' . $currency->name . $premium . '</a>';

    }
    $listAll['currency'][] = '</div>'; //close tag for .chart-search-list-subgroup
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
                $listAll['equity'][] = '</div>'; //close tag for .chart-search-list-subgroup
            }
            if ($newRow) {
                $newRow = false;
                $listAll['equity'][] = '</div><div class="col-md-4 chart-search-list-column">';
            }
            $letter = $equity->name[0];
            $listAll['equity'][] = '<div class="chart-search-list-subgroup" data-group="' . strtoupper($letter) . '">';
            $listAll['equity'][] = '<h3>' . strtoupper($letter) . '</h3>';
        }

        $premium = '<span class="badge">Pro</span>';
        if (Yii::app()->user->checkToolAccess('equ', 'country-full')) {
            $premium = '<span class="badge badge-primary">Pro</span>';
        }
        if ($equity->access == 1) {
            $premium = '<span class="badge badge-primary">Essentials</span>';
        }

        $listAll['equity'][] = '<a href="#" class="list-group-item" data-ref="' . $equity->code . '" data-label="' . $equity->name . '">' . $equity->name . $premium . '</a>';

    }
    $listAll['equity'][] = '</div>'; //close tag for .chart-search-list-subgroup
}
?>

<div class="container-fluid page-dashboard pt-30">
    <?php
    if($withNav){
        $this->renderPartial('//site/dashboard/_withNav', [
            'sparklines' => $sparklines,
            'lastYear' => $lastYear,
            'catOutlook' => $catOutlook,
        ]);
    }else{
        $this->renderPartial('//site/dashboard/_withoutNav', [
            'sparklines' => $sparklines,
            'lastYear' => $lastYear,
            'catOutlook' => $catOutlook,
        ]);
    }
    ?>


    <div class="modal fade-scale chart-modal" id="dashSparkModal" tabindex="-1" role="dialog" data-type="dash">
        <div class="modal-dialog modal-fw" role="document">
            <div class="modal-content">
                <div class="modal-body">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><i class="fa fa-times"></i></button>
                    <h3 class="text-center dashSparkModal-title title-spark">Your Performance Assets</h3>
                    <h3 class="text-center dashSparkModal-title title-yoy hidden">3 Month Forecast Assets</h3>
                    <h3 class="text-center dashSparkModal-title title-outlook hidden">Category Outlook Assets</h3>

                    <div class="form-group spark-market">
                        <?php echo CHtml::dropDownList('dash-spark[market]', 'commodity', ActiveRecordMarketData::model()->listMarket(), array(
                            'class' => 'form-control selectpicker',
                        )); ?>
                    </div>
                    <div class="form-group form-typeahead hidden spark-asset-form-group spark-asset-commodity">
                        <div class="input-group">
                            <?php echo CHtml::textField('dash-spark[commodity]', '', array(
                                'class' => 'form-control',
                                'placeholder' => 'Commodity name or reference code',
                                'maxlength' => 255
                            )); ?>
                            <span class="input-group-btn">
                                <a href="#" class="btn btn-primary toggleBtn" data-toggle="chart-search-list-commodity"><span class="fa fa-list"></span> List All</a>
                                <a href="#" class="btn btn-primary clearBtn hidden"><span class="fa fa-times"></span> Clear</a>
                            </span>
                        </div>
                    </div>
                    <div class="form-group form-typeahead hidden spark-asset-form-group spark-asset-currency">
                        <div class="input-group">
                            <?php echo CHtml::textField('dash-spark[currency]', '', array(
                                'class' => 'form-control',
                                'placeholder' => 'Currency name or reference code',
                                'maxlength' => 255
                            )); ?>
                            <span class="input-group-btn">
                                <a href="#" class="btn btn-primary toggleBtn" data-toggle="chart-search-list-currency"><span class="fa fa-list"></span> List All</a>
                                <a href="#" class="btn btn-primary clearBtn hidden"><span class="fa fa-times"></span> Clear</a>
                            </span>
                        </div>
                    </div>
                    <div class="form-group form-typeahead hidden spark-asset-form-group spark-asset-equity">
                        <div class="input-group">
                            <?php echo CHtml::textField('dash-spark[equity]', '', array(
                                'class' => 'form-control',
                                'placeholder' => 'Equity name or reference code',
                                'maxlength' => 255
                            )); ?>
                            <span class="input-group-btn">
                                <a href="#" class="btn btn-primary toggleBtn" data-toggle="chart-search-list-equity"><span class="fa fa-list"></span> List All</a>
                                <a href="#" class="btn btn-primary clearBtn hidden"><span class="fa fa-times"></span> Clear</a>
                            </span>
                        </div>
                    </div>

                    <div class="form-group margin-top-20">
                        <a href="#" class="btn btn-primary btn-block chart-submit-btn"><span class="fa fa-plus"></span> Save</a>
                    </div>

                    <?php foreach ($listAll as $key => $subItems) { ?>
                        <div class="chart-search-list chart-search-list-<?php echo $key; ?> hidden">
                            <h3>All <?php echo ActiveRecordMarketData::model()->getListLabel('listMarketPlural', $key); ?></h3>
                            <div class="row">
                                <div class="col-md-4 chart-search-list-column">
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
            </div>
        </div>
    </div>

    <div class="modal fade-scale chart-modal" id="dashG10Modal" tabindex="-1" role="dialog" data-type="dash">
        <div class="modal-dialog modal-fw" role="document">
            <div class="modal-content">
                <div class="modal-body">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><i class="fa fa-times"></i></button>
                    <h3 class="text-center">Your Economic Indicators</h3>

                    <div class="form-group spark-market">
                        <?php echo CHtml::dropDownList('dash-g10[macro]', null, ActiveRecordMacroData::model()->listMacro(), array(
                            'class' => 'form-control selectpicker',
                        )); ?>
                    </div>

                    <div class="form-group margin-top-20">
                        <a href="#" class="btn btn-primary btn-block chart-submit-btn"><span class="fa fa-plus"></span> Save</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
