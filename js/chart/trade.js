$(function () {
    tradeRefreshTypeahead(true);

});

//*******************************
// COMPARE COUNTRY
//*******************************
$(function () {

    $('#chartCompareModal').on('show.bs.modal', function (e) {
        if (typeof e.relatedTarget !== 'undefined') {
            var chart = $(e.relatedTarget).parents('.chart-item');
        } else if ($('.chart-item[data-chart-id=' + $('#chartCompareModal').data('chart-id') + ']').length > 0) {
            var chart = $('.chart-item[data-chart-id=' + $('#chartCompareModal').data('chart-id') + ']')
        } else {
            var chart = $('.chart-item:first');
        }
        $('#chartCompareModal').data('chart-id', chart.data('chart-id'));
        var data = $('.chart-item-data[data-chart-id=' + chart.data('chart-id') + ']').data();

        $('#chartCompareModal .chart-search-list-reporter .list-group-item[data-ref=' + data['reporter'] + ']').addClass('list-group-item-disabled');
        tradeRefreshTypeahead();
        $('#chartCompareModal .form-typeahead input').data('ref', '').typeahead('val', '');


        if (typeof data['compare'] !== 'undefined') {
            compare = data['compare'].split(',');
            if (compare.length > 0) {
                for (var i = 0; i < compare.length; i++) {
                    if (typeof compare[i] !== 'undefined' && compare[i] != '') {
                        var label = $('.chart-search-list-reporter .list-group-item[data-ref=' + compare[i] + ']').data('label');
                        $('#ccmReporter-' + (i + 1)).data('ref', compare[i]).typeahead('val', label);
                    }
                }
                $('.ccmSearchSave').addClass('btn-primary btn-inverse').removeClass('btn-disabled');
            }
        }

        toggleClearBtns();
    });


    $('#chartCompareModal .chart-submit-btn').click(function () {
        var btn = $(this),
            chartId = btn.parents('.modal').data('chart-id'),
            code = btn.data('code');

        if (typeof chartId !== 'undefined' && !$('#chartCompareModal .modal-body .btn').hasClass('btn-disabled')) {

            var compare = [];
            $('#chartCompareModal .ccmReporter input').each(function () {
                if (typeof $(this).data('ref') != 'undefined' && $(this).data('ref') != '' && $.inArray($(this).data('ref'), compare) === -1) {
                    compare.push($(this).data('ref'));
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
// INDICATOR
//*******************************
$(function () {

    $('#chartTradeModal').on('show.bs.modal', function (e) {
        //reset indicator form
        $('#chartTradeModal .model-title').text('Add Indicator');
        $('#chartTradeModal .chart-submit-btn').html('<span class="fa fa-plus"></span> Add Indicator');

        $('#chartTradeModal .chart-submit-btn').addClass('btn-disabled').removeClass('btn-primary btn-inverse').find('.fa').removeClass().addClass('fa fa-plus');
        $('#aimIndicator').val('');
        $('.aimReporter input, .aimSector input, .aimPartner input').data('ref', '').typeahead('val', '');
        $('.aimSector input, .aimPartner input').parents('.form-group').hide();
        $('.chart-search-list').hide();
        $('.aimPartnerLabel').hide().find('.count').text('3 partner countries');
        $('.aimSectorLabel').hide().find('.count').text('3 commodities');
        $('#chartTradeModal .chart-submit-btn').data('update', 'add');

        if (typeof e.relatedTarget !== 'undefined' && $(e.relatedTarget).parents('.chart-group').length > 0) {
            var group = $(e.relatedTarget).parents('.chart-group');
            if(group.hasClass('add-fav')){
                $('#chartTradeModal .chart-submit-btn').data('update', 'favorite');
            }
        } else if (typeof $('#chartTradeModal').data('chart-id') !== 'undefined' && $('.chart-group[data-chart-id=' + $('#chartTradeModal').data('chart-id') + ']').length > 0) {
            var group = $('.chart-group[data-chart-id=' + $('#chartTradeModal').data('chart-id') + ']')
        } else {
            var group = $('.chart-group:first');
        }

        $('#chartTradeModal').data('chart-id', group.data('chart-id'));

        $('.aimReporter').hide();
        $('.aimIndicator').show();
        if (typeof group.data('reporter') === 'undefined') {
            $('.aimIndicator').hide();
            $('.aimReporter').slideDown();
        } else {
            $('#aimReporter').data('ref', group.data('reporter'));
        }

        $('#chartTradeModal .chart-search-list-partner .list-group-item').show();
        $('#chartTradeModal .chart-search-list-partner .list-group-item[data-ref=' + group.data('reporter') + ']').hide();


        if ($(e.relatedTarget).hasClass('chart-edit-btn')) {
            tradeFormPreset($(e.relatedTarget).parents('.chart-item').data('chart-id'));
        } else if (group.find('.chart-item-data').length > 0 && typeof group.find('.chart-item-data').data('require-editing') !== 'undefined' && group.find('.chart-item-data').data('require-editing')) {
            tradeFormPreset(group.find('.chart-item-data').data('chart-id'));
        }

        validateSubmitBtn('trade');
    });

    $('#aimIndicator').change(function () {
        $('.aimPartnerLabel').hide().find('.count').text('3 partner countries');
        $('.aimSectorLabel').hide().find('.count').text('3 commodities');
        $('.aimSector, .aimPartner').find('input').data('ref', '').typeahead('val', '');

        validateSubmitBtn('trade');
    });

    $('#chartTradeModal .chart-submit-btn').click(function () {
        var btn = $(this),
            chartId = btn.parents('.modal').data('chart-id'),
            partner = [],
            sector = [];

        if(!btn.hasClass('btn-disabled')) {
            btn.parents('.modal').modal('hide');

            var reporter = $('#chartTradeModal .aimReporter input').data('ref');

            var triggerLoading = true;
            if (typeof chartId !== 'undefined') {
                var group = $('.chart-group[data-chart-id=' + chartId + ']');
                if(group.length == 0){
                    var group = $('.chart-item[data-chart-id=' + chartId + ']').parents('.chart-group');
                }
                if(group.length != 0) {
                    triggerLoading = false;
                    renderChartLoading(group);

                    if (typeof group.data('reporter') !== 'undefined') {
                        reporter = group.data('reporter');
                    }
                }
            }

            var partner = [];
            $('.aimPartner input').each(function () {
                if (typeof $(this).data('ref') != 'undefined' && $(this).data('ref') != '' && $.inArray($(this).data('ref'), partner) === -1) {
                    partner.push($(this).data('ref'));
                }
            });
            partner = partner.join();

            var sector = [];
            $('.aimSector input').each(function () {
                if (typeof $(this).data('ref') != 'undefined' && $(this).data('ref') != '' && $.inArray($(this).data('ref'), sector) === -1) {
                    sector.push($(this).data('ref'));
                }
            });
            sector = sector.join();

            if (btn.data('update') == 'add' && typeof chartId !== 'undefined') {
                $.ajax({
                    url: siteData.data('url') + '/trade/chart/',
                    dataType: 'json',
                    data: {
                        'indicator': $('#aimIndicator').val(),
                        'reporter': reporter,
                        'period': group.find('.chart-period').data('period'),
                        'editable': group.data('editable'),
                        'partner': partner,
                        'sector': sector,
                        'view': 'data',
                    },
                    success: function (returnData) {
                        if (returnData.valid) {
                            group.find('.chart-group-data').append(returnData.chart.data);
                            //addChart(btn.parents('.chart-group'),data)
                            drawChart(returnData.chart.chartId, triggerLoading);
                            $('.chart-group-empty').hide();
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
                        'favorite-type': 'trade',
                        'indicator': $('#aimIndicator').val(),
                        'reporter': reporter,
                        'period': group.find('.chart-period').data('period'),
                        'partner': partner,
                        'sector': sector,
                    },
                    success: function (returnData) {
                        if (returnData.valid) {
                            $('.trade-add-fav:first').before('<div class="col-sm-6 user-favorite" style="display: none;">' + returnData.html + '</div>');
                            $('.chart-item-data[data-chart-id=' + returnData.chartId + ']').parents('.user-favorite').slideDown();
                            drawChart(returnData.chartId, triggerLoading);
                            if ($('.trade-favorites .chart-group').length >= 7) {
                                $('.trade-add-fav').slideDown();
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
                $('.chart-item-data[data-chart-id=' + chartId + ']').data('partner', partner);
                $('.chart-item-data[data-chart-id=' + chartId + ']').data('sector', sector);
                $('.chart-item-data[data-chart-id=' + chartId + ']').data('indicator', $('#aimIndicator').val());
                $('.chart-item-data[data-chart-id=' + chartId + ']').data('require-editing', false);
                drawChart(chartId, triggerLoading);
            }
        }
    });

    $('.chartTypeField input').click(function () {
        $('.chartTypeField input').parents('.btn').removeClass('btn-primary');
        $(this).parents('.btn').addClass('btn-primary');
    });

});

function tradeFormPreset (chartId) {
    var data = $('.chart-item-data[data-chart-id=' + chartId + ']').data();
    $('#chartTradeModal').data('chart-id', chartId);

    if (typeof data['reporter'] !== 'undefined' && data['reporter'] != '') {
        var label = $('.chart-search-list-reporter .list-group-item[data-ref=' + data['reporter'] + ']').data('label');
        $('#aimReporter').data('ref', data['reporter']).typeahead('val', label);
    }

    if (typeof data['indicator'] !== 'undefined') {
        $('#aimIndicator').val(data['indicator']).selectpicker('refresh');
    }

    if (typeof data['partner'] !== 'undefined') {
        partners = data['partner'].split(',');
        if (partners.length > 0) {
            for (var i = 0; i < partners.length; i++) {
                if (typeof partners[i] !== 'undefined' && partners[i] != '') {
                    var label = $('.chart-search-list-partner .list-group-item[data-ref=' + partners[i] + ']').data('label');
                    $('#aimPartner-' + (i + 1)).data('ref', partners[i]).typeahead('val', label);
                }
            }
        }
    }

    if (typeof data['sector'] !== 'undefined') {
        data['sector'] = '' + data['sector']; //fix: convert number to string
        sectors = data['sector'].split(',');
        if (sectors.length > 0) {
            for (var i = 0; i <= sectors.length; i++) {
                if (typeof sectors[i] !== 'undefined' && sectors[i] != '') {
                    var label = $('.chart-search-list-sector .list-group-item[data-ref=' + sectors[i] + ']').data('label');
                    $('#aimSector-' + (i + 1)).data('ref', sectors[i]).typeahead('val', label);
                }
            }
        }
    }

    if (typeof data['compare'] !== 'undefined' && data['compare'] != '') {
        $('.aimSector').each(function (i) {
            if (i > 0) {
                $(this).hide();
            }
        });
    }

    if (typeof data['requireEditing'] === 'undefined' || !data['requireEditing']) {
        $('#chartTradeModal .model-title').text('Edit Indicator');
        $('#chartTradeModal .chart-submit-btn').html('<span class="fa fa-pencil"></span> Edit Indicator');
    }
    $('#chartTradeModal .chart-submit-btn').data('update', 'edit');
    validateSubmitBtn('trade');
}

function tradeRefreshTypeahead (init) {
    init = (typeof init !== 'undefined' ? init : false);
    var datasets = [],
        flow = (typeof $('#aimIndicator').val() !== 'undefined' && $('#aimIndicator').val().indexOf('flow-') !== -1 ? '-flow' : ''),
        reporter = (typeof $('#aimReporter').data('ref') !== 'undefined' ? $('#aimReporter').data('ref') : '');

    if (!init) {
        filterLists($('#chartTradeModal .chart-search-list-reporter, #chartCompareModal .chart-search-list-reporter'), {
            type: 'reporter',
        });
        filterLists($('#chartTradeModal .chart-search-list-partner'), {
            type: 'partner' + flow,
            reporter: $('#aimReporter').data('ref'),
        });

        filterLists($('#chartTradeModal .chart-search-list-sector'), {
            type: 'sector' + flow,
            reporter: $('#aimReporter').data('ref'),
        });
    }

    datasets.push({
        'selector': '.ccmReporter input',
        'url': siteData.data('url') + '/autoComplete/reporterTrade?&term=%QUERY',
    });

    datasets.push({
        'selector': '.aimReporter input',
        'url': siteData.data('url') + '/autoComplete/reporterTrade?term=%QUERY',
    });

    datasets.push({
        'selector': '.aimPartner input',
        'url': siteData.data('url') + '/autoComplete/partner?reporter=' + reporter + '&filterType=partner' + flow + '&term=%QUERY',
    });

    datasets.push({
        'selector': '.aimSector input',
        'url': siteData.data('url') + '/autoComplete/sector?reporter=' + reporter + '&filterType=sector' + flow + '&term=%QUERY',
    });

    refreshTypeahead('trade', datasets, init);
}

function tradeValidateSubmitBtn () {
    var valid = true;
    var reporter = '';


    if (typeof $('#chartTradeModal .aimReporter input').data('ref') !== 'undefined' && $('#chartTradeModal .aimReporter input').data('ref') != '') {
        $('.aimIndicator').slideDown();
        reporter = $('#aimReporter').data('ref');
    } else {
        $('.aimIndicator, .aimPartner, .aimSector, aimPartnerLabel, aimSectorLabel').slideUp();
        $('.chart-search-list').slideUp();

        $('#aimIndicator').val('');
        $('.aimSector input, .aimPartner input').data('ref', '').typeahead('val', '');
        valid = false;
    }

    //disable indicators that are already in use
    $('#aimIndicator option').prop('disabled', false);
    if (reporter != '' && $('.chart-item-data').length > 0) {
        $('.chart-item-data').each(function () {
            if (typeof $(this).data('indicator') !== 'undefined' && typeof $(this).data('reporter') !== 'undefined' && $(this).data('reporter') == reporter && $(this).data('indicator') != $('#aimIndicator').val()) {
                $('#aimIndicator option[value=' + $(this).data('indicator') + ']').prop('disabled', true);
            }
        });
    }
    $('#aimIndicator').selectpicker('refresh');

    switch ($('#aimIndicator').val()) {
        //3 partners
        case 'trade-partner-ev':
        case 'trade-partner-iv':
        case 'trade-partner-tt':
            $('.aimPartner').slideDown();
            $('.aimSector').hide();
            $('.aimPartnerLabel').slideDown();
            $('.aimSectorLabel').hide();
            break;

        //3 sectors
        case 'trade-sector-ev':
        case 'trade-sector-iv':
        case 'trade-sector-tt':
            $('.aimPartner').hide();
            $('.aimSector').slideDown();
            $('.aimPartnerLabel').hide();
            $('.aimSectorLabel').slideDown();
            break;

        //3 partners and 1 sector
        case 'flow-partner-sector-iv':
        case 'flow-partner-sector-ev':
            $('.aimPartner').slideDown();
            $('.aimSector').hide();
            $('.aimSector:first').slideDown();
            $('.aimPartnerLabel').slideDown();
            $('.aimSectorLabel').slideDown().find('.count').text('1 commodity');
            break;

        //1 partner
        case 'flow-partner-top10-iv':
        case 'flow-partner-top10-ev':
            $('.aimPartner').hide();
            $('.aimPartner:first').slideDown();
            $('.aimSector').hide();
            $('.aimPartnerLabel').slideDown().find('.count').text('1 partner country');
            $('.aimSectorLabel').hide();
            break;

        //1 partner and 3 sectors
        case 'flow-sector-partner-iv':
        case 'flow-sector-partner-ev':
            $('.aimPartner').hide();
            $('.aimPartner:first').slideDown();
            $('.aimSector').slideDown();
            $('.aimPartnerLabel').slideDown().find('.count').text('1 partner country');
            $('.aimSectorLabel').slideDown();
            break;

        //1 sector
        case 'flow-sector-top10-iv':
        case 'flow-sector-top10-ev':
            $('.aimPartner').hide();
            $('.aimSector').hide();
            $('.aimSector:first').slideDown();
            $('.aimPartnerLabel').hide();
            $('.aimSectorLabel').slideDown().find('.count').text('1 commodity');
            break;

        //no partners or sectors
        default:
            $('.aimPartner').hide();
            $('.aimSector').hide();
            $('.aimPartnerLabel').hide();
            $('.aimSectorLabel').hide();
            break;
    }

    if ($('#aimIndicator').val() == '') {
        valid = false;
    }

    if (valid) {
        var hasPartner = false;
        $('.aimPartner').each(function (i) {
            if ($(this).is(':visible')) {
                hasPartner = true;
            }
        });
        if (hasPartner) {
            valid = false;
            $('.aimPartner').each(function (i) {
                if ($(this).is(':visible') && typeof $(this).find('input').data('ref') !== 'undefined' && $(this).find('input').data('ref') != '') {
                    valid = true;
                }
            });
        }
    }

    if (valid) {
        var hasSector = false;
        $('.aimSector').each(function (i) {
            if ($(this).is(':visible')) {
                hasSector = true;
            }
        });
        if (hasSector) {
            valid = false;
            $('.aimSector').each(function (i) {
                if ($(this).is(':visible') && typeof $(this).find('input').data('ref') !== 'undefined' && $(this).find('input').data('ref') != '') {
                    valid = true;
                }
            });
        }
    }

    if (valid) {
        $('#chartTradeModal .chart-submit-btn').addClass('btn-primary btn-inverse').removeClass('btn-disabled');
    } else {
        $('#chartTradeModal .chart-submit-btn').addClass('btn-disabled').removeClass('btn-primary btn-inverse');
    }

    var icon = 'plus';
    if ($('.chart-submit-btn').data('update') == 'edit') {
        icon = 'pencil';
    }
    $('#chartTradeModal .chart-submit-btn .fa').removeClass().addClass('fa fa-' + icon);
    tradeRefreshTypeahead();
}
