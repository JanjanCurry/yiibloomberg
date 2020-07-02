//*******************************
// CHANGE DATE
//*******************************
$(function () {

    refreshChartDatepicker($('.chart-item:first'));

    $('#chartTimeModal').on('show.bs.modal', function (e) {
        //reset indicator form
        //$('#ctmStartDate').val('');
        //$('#ctmEndDate').val('');

        if (typeof e.relatedTarget !== 'undefined' && $(e.relatedTarget).parents('.chart-item').length > 0) {
            var chart = $(e.relatedTarget).parents('.chart-item');
            $('#chartTimeModal').data({
                'chart-id': chart.data('chart-id'),
                'chart-group': 0,
            });
        } else {
            var chart = $('.chart-item:first');
            $('#chartTimeModal').data({
                'chart-id': '',
                'chart-group': 1,
            });
        }

        refreshChartDatepicker(chart);
    });

    $('#ctmStartDate, #ctmEndDate').on('dp.change', function (e) {
        //toggle indicator submit button
        if ($(this).val() != '') {
            $('#chartTimeModal .modal-body .btn').addClass('btn-primary btn-inverse').removeClass('btn-disabled');
        } else {
            $('#chartTimeModal .modal-body .btn').addClass('btn-disabled').removeClass('btn-primary btn-inverse');
        }
    });

    $('#chartTimeModal .chart-submit-btn').click(function () {
        var btn = $(this),
            chartId = btn.parents('.modal').data('chart-id'),
            start = moment($('#ctmStartDate').val(), 'MMM YYYY'),
            end = moment($('#ctmEndDate').val(), 'MMM YYYY'),
            period = 'month';

        if(start > end){
            var temp = start;
            start = end;
            end = temp;
        }

        if($('.chart-period').length && typeof $('.chart-period').data('period') !== 'undefined'){
            period = $('.chart-period').data('period');
        }

        if ($('#chartTimeModal').data('chart-group') == 1) {
            $('.chart-item').each(function () {
                var chartId = $(this).data('chart-id');
                $('.chart-item-data[data-chart-id=' + chartId + ']').data('start-time', start.format('MMM YYYY'));
                $('.chart-item-data[data-chart-id=' + chartId + ']').data('end-time', end.format('MMM YYYY'));
                $('.chart-item-data[data-chart-id=' + chartId + ']').data('session', 'set');
                drawChart(chartId);
            });

            var formats = {
                'month': 'MMM YYYY',
                'quarter': 'YYYY [Q]Q',
                'annual': 'YYYY',
            };
            for(var key in formats){
                $('.chart-time .'+key).text(start.format(formats[key]) + ' - ' + end.format(formats[key]));
            }

            btn.parents('.modal').modal('hide');
            $('.chart-time, #chartTimeModal').data('start',start.format('MMM YYYY')).data('end',end.format('MMM YYYY'));

        } else if (!$('#chartTimeModal .modal-body .btn').hasClass('btn-disabled')) {
            //update all charts in
            if(typeof chartId !== 'undefined') {
                $('.chart-item-data[data-chart-id=' + chartId + ']').data('start-time', start.format('MMM YYYY'));
                $('.chart-item-data[data-chart-id=' + chartId + ']').data('end-time', end.format('MMM YYYY'));
                $('.chart-item-data[data-chart-id=' + chartId + ']').data('session', 'none');
                drawChart(chartId);
            }

            btn.parents('.modal').modal('hide');
        }
    });
});

function refreshChartDatepicker(chart){
    var startDate = moment().startOf('month').subtract(2, 'months'),
        endDate = moment().startOf('month').add(2, 'months'),
        data = $('.chart-item-data[data-chart-id=' + chart.data('chart-id') + ']');

    if ($('#ctmStartDate').val() !== '') {
        startDate = moment('1 ' +$('#ctmStartDate').val(), 'D MMM YYYY');
    }

    if ($('#ctmEndDate').val() !== '') {
        endDate = moment($('#ctmEndDate').val(), 'MMM YYYY');
    }

    if (typeof data.data('start-time') !== 'undefined' && data.data('start-time') !== '') {
        startDate = moment(data.data('start-time'), 'MMM YYYY');
    }

    if (typeof data.data('end-time') !== 'undefined' && data.data('end-time') !== '') {
        endDate = moment(data.data('end-time'), 'MMM YYYY');
    }

    if(startDate > endDate){
        var temp = startDate;
        startDate = endDate;
        endDate = temp;
    }

    if (typeof $('#ctmStartDate').data("DateTimePicker") !== 'undefined') {
        $('#ctmStartDate').data("DateTimePicker").destroy();
        $('#ctmEndDate').data("DateTimePicker").destroy();
    }

    var type = '';
    if(chart.length > 0){
        type = chart.parents('.chart-group').find('.chart-time').data('chart-type')
    }
    if(typeof type === 'undefined' || type === ''){
        type = $('.chart-time').data('type')
    }

    $.ajax({
        url: siteData.data('url') + '/site/chartDateLimit/',
        dataType: 'json',
        //data: 'type=' + chart.parents('.chart-group').data('chart-type'),
        data: 'type=' + type,
        success: function (returnData) {
            var minDate = moment(returnData.min, 'D MMM YYYY'),
                maxDate = moment(returnData.max, 'D MMM YYYY');

            if(startDate < minDate){
                startDate = minDate;
            }

            if(endDate > maxDate){
                endDate = maxDate;
            }

            $('#ctmStartDate').datetimepicker({
                viewMode: ($('.chart-period').data('period') == 'annual' ? 'years' : 'months'),
                format: 'MMM YYYY',
                inline: true,
                minDate: minDate,
                maxDate: maxDate,
                defaultDate: startDate
            });

            $('#ctmEndDate').datetimepicker({
                viewMode: ($('.chart-period').data('period') == 'annual' ? 'years' : 'months'),
                format: 'MMM YYYY',
                inline: true,
                minDate: minDate,
                maxDate: maxDate,
                defaultDate: endDate
            });

            $('#chartTimeModal .monthPicker').data('min-date', minDate.format('YYYY'));
            $('#chartTimeModal .monthPicker').data('max-date', maxDate.format('YYYY'));

            $('#chartTimeModal  .monthPicker').on('dp.show dp.update', function () {
                $(".datepicker-years .picker-switch").removeAttr('title').on('click', function (e) {
                    e.stopPropagation();
                });

                var minDate = parseInt($(this).data('min-date')),
                    maxDate = parseInt($(this).data('max-date'));
                $('.datepicker-years tbody td span').remove();
                for (var i = minDate; i <= maxDate; i++) {
                    $('.datepicker-years tbody td').append('<span data-action="selectYear" class="year">' + i + '</span>');
                }
                $('.datepicker-years .picker-switch').text(minDate + '-' + maxDate);
                $('.datepicker-years .prev, .datepicker-years .next').addClass('disabled');

            }).trigger('dp.update');
        }
    });
}


//*******************************
// CHANGE CHART TYPE
//*******************************
$(function () {
    $('#chartTypeModal').on('show.bs.modal', function (e) {
        var chart = $(e.relatedTarget).parents('.chart-item');
        $('#chartTypeModal').data('chart-id', chart.data('chart-id'));
        var data = $('.chart-item-data[data-chart-id=' + chart.data('chart-id') + ']').data();

        $('#chartTypeModal .chartTypeField input').each(function () {
            $(this).prop('disabled', false);
            $(this).parents('.btn').removeClass('btn-disabled');
            $(this).addClass('hidden');
            if ($(this).prop('checked')) {
                $(this).parents('.btn').addClass('btn-primary');
            } else {
                $(this).parents('.btn').removeClass('btn-primary');
            }
        });

        $('#chartTypeModal .chartTypeField input').prop('checked', false);
        $('#chartTypeModal .chartTypeField input[value=' + data['chartType'] + ']').prop('checked', true).trigger('click');
    });

    $('#chartTypeModal .chart-submit-btn').click(function () {
        var btn = $(this),
            chartId = btn.parents('.modal').data('chart-id');

        if (typeof chartId !== 'undefined') {

            var chartType = 'line';
            $('#chartTypeModal .chartTypeField input').each(function () {
                if ($(this).prop('checked')) {
                    chartType = $(this).val();
                }
            });

            //add the indicator to the parent chart and re-render entire chartBox
            $('.chart-item-data[data-chart-id=' + chartId + ']').data('chart-type', chartType);

            drawChart(chartId);
            btn.parents('.modal').modal('hide');
        }
    });

    $('#chartTypeModal .chartTypeField input').click(function () {
        $('#chartTypeModal .chartTypeField input').parents('.btn').removeClass('btn-primary');
        $(this).parents('.btn').addClass('btn-primary');
    });
});