$(function(){

    $('.dash-tabs .tab-pane').data('resized', '0');
    $('#dash-nav').on('changed.bs.select', function () {
        var $tab = $('.dash-tabs #'+$(this).val());
        $('.dash-tabs .tab-pane').removeClass('active');
        $tab.addClass('active');

        if($tab.data('resized') == '0') {
            $tab.find('.chart-group').each(function () {
                var chartId = $(this).data('chart-id');
                if (typeof chartsLog[chartId] !== 'undefined') {
                    toggleChartSeries(chartId);
                }
            });
            $tab.data('resized', '1');
        }
    });

    $(document).on('click', '.dash-change-toggle', function(){
        if(!$(this).hasClass('btn-primary')) {
            $(this).parents('.dash-change-toggles').find('.dash-change-toggle').removeClass('btn-disabled');
            $(this).addClass('btn-disabled');

            var $data = $(this).parents('.chart-group').find('.chart-item-data');
            $data.data('report', $(this).data('report'))
            drawChart($data.data('chart-id'), true);
        }
    });

    $(document).on('click', '.spark-edit-btn', function(){
        var chartId = $(this).parents('.chart-item').data('chart-id');
        $('#dashSparkModal').data('chart-id', chartId);
        $('#dashSparkModal').data('dash-type', 'spark');
        $('#dashSparkModal').data('target', $(this));
        $('#dashSparkModal').modal('show');
    });

    $(document).on('click', '.yoy-edit-btn', function(){
        var chartId = $(this).parents('.chart-item').data('chart-id');
        $('#dashSparkModal').data('chart-id', chartId);
        $('#dashSparkModal').data('dash-type', 'yoy');
        $('#dashSparkModal').data('target', $(this));
        $('#dashSparkModal').modal('show');
    });

    $(document).on('click', '.macro-edit-btn', function(){
        var chartId = $('.dash-g10 .chart-item-data').data('chart-id');
        $('#dashG10Modal').data('chart-id', chartId);
        $('#dashG10Modal').modal('show');
    });

    $(document).on('click', '.dash-outlook-item-btn', function(){
        var $group = $(this).parents('.chart-group'),
            chartId = $group.find('.chart-item').data('chart-id');
        $('#dashSparkModal').data('chart-id', chartId);
        $('#dashSparkModal').data('dash-type', 'outlook');
        $('#dashSparkModal').data('target', $(this));
        $('#dashSparkModal').modal('show');
    });

    $('#dash-spark_market').on('changed.bs.select', function () {
        $('#dashSparkModal .spark-asset-form-group').slideUp();
        $('#dashSparkModal .spark-asset-form-group input').val('').data('ref', '');
        $('#dashSparkModal .spark-asset-'+$(this).val()).slideDown();

        validateSubmitBtn('dash');
    });

    $('#dashSparkModal .chart-submit-btn').click(function () {
        var chartId = $(this).parents('.modal').data('chart-id'),
            $data = $('.chart-item-data[data-chart-id=' + chartId + ']'),
            market = $('#dash-spark_market').selectpicker('val'),
            item = $('#dashSparkModal .spark-asset-'+market+' input').data('ref'),
            oldItem = $data.data('item'),
            type = $('#dashSparkModal').data('dash-type'),
            $target = $('#dashSparkModal').data('target');
        if(!$(this).hasClass('btn-disabled')) {
            if (type === 'outlook') {
                oldItem = $target.data('ref');
                $target.data('ref', item);
                $target.find('.dash-outlook-item-label').text($('#dashSparkModal .chart-search-list-' + market + ' .list-group-item[data-ref=' + item + ']').data('label'));
            }

            if (typeof chartId !== 'undefined') {
                $.ajax({
                    url: siteData.data('url') + '/user/dashFavorite/',
                    method: 'post',
                    dataType: 'json',
                    data: {
                        'old': {
                            'item': oldItem,
                            'market': $data.data('market'),
                        },
                        'new': {
                            'item': item,
                            'market': market,
                        },
                        'type': type
                    }
                });

                var joined = [],
                    items = $data.data('item').split(',');
                for (var key in items) {
                    if (items[key] === oldItem) {
                        joined.push(item);
                    } else {
                        joined.push(items[key]);
                    }
                }
                if (oldItem === '') {
                    joined.push(item);
                }
                joined = joined.join();

                $data.data('market', market);
                $data.data('item', joined);

                drawChart(chartId);
                $(this).parents('.modal').modal('hide');
            }
        }
    });

    $('#dashG10Modal .chart-submit-btn').click(function () {
        var chartId = $(this).parents('.modal').data('chart-id'),
            $data = $('.chart-item-data[data-chart-id=' + chartId + ']'),
            macro = $('#dash-g10_macro').selectpicker('val');

        if(!$(this).hasClass('btn-disabled')) {
            if (typeof chartId !== 'undefined') {
                $.ajax({
                    url: siteData.data('url') + '/user/dashFavorite/',
                    method: 'post',
                    dataType: 'json',
                    data: {
                        'old': {
                            'macro': $data.data('macro'),
                        },
                        'new': {
                            'macro': macro,
                        },
                        'type': 'g10'
                    }
                });

                $data.data('macro', macro);

                drawChart(chartId);
                $(this).parents('.modal').modal('hide');
            }
        }
    });

    dashboardRefreshTypeahead(true);


    $('#dashSparkModal').on('show.bs.modal', function (e) {
        var chartId = $('#dashSparkModal').data('chart-id'),
            $data = $('.chart-item-data[data-chart-id=' + chartId + ']'),
            type = $('#dashSparkModal').data('dash-type'),
            $target = $('#dashSparkModal').data('target'),
            market = $data.data('market'),
            item = $data.data('item');

        if(type === 'outlook') {
            item = $target.data('ref')
            $('#dash-spark_market').parents('.form-group').hide();
        }else{
            $('#dash-spark_market').parents('.form-group').show();
        }

        if(item === ''){
            var label = '';
        }else{
            var label = $('#dashSparkModal .chart-search-list-' + market + ' .list-group-item[data-ref=' + item + ']').data('label');
        }

        dashboardRefreshTypeahead(false, $target.parents('.dash-group'));

        $('.dashSparkModal-title').hide();
        $('#dashSparkModal .title-'+type).show();

        $('#dash-spark_market').selectpicker('val',market);
        $('#dashSparkModal .spark-asset-form-group').hide();
        $('#dashSparkModal .spark-asset-form-group input').val('');
        $('#dashSparkModal .spark-asset-'+market+' input').val(label).data('ref', item);
        $('#dashSparkModal .spark-asset-'+market).show();
    });

    $('#dashG10Modal').on('show.bs.modal', function (e) {
        var chartId = $('#dashG10Modal').data('chart-id'),
            $data = $('.chart-item-data[data-chart-id=' + chartId + ']'),
            macro = $data.data('macro');

        dashboardRefreshTypeahead(false, $('.dash-g10'));

        $('#dash-g10_macro').selectpicker('val',macro);
    });

    $('#dashSparkModal').on('shown.bs.modal', function (e) {
        validateSubmitBtn('dash');
    });
});


function dashboardRefreshTypeahead (init, $group) {
    init = (typeof init !== 'undefined' ? init : false);

    var restrict = []

    if (!init) {
        $group.find('.chart-item-data').each(function(){
            if(typeof $(this).data('item') !== 'undefined'){
                restrict.push($(this).data('item'));
            } else if(typeof $(this).data('reporter') !== 'undefined'){
                restrict.push($(this).data('reporter'));
            }
        });
        restrict.join();

        filterLists($('#dashSparkModal .chart-search-list-commodity'), {
            type: 'commodity',
            restrict: restrict,
            selected: restrict,
        });
        filterLists($('#dashSparkModal .chart-search-list-currency'), {
            type: 'currency',
            restrict: restrict,
            selected: restrict,
            filter: 'USD'
        });
        filterLists($('#dashSparkModal .chart-search-list-equity'), {
            type: 'equity',
            restrict: restrict,
            selected: restrict,
        });
        filterLists($('#dashG10Modal #dash-g10_macro'), {
            type: 'macro',
            period: 'annual',
            returnPartner: true,
            strictHide: true,
            reporter: restrict,
            //selected: restrict,
        }, 'dropdown');
    }

    var datasets = [];

    datasets.push({
        'selector': '.spark-asset-commodity input',
        'url': siteData.data('url') + '/autoComplete/commodity?restrict='+restrict+'&term=%QUERY',
    });
    datasets.push({
        'selector': '.spark-asset-currency input',
        'url': siteData.data('url') + '/autoComplete/currency?restrict='+restrict+'&filterLike=USD&term=%QUERY',
    });
    datasets.push({
        'selector': '.spark-asset-equity input',
        'url': siteData.data('url') + '/autoComplete/equity?restrict='+restrict+'&term=%QUERY',
    });

    refreshTypeahead('dash', datasets, init);
}

function dashValidateSubmitBtn () {
    var valid = true;

    if (valid) {
        valid = false;

        $('#dashSparkModal input').each(function () {
            if (typeof $(this).data('ref') !== 'undefined' && $(this).data('ref') != '') {
                valid = true;
            }
        });
    }

    if (valid) {
        $('#dashSparkModal .chart-submit-btn').addClass('btn-primary btn-inverse').removeClass('btn-disabled');
    } else {
        $('#dashSparkModal .chart-submit-btn').addClass('btn-disabled').removeClass('btn-primary btn-inverse');
    }
}