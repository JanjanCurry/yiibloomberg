var CrChartCount = 0
$(function () {

    $('.cr-submit-btn').click(function (e) {
        e.preventDefault();
        if(!$(this).hasClass('btn-disabled')) {
            $('.report-data').data('reporter', $('#FormReportCountry_reporter').data('ref'));
            CrSelectCountry($('#FormReportCountry_reporter').data('ref'));
        }
    });

    crRefreshTypeahead(true);
    crValidateSubmitBtn(false);
    $('#FormReportCountry_reports').on('changed.bs.select', function () {
        $('#FormReportCountry_reporter').data('ref', '').typeahead('val', '');
        crValidateSubmitBtn(true);
    });
});

function CrSelectCountry (code) {
    $('.chart-item-data, .chart-item, .chart-item-table').remove();
    CrChartCount = 0;

    $('body').append('<div class="page-loading-ajax" style="display: none;"><div class="chart-loading-text"><span class="fa fa-spinner fa-spin"></span><br /><br />Please wait a moment while we create your report</div></div>');
    $('.page-loading-ajax').fadeIn();

    $.ajax({
        url: siteData.data('url') + '/report/country/' + code,
        type: 'post',
        dataType: 'json',
        data: {
            'method': 'exists',
            'period': $('.chart-period').data('period'),
            'startTime': $('.chart-time').data('start'),
            'endTime': $('.chart-time').data('end'),
            'reports': $('#FormReportCountry_reports').val(),
        },
        success: function (returnData) {
            if (returnData.valid) {
                /*var link = document.createElement('a');
                link.href = returnData.url;
                link.download = returnData.filename;
                document.body.appendChild(link);
                link.click();
                link.remove();*/
                triggerFileDownload(returnData.url, returnData.filename);
                $('.page-loading-ajax').fadeOut(function () {
                    $(this).remove();
                });
            } else if (typeof returnData.error !== 'undefined') {
                CrTriggerCharts();
            }
        }
    });
}

function CrTriggerCharts () {
    var charts = {
        'trade': {},
        'macro': {},
    };

    $('#FormReportCountry_reports option:selected').each(function () {
        var cat = $(this).data('cat');

        if (cat == 'trade') {
            charts[cat][$(this).val()] = {
                'indicator': $(this).val()
            }

        } else if (cat == 'macro') {
            charts[cat][$(this).val()] = {
                'macro': $(this).val()
            }
        }
    });

    var initDownload = true,
        code = $('.report-data').data('reporter');
    for (var group in charts) {
        for (var key in charts[group]) {
            CrChartCount++;
        }
    }
    for (var group in charts) {
        for (var key in charts[group]) {

            charts[group][key]['period'] = $('.chart-period').data('period');
            charts[group][key]['startTime'] = $('.chart-time').data('start');
            charts[group][key]['endTime'] = $('.chart-time').data('end');
            charts[group][key]['view'] = 'data';
            charts[group][key]['reporter'] = code;

            /*switch(charts[group][key]['period']){
                case 'month':
                    charts[group][key]['startTime'] = moment().subtract(3, 'months').format('MMM YYYY');
                    charts[group][key]['endTime'] = moment().add(3, 'months').format('MMM YYYY');
                    break;
                case 'annual':
                    charts[group][key]['startTime'] = moment().subtract(5, 'years').format('MMM YYYY');
                    charts[group][key]['endTime'] = moment().add(1, 'years').format('MMM YYYY');
                    break;
                case 'quarter':
                    charts[group][key]['startTime'] = moment().subtract(9, 'months').format('MMM YYYY');
                    charts[group][key]['endTime'] = moment().add(9, 'months').format('MMM YYYY');
                    break;
            }*/

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
                            CrTriggerDownload();
                        }
                    } else {
                        CrChartCount--;
                        if (CrChartCount == 0) {
                            $('.page-loading-ajax').fadeOut(function () {
                                $(this).remove();
                            });
                            addFlashMessage('danger', 'Sorry, there isn\'t enough data for that country');
                        }
                    }
                }
            });
        }
    }
}

var CrDownload,
    CrLoadingCount;

function CrTriggerDownload () {
    var valid = false,
        loadingCount = 0,
        groups = ['macro', 'trade'];

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
            CrTriggerDownload();
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
                    CrTriggerDownload();
                });
                renderPrintableChart(chart.chartId, 'pdfReport');
            });
        }

    } else if (CrLoadingCount < 1 && CrDownload == 'render') {
        CrDownload = 'done';
        setTimeout(function () {
            CrDownloadReport();
        }, 200);
    }

}

function CrDownloadReport (resend) {
    resend = (typeof resend !== 'undefined' ? resend : 0);

    var groups = ['macro', 'trade'],
        charts = {};

    for (var key in groups) {
        charts[groups[key]] = {};
        $('.chart-group[data-chart-type=' + groups[key] + '] .chart-item-data').each(function (i) {

            if (typeof $(this).data('img') !== 'undefined' && $(this).data('img').length > 0) {
                var group = $(this).parents('.chart-group').data('chart-type');
                switch (group) {
                    case 'macro':
                        charts[group][$(this).data('macro')] = $(this).data();
                        break;

                    case 'trade':
                        charts[group][$(this).data('indicator')] = $(this).data();
                        break;
                }

            }
        });
    }

    $.ajax({
        url: siteData.data('url') + '/report/country/' + $('.report-data').data('reporter'),
        type: 'post',
        dataType: 'json',
        contentType: "application/x-www-form-urlencoded",
        data: {
            'period': $('.chart-period').data('period'),
            'startTime': $('.chart-time').data('start'),
            'endTime': $('.chart-time').data('end'),
            'reports': $('#FormReportCountry_reports').val(),
            'format': $('#FormReportCountry_format').val(),
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
                CrDownloadReport(resend);
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

    /*setTimeout(function () {
        $('.page-loading-ajax').fadeOut(function () {
            $(this).remove();
        });
    }, 60000);*/
}

function crRefreshTypeahead (init) {
    init = (typeof init !== 'undefined' ? init : false);
    var datasets = [],
        reporter = (typeof $('#FormReportCountry_reporter').data('ref') !== 'undefined' ? $('#FormReportCountry_reporter').data('ref') : ''),
        reports = $('#FormReportCountry_reports').val(),
        period = $('.chart-period').data('period');


    datasets.push({
        'selector': '#FormReportCountry_reporter',
        'url': siteData.data('url') + '/autoComplete/reporterTrade?period=' + period + '&term=%QUERY',
    });
    $('.reporter-field-label').html('<span class="fa fa-spinner fa-spin"></span> Recalculating, please wait');

    $('.search-list-container .chart-search-list-reporter').slideUp();
    $('.search-list-container .form-group').addClass('loading');
    filterLists($('.search-list-container .chart-search-list-reporter'), {
        type: 'countryReport',
        reports: reports,
        period: period,
        field: 'FormReportCountry_reporter',
        countryReport: datasets
    });

    if (init) {
        refreshTypeahead('countryReport', datasets, init);
    }
}

function crValidateSubmitBtn (refreshTypeahead) {
    refreshTypeahead = (typeof refreshTypeahead !== 'undefined' ? refreshTypeahead : true);
    var valid = true;
    var reporter = '';

    if ($('#FormReportCountry_reports').val() === null) {
        valid = false;
    }

    if (typeof $('#FormReportCountry_reporter').data('ref') !== 'undefined' && $('#FormReportCountry_reporter').data('ref') != '') {
        reporter = $('#FormReportCountry_reporter').data('ref');
    } else {
        valid = false;
    }

    if (valid) {
        $('.cr-submit-btn').addClass('btn-primary btn-inverse').removeClass('btn-disabled');
    } else {
        $('.cr-submit-btn').addClass('btn-disabled').removeClass('btn-primary btn-inverse');
    }

    if (refreshTypeahead) {
        crRefreshTypeahead();
    }
}