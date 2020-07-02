$(function(){
    loadCharts();
});

function loadCharts () {
    google.charts.load('current', {'packages': ['corechart'], 'callback': drawChart});
    //google.charts.setOnLoadCallback(drawCharts());

    $('body').data('google-load', 0);
    setTimeout(function () {
        if (typeof google !== 'undefined' && typeof google.visualization !== 'undefined' && $('body').data('google-load') === 0) {
            drawChart();
        }
    }, 15000);
}

function drawChart () {
    $('body').data('google-load', 1);

}

function drawChart (showLoading) {
    var chartId = 'chart-bom'
        group = data.parents('.chart-group');

    showLoading = (typeof showLoading !== 'undefined' ? showLoading : true);
    if (showLoading) {
        renderChartLoading(group);
    }


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
        /*legend: {
            position: 'top',
            alignment: 'center',
            maxLines: 2
        },*/
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

    //chart width for dual axix
    if ($.isArray(chart.axis['y']) && chart.axis['y'].length > 1) {
        options.chartArea.width = '80%';
        options.vAxes = [
            {
                title: chart.axis['y'][0].label,
                //format: 'short'
                format: '#,###.##'
            },
            {
                title: chart.axis['y'][1].label,
                //format: 'short',
                format: '#,###.##'
            },
        ];

        //chart width for ????
    } else if ($.isArray(chart.axis['y'])) {
        options.vAxis = {
            title: chart.axis['y'][0].label,
            format: 'short'
        };
        //chart width for single axix
    } else {
        options.vAxis = {
            title: chart.axis['y'].label,
            format: '#,###.##'
        };
    }

    //chart width for favorites
    if (group.parents('.carousel ').length > 0) {
        //dual axis
        if ($.isArray(chart.axis['y']) && chart.axis['y'].length > 1) {
            options.chartArea.width = '80%';

            //single axis
        }else {
            options.chartArea.width = '100%';
        }
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
    printOptions.backgroundColor = '#FFFFFF';
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