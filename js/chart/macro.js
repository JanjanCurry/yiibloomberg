$(function () {
    macroRefreshTypeahead(true);

});


//*******************************
// COMPARE MACRO
//*******************************
$(function () {

    $('#chartCompareModal').on('show.bs.modal', function (e) {
        //reset compare form
        $('.compareMacroSelect option').prop('disabled', false).prop("selected", false);
        $('.compareMacroSelect select').val('').trigger("change");

        //get chartId
        if (typeof e.relatedTarget !== 'undefined') {
            var chart = $(e.relatedTarget).parents('.chart-item');
        } else if ($('.chart-item[data-chart-id=' + $('#chartCompareModal').data('chart-id') + ']').length > 0) {
            var chart = $('.chart-item[data-chart-id=' + $('#chartCompareModal').data('chart-id') + ']')
        } else {
            var chart = $('.chart-item:first');
        }
        $('#chartCompareModal').data('chart-id', chart.data('chart-id'));
        var data = $('.chart-item-data[data-chart-id=' + chart.data('chart-id') + ']').data();

        //limit options
        filterLists($('#chartCompareModal .compareMacroSelect'), {
            type: 'macro',
            period: data['period'],
            reporter: data['reporter'],
            variant: data['variant']
        }, 'dropdown');

        setTimeout(function () {
            $('#chartCompareModal .compareMacroSelect option[value=' + data['macro'] + ']').prop('disabled', true);
            $('.selectpicker').selectpicker('refresh');
        }, 1000);

        //preset form when editing
        if (typeof data['compare'] !== 'undefined') {
            compare = data['compare'].split(',');
            if (compare.length > 0) {
                for (var i = 0; i <= compare.length; i++) {
                    if (typeof compare[i] !== 'undefined' && compare[i] != '') {
                        $('#compareMacro-' + (i + 1)).val(compare[i]).trigger('change');
                    }
                }
            }
        }

    });

    //submit compare form
    $('#chartCompareModal .chart-submit-btn').click(function () {
        var btn = $(this),
            chartId = btn.parents('.modal').data('chart-id'),
            code = btn.data('code');

        if (typeof chartId !== 'undefined') {

            //get data
            var compare = [];
            $('#chartCompareModal .compareMacroSelect select').each(function (i) {
                if ($(this).val() != '' && $.inArray($(this).val(), compare) === -1) {
                    compare.push($(this).val());
                }
            });
            compare = compare.join();

            $('.chart-item-data[data-chart-id=' + chartId + ']').data('compare', compare);

            drawChart(chartId);
            btn.parents('.modal').modal('hide');
        }
    });
});

//*******************************
// MACRO
//*******************************
$(function () {

    $('#chartMacroModal').on('show.bs.modal', function (e) {
        //reset macro form
        $('#chartMacroModal .model-title').text('Add Macro');
        $('#chartMacroModal .chart-submit-btn').html('<span class="fa fa-plus"></span> Add Macro');
        $('#chartMacroModal .chart-submit-btn').addClass('btn-disabled').removeClass('btn-primary btn-inverse').find('.fa').removeClass().addClass('fa fa-plus');
        $('.macro-country input').data('ref', '').typeahead('val', '');

        $('.chart-search-list, .macro-country').hide();
        $('#chartMacroModal .chart-submit-btn').data('update', 'add');

        //get related chartId
        if (typeof e.relatedTarget !== 'undefined' && $(e.relatedTarget).parents('.chart-group').length > 0) {
            var group = $(e.relatedTarget).parents('.chart-group');
            if(group.hasClass('add-fav')){
                $('#chartMacroModal .chart-submit-btn').data('update', 'favorite');
            }
        } else if (typeof $('#chartMacroModal').data('chart-id') !== 'undefined' && $('.chart-group[data-chart-id=' + $('#chartMacroModal').data('chart-id') + ']').length > 0) {
            var group = $('.chart-group[data-chart-id=' + $('#chartMacroModal').data('chart-id') + ']')
        } else {
            var group = $('.chart-group:first');
        }
        $('#chartMacroModal').data('chart-id', group.data('chart-id'));

        //preset form on edit
        if ($(e.relatedTarget).hasClass('chart-edit-btn')) {
            macroFormPreset($(e.relatedTarget).parents('.chart-item').data('chart-id'));
        } else if (group.find('.chart-item-data').length > 0 && typeof group.find('.chart-item-data').data('require-editing') !== 'undefined' && group.find('.chart-item-data').data('require-editing')) {
            macroFormPreset(group.find('.chart-item-data').data('chart-id'));
        }else{
            $('#macroType').val('').trigger("change");
        }

        macroRefreshTypeahead();
        macroValidateSubmitBtn();
    });

    //toggle country fields
    $('#macroType').change(function () {
        var chartId = $('#chartMacroModal').data('chart-id'),
            limit = '';

        if (typeof chartId !== 'undefined') {
            var data = $('.chart-item-data[data-chart-id=' + chartId + ']');
            if (typeof data !== 'undefined' && typeof data.data('compare') !== 'undefined' && data.data('compare') != '') {
                limit = ':first';
            }
        }
        if ($(this).val() != '') {
            $('.macro-country' + limit).slideDown();
        } else {
            $('.macro-country').hide();
        }

        macroRefreshTypeahead();
        macroValidateSubmitBtn();
    });

    //submit macro form
    $('#chartMacroModal .chart-submit-btn').click(function () {
        var btn = $(this),
            chartId = btn.parents('.modal').data('chart-id');

        if(!btn.hasClass('btn-disabled')){
            btn.parents('.modal').modal('hide');

            var triggerLoading = true;
            if (typeof chartId !== 'undefined') {
                var group = $('.chart-group[data-chart-id=' + chartId + ']');
                if(group.length == 0){
                    var group = $('.chart-item[data-chart-id=' + chartId + ']').parents('.chart-group');
                }
                if(group.length != 0) {
                    triggerLoading = false;
                    renderChartLoading(group);
                }
            }

            var reporter = [],
                exists = [];
            $('.cmReporter input').each(function (i) {
                if (typeof $(this).data('ref') != 'undefined' && $(this).data('ref') != '' && $.inArray($(this).data('ref'), exists) === -1) {
                    reporter.push($(this).data('ref'));
                    exists.push($(this).data('ref'));
                }
            });
            reporter = reporter.join();

            if (btn.data('update') == 'add' && typeof chartId !== 'undefined') {
                $.ajax({
                    url: siteData.data('url') + '/macro/chart/',
                    dataType: 'json',
                    data: {
                        'editable': group.data('editable'),
                        'macro': $('#macroType').val(),
                        'period': group.find('.chart-period').data('period'),
                        'reporter': reporter,
                        'view': 'data',
                    },
                    success: function (returnData) {
                        if (returnData.valid) {
                            group.find('.chart-group-data').append(returnData.chart.data);
                            //addChart(btn.parents('.chart-group'),data)
                            drawChart(returnData.chart.chartId, triggerLoading);
                            $('.chart-group-empty').slideUp();
                        } else {
                            if (typeof returnData.error !== 'undefined') {
                                for (var key in returnData.error) {
                                    addFlashMessage('danger', returnData.error[key]);
                                }
                            }
                            group.find('.chart-loading').fadeOut(function () {
                                $(this).remove();
                            });
                        }

                    }
                });
            }

            if (btn.data('update') == 'favorite') {
                $.ajax({
                    url: siteData.data('url') + '/user/addFavorite/',
                    dataType: 'json',
                    method: 'post',
                    data: {
                        'favorite-type': 'macro',
                        'macro': $('#macroType').val(),
                        'period': 'annual',
                        'reporter': reporter,
                    },
                    success: function (returnData) {
                        if (returnData.valid) {
                            $('.macro-add-fav:first').before('<div class="col-sm-6 user-favorite" style="display: none;">' + returnData.html + '</div>');
                            $('.chart-item-data[data-chart-id=' + returnData.chartId + ']').parents('.user-favorite').slideDown();
                            drawChart(returnData.chartId, triggerLoading);
                            if ($('.macro-favorites .chart-group').length >= 7) {
                                $('.macro-add-fav').slideDown();
                            }
                        } else if (typeof returnData.error !== 'undefined') {
                            for (var key in returnData.error) {
                                addFlashMessage('danger', returnData.error[key]);
                            }
                        }
                    }
                });
            }

            if (btn.data('update') == 'edit' && typeof chartId !== 'undefined') {
                $('.chart-item-data[data-chart-id=' + chartId + ']').data('macro', $('#macroType').val());
                $('.chart-item-data[data-chart-id=' + chartId + ']').data('reporter', reporter);
                drawChart(chartId, triggerLoading);
            }
        }
    });


});

function macroFormPreset (chartId) {
    var data = $('.chart-item-data[data-chart-id=' + chartId + ']').data();

    $('#chartMacroModal').data('chart-id', chartId);

    $('#macroType').val(data['macro']).trigger('change');

    if (typeof data['reporter'] !== 'undefined') {
        reporter = data['reporter'].split(',');
        if (reporter.length > 0) {
            for (var i = 0; i <= reporter.length; i++) {
                if (typeof reporter[i] !== 'undefined' && reporter[i] != '') {
                    var label = $('.chart-search-list-reporter .list-group-item[data-ref=' + reporter[i] + ']').data('label');
                    $('#macroCountry-' + (i + 1)).data('ref', reporter[i]).typeahead('val', label);
                }
            }
        }
    }

    if (typeof data['compare'] !== 'undefined' && data['compare'] != '') {
        $('.macro-country-compare').hide();
    }

    if (typeof data['requireEditing'] === 'undefined' || !data['requireEditing']) {
        $('#chartMacroModal .model-title').text('Edit Macro');
        $('#chartMacroModal .chart-submit-btn').html('<span class="fa fa-plus"></span> Edit Macro');
        $('#chartMacroModal .chart-submit-btn').addClass('btn-primary btn-inverse').removeClass('btn-disabled').find('.fa').removeClass().addClass('fa fa-pencil');
    }

    $('#chartMacroModal .chart-submit-btn').data('update', 'edit');

}

function macroRefreshTypeahead (init) {
    init = (typeof init !== 'undefined' ? init : false);

    if (!init) {
        filterLists($('#chartMacroModal .chart-search-list-reporter'), {
            type: 'macro',
            //period: $('.chart-period').data('period'),
            macro: $('#chartMacroModal #macroType').selectpicker('val'),
        });
    }

    var datasets = [];

    datasets.push({
        'selector': '.macro-country input',
        'url': siteData.data('url') + '/autoComplete/reporterMacro?period=' + $('.chart-period').data('period') + '&macro=' + $('#chartMacroModal #macroType').val() + '&term=%QUERY',
    });

    refreshTypeahead('macro', datasets, init);
}

function macroValidateSubmitBtn () {
    var valid = true;

    if ($('#macroType').val() == '') {
        valid = false;
    }

    if (valid) {
        valid = false;
        $('.macro-country input').each(function () {
            if (typeof $(this).data('ref') !== 'undefined' && $(this).data('ref') != '') {
                valid = true;
            }
        });
    }

    if (valid) {
        $('#chartMacroModal .chart-submit-btn').addClass('btn-primary btn-inverse').removeClass('btn-disabled');
    } else {
        $('#chartMacroModal .chart-submit-btn').addClass('btn-disabled').removeClass('btn-primary btn-inverse');
    }
}