$(function () {

    marketRefreshTypeahead(true);
    marketValidateSubmitBtn();

    $('.mr-submit-btn').click(function (e) {
        e.preventDefault();
        if (!$(this).hasClass('btn-disabled')) {
            MrSelectMarket();
        }
    });

    $(document).on('click', '.market-asset-selection .clearBtn', function () {
        $(this).parents('.form-group').slideUp(function () {
            $(this).remove();
            limitAddAsset();
            marketValidateSubmitBtn();
        });
    });

    $('.market-asset-add-btns .btn').click(function () {
        var market = $(this).data('market');

        $('#chartMarketModal .market-search').hide();
        $('#chartMarketModal .market-search input').data('ref', '').typeahead('val', '');
        marketRefreshTypeahead();
        $('#chartMarketModal .' + market + '-search').show();
        $('#chartMarketModal').data('type', market).modal('show');
    });

    $('#chartMarketModal .market-search input').on("typeahead:selected", function (object, datum, name) {
        $(this).data('ref', datum.code);
        validateSubmitBtn('market');
    }).on('keyup', function (e) {
        if ($(this).typeahead('val') == '') {
            $(this).data('ref', '');
            validateSubmitBtn('market');
        } else {
            if (e.which == 13 && $(this).parents('.form-group').find(".tt-suggestion:first-child").length > 0) {
                $(this).parents('.form-group').find(".tt-suggestion:first-child").trigger('click');
            }
        }
    });

    $('#chartMarketModal .chart-submit-btn').click(function () {
        var btn = $(this),
            market = btn.parents('.modal').data('type'),
            id = '',
            item = $('#chartMarketModal .' + market + '-search input').data('ref'),
            name = $('#chartMarketModal .' + market + '-search input').val();

        switch (market) {
            case 'commodity':
                name = 'Commodity: ' + name;
                id = 'assets_COM-' + item;
                item = 'COM-' + item;
                break;
            case 'currency':
                name = 'Currency: ' + name;
                id = 'assets_CUR-' + item;
                item = 'CUR-' + item;
                break;
            case 'equity':
                name = 'Equity: ' + name;
                id = 'assets_EQU-' + item;
                item = 'EQU-' + item;
                break;
        }

        if (!btn.hasClass('btn-disabled')) {
            btn.parents('.modal').modal('hide');

            if ($('#' + id).length === 0) {
                var html = '<div class="form-group">' +
                    '<div class="input-group">' +
                    '<input type="text" class="form-control" readonly maxlength="255" name="assets[' + item + ']" data-ref="' + item + '" value="' + name + '" id="' + id + '" />' +
                    '<span class="input-group-btn">' +
                    '<a href="#" class="btn btn-accent clearBtn"><span class="fa fa-times"></span> Remove</a>' +
                    '</span>' +
                    '</div>' +
                    '</div>';

                $('.market-asset-selection').append(html);
                marketValidateSubmitBtn();
            }
            limitAddAsset();
        }

    });

});

function limitAddAsset () {
    if ($('.market-asset-selection .form-group').length >= 10) {
        $('.market-asset-add-btns').slideUp();
    } else {
        $('.market-asset-add-btns').slideDown();
    }
}

function marketRefreshTypeahead (init) {
    init = (typeof init !== 'undefined' ? init : false);

    var restrict = {
        'COM': [],
        'CUR': [],
        'EQU': [],
    }
    $('.market-asset-selection input').each(function () {
        if (typeof $(this).data('ref') !== 'undefined') {
            var key = $(this).data('ref').slice(0, 3),
                val = $(this).data('ref').slice(4);
            restrict[key].push(val);
        }
    });
    restrict['COM'].join();
    restrict['CUR'].join();
    restrict['EQU'].join();

    if (!init) {
        filterLists($('#chartMarketModal .chart-search-list-commodity, #chartMarketCompareModal .chart-search-list-commodity'), {
            type: 'commodity',
            //commodity: $('#commodity-search-1').data('ref'),
            restrict: restrict['COM'],
            selected: restrict['COM'],
        });
        filterLists($('#chartMarketModal .chart-search-list-currency, #chartMarketCompareModal .chart-search-list-currency'), {
            type: 'currency',
            //commodity: $('#commodity-search-1').data('ref'),
            restrict: restrict['CUR'],
            selected: restrict['CUR'],
        });
        filterLists($('#chartMarketModal .chart-search-list-equity, #chartMarketCompareModal .chart-search-list-equity'), {
            type: 'equity',
            //commodity: $('#commodity-search-1').data('ref'),
            restrict: restrict['EQU'],
            selected: restrict['EQU'],
        });
    }

    var datasets = [];

    datasets.push({
        'selector': '.commodity-search input',
        'url': siteData.data('url') + '/autoComplete/commodity?restrict=' + restrict['COM'] + '&term=%QUERY',
    });
    datasets.push({
        'selector': '.currency-search input',
        'url': siteData.data('url') + '/autoComplete/currency?restrict=' + restrict['CUR'] + '&term=%QUERY',
    });
    datasets.push({
        'selector': '.equity-search input',
        'url': siteData.data('url') + '/autoComplete/equity?restrict=' + restrict['EQU'] + '&term=%QUERY',
    });

    refreshTypeahead('market', datasets, init);
}

function marketValidateSubmitBtn () {
    var valid = true;

    if (valid) {
        valid = false;
        $('#chartMarketModal .market-search input').each(function () {
            if (typeof $(this).data('ref') !== 'undefined' && $(this).data('ref') != '') {
                valid = true;
            }
        });
    }

    if (valid) {
        $('#chartMarketModal .chart-submit-btn').addClass('btn-primary btn-inverse').removeClass('btn-disabled');
    } else {
        $('#chartMarketModal .chart-submit-btn').addClass('btn-disabled').removeClass('btn-primary btn-inverse');
    }

    if ($('.market-asset-selection .form-group').length >= 1) {
        $('.mr-submit-btn').addClass('btn-primary btn-inverse').removeClass('btn-disabled');
    } else {
        $('.mr-submit-btn').addClass('btn-disabled').removeClass('btn-primary btn-inverse');
    }

}

function MrSelectMarket () {
    $('.chart-item-data, .chart-item, .chart-item-table').remove();
    CrChartCount = 0;

    $('body').append('<div class="page-loading-ajax" style="display: none;"><div class="chart-loading-text"><span class="fa fa-spinner fa-spin"></span><br /><br />Please wait a moment while we generate your real-time report</div></div>');
    $('.page-loading-ajax').fadeIn();

    MrTriggerCharts();
}

function MrTriggerCharts () {
    var charts = {
        'commodity': {},
        'currency': {},
        'equity': {},
    };

    $('.market-asset-selection input').each(function (i) {
        var val = $(this).data('ref'),
            cat = val.substring(0, 3),
            asset = val.slice(4);
        switch (cat) {
            case 'COM':
                cat = 'commodity';
                break;
            case 'CUR':
                cat = 'currency';
                break;
            case 'EQU':
                cat = 'equity';
                break;
        }
        charts[cat][asset] = {
            'item': asset
        }
    });

    var initDownload = true;
    for (var group in charts) {
        for (var key in charts[group]) {
            CrChartCount++;
        }
    }
    for (var group in charts) {
        for (var key in charts[group]) {
            charts[group][key]['view'] = 'data';

            $.ajax({
                url: siteData.data('url') + '/' + group + '/chart/',
                dataType: 'json',
                type: 'post',
                data: charts[group][key],
                success: function (returnData) {
                    if (returnData.valid) {
                        $('.chart-group[data-chart-type=' + returnData.type + '] .chart-group-data').append(returnData.chart.data);
                        drawChart(returnData.chart.chartId);
                        if (initDownload) {
                            initDownload = false;
                            CrDownload = 'init';
                            MrTriggerDownload();
                        }
                    } else {
                        CrChartCount--;
                        if (CrChartCount == 0) {
                            $('.page-loading-ajax').fadeOut(function () {
                                $(this).remove();
                            });
                            addFlashMessage('danger', 'Sorry, there isn\'t enough data for that asset');
                        }
                    }
                }
            });
        }
    }
}

function MrTriggerDownload () {

    var valid = false,
        loadingCount = 0,
        groups = ['commodity', 'currency', 'equity'];

    for (var key in groups) {
        if (typeof $('.chart-group[data-chart-type=' + groups[key] + '] .chart-loading').data('count') !== 'undefined') {
            loadingCount += parseInt($('.chart-group[data-chart-type=' + groups[key] + '] .chart-loading').data('count'));
        }
    }

    if (!isNaN(loadingCount)) {
        CrLoadingCount = loadingCount;
    }

    if (CrLoadingCount > 0) {
        if (CrDownload == 'init') {
            CrDownload = 'loading';
        }
        setTimeout(function () {
            MrTriggerDownload();
        }, 200);

    } else if (CrLoadingCount < 1 && CrDownload == 'loading') {
        CrDownload = 'render';
        CrLoadingCount = 0;
        for (var key in groups) {
            $('.chart-group[data-chart-type=' + groups[key] + '] .chart-item').each(function () {
                CrLoadingCount++;
                var chart = chartsLog[$(this).data('chart-id')];
                var event = google.visualization.events.addListener(chart.chartObjPrint, 'ready', function () {
                    CrLoadingCount--;
                    google.visualization.events.removeListener(event);
                    MrTriggerDownload();
                });
                renderPrintableChart(chart.chartId, 'pdfReport');
            });
        }

    } else if (CrLoadingCount < 1 && CrDownload == 'render') {
        CrDownload = 'done';
        setTimeout(function () {
            MrDownloadReport();
        }, 200);
    }

}

function MrDownloadReport (resend) {
    resend = (typeof resend !== 'undefined' ? resend : 0);

    var groups = ['commodity', 'currency', 'equity'],
        charts = {};

    for (var key in groups) {
        charts[groups[key]] = {};
        $('.chart-group[data-chart-type=' + groups[key] + '] .chart-item-data').each(function (i) {

            if (typeof $(this).data('img') !== 'undefined' && $(this).data('img').length > 0) {
                var group = $(this).parents('.chart-group').data('chart-type');
                charts[group][$(this).data('item')] = $(this).data();

            }
        });
    }

    $.ajax({
        url: siteData.data('url') + '/report/market/',
        type: 'post',
        dataType: 'json',
        contentType: "application/x-www-form-urlencoded",
        data: {
            'format': $('#FormReportMarket_format').val(),
            'charts': charts,
        },
        success: function (returnData) {
            var done = false;
            if (returnData.valid) {
                /*var link = document.createElement('a');
                link.href = returnData.url;
                link.download = returnData.filename;
                document.body.appendChild(link);
                link.click();
                link.remove();*/
                triggerFileDownload(returnData.url, returnData.filename);
                done = true;
            } else if (returnData.resend && resend < 10) {
                resend++;
                MrDownloadReport(resend);
            } else if (typeof returnData.error !== 'undefined') {
                for (var key in returnData.error) {
                    addFlashMessage('danger', returnData.error[key]);
                }
                done = true;
            }

            if (done) {
                $('.page-loading-ajax').fadeOut(function () {
                    $(this).remove();
                });
            }
        },
        error: function () {
            $('.page-loading-ajax').fadeOut(function () {
                $(this).remove();
            });
        }
    });
}