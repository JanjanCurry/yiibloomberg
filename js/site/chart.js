/*
//!*******************************
// INIT
//!*******************************

$(function () {
    loadCharts();

    $(window).resize(function () {
        if (this.resizeTO) {
            clearTimeout(this.resizeTO);
        }
        this.resizeTO = setTimeout(function () {
            $(this).trigger('resizeEnd');
        }, 500);
    });

    //redraw graph when window resize is completed
    $(window).on('resizeEnd', function () {
        var key;
        for (key in chartsLog) {
            toggleChartSeries(key);
            //chartsLog[key].chartObj.draw(chartsLog[key].chartData, chartsLog[key].options);
        }
        $('#home-fav').data('resized', 'false');
    });

    $(document).on('click', '.download-btn', function () {
        var chartId = $(this).parents('.chart-item').data('chart-id');
        if (typeof chartId !== 'undefined') {
            renderPrintableChart(chartId, true);
        }
    });

    $(document).on('click', '.chart-group-png', function () {
        var chartId;
        $(this).parents('.chart-group').find('.chart-item').each(function () {
            chartId = $(this).data('chart-id')
            renderPrintableChart(chartId, false, false);
        });
        renderPrintableChart(chartId, true, true);
    });


    $(document).on('click', '.csv-btn', function () {
        downloadCSV($(this).parents('.chart-item').data('chart-id'));
    });

    $(document).on('click', '.chart-group-csv', function () {
        var chartIds = [];
        $(this).parents('.chart-group').find('.chart-item').each(function () {
            chartIds.push($(this).data('chart-id'));
        });
        downloadCSV(chartIds);
    });

    $(document).on('click', '.chart-btn-remove', function () {
        var chartId = $(this).parents('.chart-item').data('chart-id');
        $('.chart-item-data[data-chart-id=' + chartId + ']').remove();
        $('.chart-item[data-chart-id=' + chartId + '], .chart-item-table[data-chart-id=' + chartId + ']').slideUp(function () {
            $(this).remove();
        });

        if ($('.chart-item-data').length === 0) {
            $('.chart-group-empty').slideDown();
        }
    });

    $(document).on('click', '.chart-group-remove', function () {
        $(this).parents('.chart-group').find('.chart-item').each(function () {
            var chartId = $(this).data('chart-id');
            $('.chart-item-data[data-chart-id=' + chartId + ']').remove();
            $('.chart-item[data-chart-id=' + chartId + '], .chart-item-table[data-chart-id=' + chartId + ']').slideUp(function () {
                $(this).remove();
            });
        });
        if ($('.chart-item-data').length === 0) {
            $('.chart-group-empty').slideDown();
        }
    });

    $(document).on('click', '.chart-btn-reset', function () {
        var chartId = $(this).parents('.chart-item').data('chart-id');
        $('.chart-item-data[data-chart-id=' + chartId + ']').data('compare', '').data('start-time', '').data('end-time', '');
        drawChart(chartId);
    });

    $(document).on('click', '.chart-group-reset', function () {
        $(this).parents('.chart-group').find('.chart-item').each(function () {
            var chartId = $(this).data('chart-id');
            $('.chart-item-data[data-chart-id=' + chartId + ']').data('compare', '').data('start-time', '').data('end-time', '');
            drawChart(chartId);
        });
    });

    $(document).on('click', '.chart-group-change-date', function () {
        $('#chartTimeModal').modal('show');
        $('#chartTimeModal').data('chart-group', 1);
    });

    $(document).on('click', '.chart-notes-btn', function () {
        var chartId = $(this).parents('.chart-item-table').data('chart-id');
        $(this).parents('.chart-item-table').find('.chart-notes').slideToggle();
        $('.chart-item[data-chart-id=' + chartId + '] .chart-notes').slideToggle();
        $('.chart-item[data-chart-id=' + chartId + '] .chart-notes').slideToggle();
    });

    $(document).on('click', '.chart-legend-item', function () {
        if ($(this).hasClass('active')) {
            $(this).removeClass('active');
        } else {
            $(this).addClass('active');
        }

        if ($(this).parents('.chart-item-legend').find('.chart-legend-item.active').length > 0) {
            toggleChartSeries($(this).parents('.chart-item').data('chart-id'));
        } else {
            $(this).addClass('active');
        }
    });

    $('.chart-period .btn').click(function () {
        var period = $(this).data('period');

        $('.chart-period .btn').addClass('btn-default').removeClass('btn-primary');
        $(this).addClass('btn-primary');
        $(this).parents('.chart-period').data('period', period);

        $('.chart-time a span').hide();
        $('.chart-time a span.'+period).show();

        $(this).parents('.chart-group').find('.chart-item-data').each(function () {
            var chartId = $(this).data('chart-id');
            $(this).data('period', period);
            drawChart(chartId);
        });

    });

});


function toggleChartSeries (chartId, updatePrint) {
    updatePrint = (typeof updatePrint !== 'undefined' ? updatePrint : false)
    var chart = chartsLog[chartId],
        legend = $('#chart-legend-' + chartId);

    //get chart instances and presets
    viewData = new google.visualization.DataView(chart.chartData);
    viewOptions = $.extend(true, {}, chart.options);
    viewPrintOptions = $.extend(true, {}, chart.printOptions);

    viewPrintOptions.colors = viewOptions.colors = [];
    viewPrintOptions.series = viewOptions.series = {};

    //find out which series to remove
    var i = 0,
    cols = [];
    legend.find('.chart-legend-item').each(function (j) {
        col = parseInt($(this).data('col'));

        if (!$(this).hasClass('active')) {
            cols.push(col);
        } else {
            viewOptions.series[i] =  chart.options.series[(col - 1)];
            viewPrintOptions.series[i] = chart.options.series[(col - 1)];
            i++;
        }
    });
    if(cols.length > 0) {
        viewData.hideColumns(cols);
    }

    //update the charts
    chart.chartObj.draw(viewData, viewOptions);

    //FIX/HACK: Google is reversing the order of the right axis label, drawing the chat a 2nd time corrects it
    if(typeof legend.data('double-draw-hack') !== 'undefined' && legend.data('double-draw-hack') === 'active') {
        chart.chartObj.draw(viewData, viewOptions);
    }else{
        legend.data('double-draw-hack', 'active');
    }

    //update downloadable chart
    if (updatePrint) {;
        if (!chart.drawPrint) {
            var event = google.visualization.events.addListener(chart.chartObjPrint, 'ready', function () {
                chartsLog[chartId].drawPrint = true;
                google.visualization.events.removeListener(event);
                chart.chartObjPrint.draw(viewData, viewPrintOptions);
            });
            chart.chartObjPrint.draw(chart.chartData, chart.printOptions);
        } else {
            chart.chartObjPrint.draw(viewData, viewOptions);
        }
    }

    //save changes
    chartsLog[chartId].viewData = viewData;
    chartsLog[chartId].viewOptions = viewOptions;
    chartsLog[chartId].viewPrintOptions = viewPrintOptions;
}

function loadCharts () {
    google.charts.load('current', {'packages': ['corechart'], 'callback': drawCharts});
    //google.charts.setOnLoadCallback(drawCharts());

    $('body').data('google-load', 0);
    setTimeout(function () {
        if (typeof google !== 'undefined' && typeof google.visualization !== 'undefined' && $('body').data('google-load') === 0) {
            drawCharts();
        }
    }, 15000);
}

//!*******************************
// RENDERING
//!*******************************

function drawCharts () {
    $('body').data('google-load', 1);
    $('.chart-group .chart-group-data .chart-item-data').each(function () {
        if (typeof $(this).data('require-editing') !== 'undefined' && $(this).data('require-editing')) {
            if ($('#chartTradeModal').length > 0) {
                $('#chartTradeModal').modal('show');
            }
            if ($('#chartMacroModal').length > 0) {
                $('#chartMacroModal').modal('show');
            }
            if ($('#chartMarketModal').length > 0) {
                $('#chartMarketModal').modal('show');
            }
        } else {
            drawChart($(this).data('chart-id'));
        }
    });

    $('.content .modal').each(function(){
        $(this).appendTo($('body'));
    });
}

var chartDrawCount = 0;

function drawChart (chartId, showLoading) {
    var data = $('.chart-item-data[data-chart-id=' + chartId + ']'),
        group = data.parents('.chart-group');
    chartDrawCount++;

    showLoading = (typeof showLoading !== 'undefined' ? showLoading : true);
    if (showLoading) {
        renderChartLoading(group);
    }

    var url = '/trade/chart/';
    if (typeof group.data('chart-type') !== 'undefined') {
        url = '/'+group.data('chart-type')+'/chart/';
    }

    data = data.data();
    data.startTime = $('#ctmStartDate').val();
    data.endTime = $('#ctmEndDate').val();

    $.ajax({
        url: siteData.data('url') + url,
        type: 'post',
        dataType: 'json',
        contentType: "application/x-www-form-urlencoded",
        data: data,
        success: function (returnData) {
            if (returnData.valid) {
                //set chart order
                var chartPosition = $('.chart-item-data').length;
                $('.chart-item-data').each(function (i) {
                    $(this).data('chart-position', i);
                    if ($(this).data('chart-id') === chartId) {
                        chartPosition = i;
                    }
                });

                //print charts in correct order
                if (!group.find('.chart-item[data-chart-id=' + chartId + ']').length) {
                    //var html = '<div class="chart-item ' + returnData.chart.col + '" data-chart-id="' + chartId + '"><div class="chart-item-header-container" data-chart-id="' + chartId + '"></div><div class="chart-item-chart-container"><div class="chart-item-chart" id="' + chartId + '"></div><div class="chart-item-print-container"><div class="chart-item-legend-print" id="chart-legend-print-' + chartId + '"></div><div class="chart-item-print" id="' + chartId + '-print"></div></div></div><p class="chart-notes"></p></div>',

                    var html = '<div class="chart-item ' + returnData.chart.col + '" data-chart-id="' + chartId + '"><div class="chart-item-header-container" data-chart-id="' + chartId + '"></div><div class="chart-item-chart-container"><div class="chart-item-chart" id="' + chartId + '"></div></div><p class="chart-notes"></p></div>',
                        written = false;

                    if($('.chart-print').length === 0){
                        $('body').append('<div class="chart-print"></div>');
                    }
                    if($('.chart-legend-print-' + chartId).length === 0) {
                        $('.chart-print').append('<div class="chart-print-item"><div class="chart-print-legend" id="chart-print-legend-' + chartId + '"></div><div class="chart-print-chart-box"><div class="chart-print-chart" id="' + chartId + '-print"></div></div></div>');
                    }

                    if (group.find('.chart-item').length > 0) {
                        group.find('.chart-item').each(function (i) {
                            if (!written) {
                                var data = $('.chart-item-data[data-chart-id=' + $(this).data('chart-id') + ']');
                                if (typeof data !== 'undefined') {
                                    if (parseInt(data.data('chart-position')) === (chartPosition - 1)) {
                                        written = true;
                                        $(this).after(html);
                                    } else if (parseInt(data.data('chart-position')) === (chartPosition + 1)) {
                                        written = true;
                                        $(this).before(html);
                                    }
                                }
                            }
                        });
                    }
                    if (!written) {
                        group.find('.chart-group-chart').append(html);
                    }
                }

                //print tables in correct order
                if (!group.find('.chart-item-table[data-chart-id=' + chartId + ']').length) {
                    var html = '<div class="chart-item-table" data-chart-id="' + chartId + '"></div>',
                        written = false;

                    if (group.find('.chart-item-table').length > 0) {
                        group.find('.chart-item-table').each(function (i) {
                            if (!written) {
                                var data = $('.chart-item-data[data-chart-id=' + $(this).data('chart-id') + ']');
                                if (typeof data !== 'undefined') {
                                    if (parseInt(data.data('chart-position')) === (chartPosition - 1)) {
                                        written = true;
                                        $(this).after(html);
                                    } else if (parseInt(data.data('chart-position')) === (chartPosition + 1)) {
                                        written = true;
                                        $(this).before(html);
                                    }
                                }
                            }
                        });
                    }
                    if (!written) {
                        group.find('.chart-group-table').append(html);
                    }
                }


                //show errors
                if (typeof returnData.error !== 'undefined') {
                    for (var key in returnData.error) {
                        addFlashMessage('danger', returnData.error[key]);
                    }
                }

                renderChart(returnData.chart);
                refreshForm();

                loadingCount = parseInt(group.find('.chart-loading').data('count')) - 1;
                group.find('.chart-loading').data('count', loadingCount);
                if (loadingCount < 1) {
                    group.find('.chart-loading').fadeOut(function () {
                        $(this).remove();
                    });
                }
                ;
            } else {
                if (typeof returnData.error !== 'undefined') {
                    for (var key in returnData.error) {
                        addFlashMessage('danger', returnData.error[key]);
                        group.find('.chart-item-table[data-chart-id=' + chartId + ']').remove();
                        group.find('.chart-item[data-chart-id=' + chartId + ']').remove();
                    }
                }

                refreshForm();
                group.find('.chart-loading').fadeOut(function () {
                    $(this).remove();
                });
            }
        },
        error: function (returnData) {
            refreshForm();
            group.find('.chart-loading').fadeOut(function () {
                $(this).remove();
            });
        }
    });
}

function renderChart (chart) {
    var data = $('.chart-item-data[data-chart-id=' + chart.chartId + ']'),
        group = data.parents('.chart-group');

    //add non-chart data
    group.find('.chart-item-table[data-chart-id=' + chart.chartId + ']').html(chart.table);
    group.find('.chart-item-header-container[data-chart-id=' + chart.chartId + ']').html(chart.title);
    data.data('title', group.find('.chart-item-header-container[data-chart-id=' + chart.chartId + '] .chart-item-title').attr('title'));
    data.data('url', chart.url);

    if (typeof chart.notes !== 'undefined' && !$.isEmptyObject(chart.notes)) {
        notesArr = $.map(chart.notes, function (e) {
            return e;
        });
        $('.chart-item[data-chart-id=' + chart.chartId + '] .chart-notes').html('<small>' + notesArr.join('<br />') + '</small>');
        data.data('notes', chart.notes);
    } else {
        data.data('notes', '');
    }

    data.data('chart-type', chart.chartType);

    //decide on number of labels to show based on number of data available
    var labelCount = 'automatic';
    if (chart.chartData.rows.length > 48) {
        labelCount = 12;
    } else if (chart.chartData.rows.length > 30) {
        labelCount = 6;
    } else if (chart.chartData.rows.length > 20) {
        labelCount = 3;
    } else if (chart.chartData.rows.length > 12) {
        labelCount = 2;
    }

    var legend = 'left',
        showlegend = group.data('show-legend');
    if (typeof group.data('show-legend') !== 'undefined' && group.data('show-legend') === 1) {
        legend = 'right';
    } else {
        //group.find('.chart-item-chart').addClass('chart-no-legend');
    }


    //set chart options
    var options = {
        sliceVisibilityThreshold: 0,
        chart: {
            title: chart.title
        },
        colors: [],
        series: {
            //0: {color: chart.chartData.cols[1].color}
        },
        hAxis: {
            title: chart.axis['x'].label,
            format: 'short',
            maxAlternation: 1,
            maxTextLines: 1,
            showTextEvery: labelCount,
            slantedText: false
        },
        legend: 'none',
        /!*legend: {
            position: 'top',
            alignment: 'center',
            maxLines: 2
        },*!/
        curveType: 'function',
        animation: {
            startup: true,
            duration: 1000,
            easing: 'out'
        },
        titleTextStyle: {
            fontName: 'Verdana'
        },
        chartArea: {
            left: '10%',
            width: '85%',
        },
        backgroundColor: group.css('background-color')
    };

    if (getBreakpoint() === 'xs') {
        options.hAxis.slantedText = true;
    }

    if ($.isArray(chart.axis['y']) && chart.axis['y'].length > 1) {
        options.chartArea.width = '80%';
        options.vAxes = [
            {
                title: chart.axis['y'][0].label,
                format: 'short'
            },
            {
                title: chart.axis['y'][1].label,
                format: 'short',
            },
        ];
    } else if ($.isArray(chart.axis['y'])) {
        options.vAxis = {
            title: chart.axis['y'][0].label,
            format: 'short'
        };
    } else {
        options.vAxis = {
            title: chart.axis['y'].label,
            format: 'short'
        };
    }

    if (group.parents('.carousel ').length > 0) {
        //options.chartArea.width = '75%';
    }

    //set country colors
    var key;
    for (key in chart.chartData.cols) {
        if (key > 0) {
            var subOptions = {},
                valid = false;
            if (typeof chart.chartData.cols[key].color !== 'undefined') {
                valid = true;
                subOptions['color'] = chart.chartData.cols[key].color;
                //options.colors.push(chart.chartData.cols[key].color);
            }
            if (typeof chart.chartData.cols[key].note !== 'undefined') {
                chart.chartData.cols[key]['label'] += chart.chartData.cols[key].note;
            }
            if (typeof chart.chartData.cols[key].targetAxisIndex !== 'undefined') {
                valid = true;
                subOptions['targetAxisIndex'] = chart.chartData.cols[key].targetAxisIndex;
            }
            if (typeof chart.chartData.cols[key].role !== 'undefined') {
                valid = true;
                subOptions['role'] = chart.chartData.cols[key].role;
            }

            if (valid) {
                options.series[(key - 1)] = subOptions;
            }
        }
    }



    //set print formatting
    var printOptions = $.extend(true, {}, options);
    //printOptions.legend.position = 'right';
    //printOptions.title = group.find('.chart-item-header-container[data-chart-id=' + chart.chartId + '] .chart-item-title').attr('title');

    //generate Google Chart data
    var chartData = new google.visualization.DataTable(chart.chartData);

    //set chart type
    switch (chart.chartType) {
        case 'area':
            var chartObj = new google.visualization.AreaChart(document.getElementById(chart.chartId));
            var chartObjPrint = new google.visualization.AreaChart(document.getElementById(chart.chartId + '-print'));
            break;

        case 'bar':
        case 'column':
            var chartObj = new google.visualization.ColumnChart(document.getElementById(chart.chartId));
            var chartObjPrint = new google.visualization.ColumnChart(document.getElementById(chart.chartId + '-print'));
            break;

        case 'combo':
            var chartObj = new google.visualization.ComboChart(document.getElementById(chart.chartId));
            var chartObjPrint = new google.visualization.ComboChart(document.getElementById(chart.chartId + '-print'));
            break;

        case 'scatter':
            var chartObj = new google.visualization.ScatterChart(document.getElementById(chart.chartId));
            var chartObjPrint = new google.visualization.ScatterChart(document.getElementById(chart.chartId + '-print'));
            break;

        case 'line':
        default:
            var chartObj = new google.visualization.LineChart(document.getElementById(chart.chartId));
            var chartObjPrint = new google.visualization.LineChart(document.getElementById(chart.chartId + '-print'));
    }

    //set printable image url


    //render and log the chart
    chartObj.draw(chartData, options);

    chartsLog[chart.chartId] = {
        'chartId': chart.chartId,
        'chartData': chartData,

        'chartObj': chartObj,
        'options': options,

        'chartObjPrint': chartObjPrint,
        'printOptions': printOptions,

        'drawChart': true,
        'drawPrint': false,

        'viewData': new google.visualization.DataView(chartData),
        'viewOptions': options,
        'viewPrintOptions': printOptions,
    };

    //redrawn lines - fix for forecast colors
    toggleChartSeries(chart.chartId, true);

    setTimeout(function(){
        tourAfterDrawn();
    }, 1000);
}

function renderChartLoading (group) {
    if (group.find('.chart-loading').length === 0) {
        group.append('<div class="chart-loading" data-count="1"><div class="chart-loading-text"><span class="fa fa-spinner fa-spin"></span><br /><br />Please wait a moment while we process your data</div></div>');
        group.find('.chart-loading').fadeIn();
    } else {
        var loadingCount = parseInt(group.find('.chart-loading').data('count'));
        group.find('.chart-loading').data('count', (loadingCount + 1));
    }
}


function renderPrintableChart (chartId, downloadImage, downloadMultiImage, shareImage) {
    downloadImage = (typeof downloadImage !== 'undefined' ? downloadImage : false);
    downloadMultiImage = (typeof downloadMultiImage !== 'undefined' ? downloadMultiImage : false);
    shareImage = (typeof shareImage !== 'undefined' ? shareImage : false);

    var chart = chartsLog[chartId];

    if (!chart.drawPrint) {
        var event = google.visualization.events.addListener(chart.chartObjPrint, 'ready', function () {
            chartsLog[chartId].drawPrint = true;
            google.visualization.events.removeListener(event);
            renderPrintableChart(chartId, downloadImage, downloadMultiImage, shareImage);
        });
        chart.chartObjPrint.draw(chart.chartData, chart.printOptions);
        return false;
    }

    var scrollPos;
    var event = google.visualization.events.addListener(chart.chartObjPrint, 'ready', function () {
        var data = $('.chart-item-data[data-chart-id=' + chartId + ']');

        //generate chart image
        var imgUrl = chart.chartObjPrint.getImageURI();
        $('.chart-item[data-chart-id=' + chart.chartId + '] .download-btn').data('img', imgUrl);
        data.data('img', imgUrl);

        //generate legend image
        var legend = $('#chart-legend-' + chart.chartId),
            newLegend = $('#chart-print-legend-' + chart.chartId),
            printItem = newLegend.parents('.chart-print-item');

        newLegend.html(legend.html());
        scrollPos = $(document).scrollTop();

        $(window).scrollTop(0);
        printItem.addClass('chart-print-processing');
        //positionFooter();
        html2canvas(newLegend, {
            onrendered: function (canvas) {
                printItem.removeClass('chart-print-processing');
                //positionFooter();
                //fix html2canvas scrolling to top of page on render
                $('html, body').scrollTop(scrollPos);
                //output the legend
                var legendImg = canvas.toDataURL("image/png");
                newLegend.html('<img src="' + legendImg + '" />');
                data.data('legend', legendImg);

                //optionally, download the image
                if (downloadImage) {
                    downloadChartImage(chartId, downloadMultiImage, shareImage);
                }
            }
        });

        chartDrawCount--;
        google.visualization.events.removeListener(event);
    });


    //toggleChartSeries(chartId, true);
    chart.chartObjPrint.draw(chart.viewData, chart.viewPrintOptions);
}

function downloadChartImage (chartId, multiImage, shareImage) {
    var chart = chartsLog[chartId],
        data = $('.chart-item-data[data-chart-id=' + chartId + ']');

    if (shareImage) {
        $('#chartShareModal').modal('show');
        //refer a single image to AddThis for sharing
        $.ajax({
            url: siteData.data('url') + '/site/downloadImage/',
            type: 'post',
            dataType: 'json',
            contentType: "application/x-www-form-urlencoded",
            data: data.data(),
            success: function (returnData) {
                if (returnData.valid) {
                    $('#chartShareModal #FormShare_image').val(returnData.url);

                    if (typeof addthis !== 'undefined') {
                        if (returnData.shortUrl.length > 0) {
                            addthis.update('share', 'url', returnData.shortUrl);
                            addthis.update('share', 'media', returnData.shortUrl);
                        } else {
                            addthis.update('share', 'url', returnData.url);
                            addthis.update('share', 'media', returnData.url);
                        }
                        addthis.update('share', 'title', $('.chart-item[data-chart-id=' + chartId + '] .chart-item-title').text());
                        addthis.toolbox(".addthis_toolbox");
                    }
                }
            }
        });

    } else if (multiImage) {

        //download an entire chart group
        var group = data.parents('.chart-group'),
            charts = {};

        var valid = true;
        group.find('.chart-item-data').each(function (i) {
            if (typeof $(this).data('img') !== 'undefined' && $(this).data('img').length > 0) {
                charts[i] = $(this).data();
            } else {
                valid = false;
            }
        });

        if (valid && !$.isEmptyObject(charts)) {
            $.ajax({
                url: siteData.data('url') + '/site/downloadAllImage/',
                type: 'post',
                dataType: 'json',
                contentType: "application/x-www-form-urlencoded",
                data: {
                    'charts': charts,
                    'type': group.data('chart-type')
                },
                success: function (returnData) {
                    if (returnData.valid) {
                        triggerFileDownload(returnData.url, returnData.filename);
                    }
                }
            });
        }

    } else {
        //download a single chart
        $.ajax({
            url: siteData.data('url') + '/site/downloadImage/',
            type: 'post',
            dataType: 'json',
            contentType: "application/x-www-form-urlencoded",
            data: data.data(),
            success: function (returnData) {
                if (returnData.valid) {
                    triggerFileDownload(returnData.url, returnData.filename);
                }
            }
        });

    }
}

function triggerFileDownload (url, filename) {
    var download = document.createElement('a');
    download.href = url;
    download.download = filename;
    //download.target = '_blank';


    /!*var downloading = chrome.downloads.download({
        url : url,
        filename : filename
        //conflictAction : 'uniquify'
    });*!/
    document.body.appendChild(download);
    download.click();
    download.remove();

    /!*if (document.createEvent) {
        var evObj = document.createEvent('MouseEvents');
        evObj.initEvent('click', true, false);
        download.dispatchEvent(evObj);
    } else if (document.createEventObject) {
        var evObj = document.createEventObject();
        download.fireEvent('onclick', evObj);
    } else {
        document.body.appendChild(download);
        download.click();
        download.remove();
    }*!/
}

function resizeTables () {

    $('.chart-item-table').each(function () {
        $(this).find('thead th').each(function () {

        });
    });
}

//!*******************************
// COMMON FORM TOOLS
//!*******************************
$(function () {

    $('.modal').on('hidden.bs.modal', function (e) {
        $('.chart-search-list').slideUp();
    });

    $('.chart-modal').click(function (e) {
        if ($(e.target).parents('.chart-search-list, .toggleBtn').length > 0 || $(e.target).hasClass('toggleBtn')) {
        }else{
            $('.chart-search-list').slideUp();
        }
    })

    $(document).on('click', '.form-typeahead .toggleBtn', function () {
        var modal = $(this).parents('.modal, .search-list-container'),
            listTarget = modal.find('.' + $(this).data('toggle')),
            fieldId = $(this).parents('.form-typeahead').find('input').attr('id'),
            chartId = modal.data('chart-id');

        //update selected list items from the form
        modal.find('.chart-search-list .list-group-item-selected').removeClass('list-group-item-disabled list-group-item-selected');
        modal.find('.form-typeahead input').each(function () {
            if (typeof $(this).data('ref') !== 'undefined' && $(this).data('ref') !== '') {
                modal.find('.chart-search-list .list-group-item[data-ref=' + $(this).data('ref') + ']').addClass('list-group-item-disabled list-group-item-selected');
            }
        });
        modal.find('.chart-search-list').each(function () {
            if(typeof $(this).data('select-disable') !== 'undefined') {
                restrict = $(this).data('select-disable');
                for(var key in restrict){
                    modal.find('.chart-search-list .list-group-item[data-ref=' + restrict[key] + ']').addClass('list-group-item-disabled list-group-item-selected');
                }
            }
        });


        //update selected list items from the chart data
        if (typeof chartId !== 'undefined') {
            var data = $('.chart-item-data[data-chart-id=' + chartId + ']').data();
            if (typeof data !== 'undefined') {

                var types = ['reporter', 'partner', 'sector', 'commodity', 'currency', 'equity'];

                for (var key in types) {
                    var type = types[key];
                    if (listTarget.is('.chart-search-list-' + type) && typeof data[type] !== 'undefined' && data[type] !== '') {
                        var parts = data[type].split(',');
                        if (parts.length > 0) {
                            for (var i = 0; i < parts.length; i++) {
                                if (typeof parts[i] !== 'undefined' && parts[i] !== '') {
                                    var item = modal.find('.chart-search-list .list-group-item[data-ref=' + parts[i] + ']');
                                    if (!item.is('.list-group-item-selected')) {
                                        if (modal.is('.chart-modal-compare')) {
                                            item.addClass('list-group-item-disabled list-group-item-selected');
                                        } else {
                                            item.addClass('list-group-item-selected').removeClass('list-group-item-disabled');
                                        }
                                    }
                                }
                            }
                        }
                    }
                }

                switch (type) {
                    case 'reporter':
                    case 'commodity':
                    case 'currency':
                    case 'equity':
                        var compare = type;
                        break;

                    default:
                        var compare = '';
                }

                if (compare !== '') {
                    //updated selected items from compare value
                    if (listTarget.is('.chart-search-list-' + compare) && typeof data['compare'] !== 'undefined' && data['compare'] !== '') {
                        var parts = data['compare'].split(',');
                        if (parts.length > 0) {
                            for (var i = 0; i < parts.length; i++) {
                                if (typeof parts[i] !== 'undefined' && parts[i] !== '') {
                                    modal.find('.chart-search-list .list-group-item[data-ref=' + parts[i] + ']').addClass('list-group-item-disabled list-group-item-selected');
                                }
                            }
                        }
                    }
                }
            }
        }

        listTarget.data('field', fieldId);
        setTimeout(function(){
            modal.animate({
                scrollTop: listTarget.offset().top - $(document).scrollTop()
            }, 500);
        },500)
    });

    $(document).on('click', '.chart-search-list .list-group-item', function () {
        if (!$(this).is('.list-group-item-disabled')) {
            var field = $('#' + $(this).parents('.chart-search-list').data('field'));
            field.data('ref', $(this).data('ref'));
            field.typeahead('val', $(this).data('label'));
            $(this).parents('modal').animate({
                scrollTop: 0
            }, 500);
            $('.chart-search-list').slideUp();
            validateSubmitBtn($(this).parents('.modal, .search-list-container').data('type'));
        }
    });

    $('.form-typeahead .clearBtn').click(function () {
        $(this).parents('.form-group').find('input').data('ref', '').typeahead('val', '');
        validateSubmitBtn($(this).parents('.modal, .search-list-container').data('type'));
    });
})

function addChart (group, data) {
    var key = '',
        dataStr = '';
    for (key in data) {
        dataStr += ' data-' + key + '="' + data[key] + '"';
    }
    group.find('.chart-group-data').append('<div class="chart-item-data"' + dataStr + '></div>');
}

function filterLists (target, vars, type) {
    type = (typeof type !== 'undefined' ? type : 'list');
    $.ajax({
        url: siteData.data('url') + '/site/filterList/',
        data: vars,
        dataType: 'json',
        method: 'post',
        success: function (returnData) {
            if (returnData.valid && typeof returnData.list !== 'undefined') {
                switch (type) {
                    case 'dropdown':
                        target.find('option').prop('disabled', true);
                        for (var key in returnData.list) {
                            var available = true;
                            if (typeof returnData.hide !== 'undefined' && $.inArray(returnData.list[key], returnData.hide) !== -1) {
                                available = false;
                            }
                            if(available) {
                                target.find('option[value=' + returnData.list[key] + ']').prop('disabled', false);
                            }
                        }
                        target.find('option:first').prop('disabled', false);
                        target.selectpicker('refresh');
                        break;

                    case 'list':
                        target.find('.chart-search-list-subgroup').hide();
                        target.find('.list-group-item').each(function () {
                            var ref = $(this).data('ref');
                            if(typeof ref !== 'string'){
                                if(ref < 1000){
                                    ref = '0'+ref;
                                }
                                ref = ref.toString();
                            }

                            var available = false,
                                hide = false;
                            if (Object.values(returnData.list).indexOf(ref) > -1) {
                                available = true;
                            }
                            if (typeof returnData.hide !== 'undefined' && $.inArray(ref, returnData.hide) !== -1) {
                                hide = true;
                                available = false;
                            }

                            if(available && !hide){
                                $(this).parents('.chart-search-list-subgroup').show();
                                $(this).removeClass('list-group-item-disabled').show();
                            }else if (!available && hide){
                                $(this).addClass('list-group-item-disabled').hide();
                            }else{
                                $(this).parents('.chart-search-list-subgroup').show();
                                $(this).addClass('list-group-item-disabled').show();
                            }
                        });
                        if(typeof vars['field'] !== 'undefined' && $('#' + vars['field']).length > 0) {
                            $('#' + vars['field']).data('filter', returnData.list);
                        }
                        if(typeof vars.selected !== 'undefined'){
                            target.data('select-disable', vars.selected);
                        }

                        if(typeof vars['countryReport'] !== 'undefined') {
                            vars['countryReport'][0]['url'] = vars['countryReport'][0]['url']+'&filter='+returnData.list.join(',');
                            if($('#reporter').data('ref') !== 'undefined' && $('#reporter').data('ref') !== '' && !(returnData.list.indexOf($('#reporter').data('ref')) > -1)){
                                $('#reporter').data('ref','').typeahead('val', '');
                            }
                            crValidateSubmitBtn(false);
                            refreshTypeahead('countryReport', vars['countryReport'], false);
                            //$('.country-count').text($('.list-group-item:not(.list-group-item-disabled)').length);
                            var count = $('.list-group-item:not(.list-group-item-disabled)').length;
                            if(count === 0){
                                $('.reporter-field-label').html('No countries available, select different indicators');
                                $('.search-list-container .form-group').addClass('disabled');
                            }else{
                                $('.reporter-field-label').html('Select a country (<span class="country-count">'+$('.list-group-item:not(.list-group-item-disabled)').length+'</span> Available)');
                                $('.search-list-container .form-group').removeClass('disabled');
                            }
                            $('.search-list-container .form-group').removeClass('loading');
                        }
                        /!*for (var key in returnData.list) {
                            target.find('.list-group-item[data-ref=' + returnData.list[key] + ']').removeClass('list-group-item-disabled');
                        }*!/
                        break;
                }
            } else {
                switch (type) {
                    case 'dropdown':
                        target.find('option').prop('disabled', false);
                        target.selectpicker('refresh');
                        break;

                    case 'list':
                        target.find('.list-group-item').removeClass('list-group-item-disabled');
                        break;
                }
            }
        }
    });
}

function refreshTypeahead (type, datasets, init) {
    init = (typeof init !== 'undefined' ? init : false);

    for (var key in datasets) {
        var dataset = datasets[key];
        $(dataset['selector']).each(function () {
            var val = $(this).typeahead('val'),
                ref = (typeof $(this).data('ref') !== 'undefined' ? $(this).data('ref') : '');

            if (!init) {
                $(this).data('ref', '').typeahead('val', '').typeahead('destroy');
            }

            dataset['bloodhound'] = new Bloodhound({
                datumTokenizer: Bloodhound.tokenizers.obj.whitespace('value'),
                queryTokenizer: Bloodhound.tokenizers.whitespace,
                remote: {
                    url: dataset['url'],
                    wildcard: '%QUERY'
                },
                sufficient: 10
            });
            dataset['bloodhound'].initialize();
            $(this).typeahead({
                hint: false,
                highlight: true
            }, {
                limit: 10,
                name: 'search',
                displayKey: 'value',
                source: dataset['bloodhound'].ttAdapter()
            });

            $(this).typeahead('val', val);
            $(this).data('ref', ref);
        });

        if (init) {
            $(dataset['selector']).on("typeahead:selected", function (object, datum, name) {
                $(this).data('ref', datum.code);
                validateSubmitBtn(type);
            }).on('keyup', function (e) {
                if ($(this).typeahead('val') === '') {
                    $(this).data('ref', '');
                    validateSubmitBtn(type);
                } else if (e.which === 13 && $(this).parents('.form-group').find(".tt-suggestion:first-child").length > 0) {
                    $(this).parents('.form-group').find(".tt-suggestion:first-child").trigger('click');
                }
            });
        }
    }

    toggleClearBtns();
}

function toggleClearBtns () {
    $('.form-typeahead input').each(function () {
        if ($(this).val() !== '') {
            $(this).parents('.form-group').find('.toggleBtn').hide();
            $(this).parents('.form-group').find('.clearBtn').show();
        } else {
            $(this).parents('.form-group').find('.clearBtn').hide();
            $(this).parents('.form-group').find('.toggleBtn').show();
        }
    });
}

function validateSubmitBtn (type) {
    switch (type) {
        case 'countryReport':
            crValidateSubmitBtn();
            break;

        case 'market':
        case 'commodity':
        case 'currency':
        case 'equity':
            marketValidateSubmitBtn();
            break;

        case 'macro':
            macroValidateSubmitBtn();
            break;

        case 'trade':
            tradeValidateSubmitBtn();
            break;
    }

    toggleClearBtns();
}


//!*******************************
// FAVORITES
//!*******************************
$(function () {
    $(document).on('click', '.toggle-favorite', function () {
        var btn = $(this),
            chartId = btn.parents('.chart-item').data('chart-id'),
            type = btn.data('type');

        if (typeof chartId !== 'undefined') {
            var data = $('.chart-item-data[data-chart-id=' + chartId + ']').data();
            data['favorite-type'] = type;
            data['img'] = '';

            $.ajax({
                url: siteData.data('url') + '/user/toggleFavorites/',
                data: data,
                dataType: 'json',
                method: 'post',
                success: function (returnData) {
                    if (returnData.valid) {
                        if (returnData.action === 'add') {
                            btn.html('<span class="fa fa-star"></span> Remove Favorite');
                            btn.parents('.chart-item-header').find('.chart-item-title').prepend('<span class="fa fa-star"></span>');
                        } else {
                            btn.html('<span class="fa fa-star"></span> Add Favorite');
                            btn.parents('.chart-item-header').find('.chart-item-title .fa-star').remove();
                            if (btn.parents('.user-favorite').length > 0) {
                                btn.parents('.user-favorite').slideUp(function () {
                                    $(this).remove();
                                });
                            }
                        }
                    }
                }
            });
        }
    });
});


function downloadCSV (chartId) {
    var data = {},
        group = $('.chart-group:first');

    if ($.isArray(chartId)) {
        $.each(chartId, function (key, val) {
            var chartData = $('.chart-item-data[data-chart-id=' + val + ']'),
                group = chartData.parents('.chart-group');

            data[val] = chartData.data();
            data[val]['img'] = '';
        });
    } else { 
        var chartData = $('.chart-item-data[data-chart-id=' + chartId + ']'),
            group = chartData.parents('.chart-group');

        data[chartId] = chartData.data();
        data[chartId]['img'] = '';
    }

    var url = '/trade/downloadCsv/';
    if (typeof group.data('chart-type') !== 'undefined') {
        url = '/'+group.data('chart-type')+'/downloadCsv/';
    }

    $.ajax({
        url: siteData.data('url') + url,
        type: 'post',
        dataType: 'json',
        data: data,
        success: function (returnData) {
            if (returnData.valid) {
                triggerFileDownload(returnData.url, returnData.filename);
                /!*var link = document.createElement('a');
                link.href = returnData.url;
                link.download = returnData.filename;
                document.body.appendChild(link);
                link.click();
                link.remove();*!/
            }
        }
    });
}

*/
