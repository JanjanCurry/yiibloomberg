$(function () {
    marketRefreshTypeahead(true);

});


//*******************************
// COMPARE MARKET
//*******************************
$(function () {

    $('#chartMarketCompareModal').on('show.bs.modal', function (e) {
        //get chartId
        if (typeof e.relatedTarget !== 'undefined') {
            var chart = $(e.relatedTarget).parents('.chart-item');
        } else if ($('.chart-item[data-chart-id=' + $('#chartMarketCompareModal').data('chart-id') + ']').length > 0) {
            var chart = $('.chart-item[data-chart-id=' + $('#chartMarketCompareModal').data('chart-id') + ']')
        } else {
            var chart = $('.chart-item:first');
        }
        $('#chartMarketCompareModal').data('chart-id', chart.data('chart-id'));
        var data = $('.chart-item-data[data-chart-id=' + chart.data('chart-id') + ']').data();

        var market = (typeof data['marketCompare'] !== 'undefined' && data['marketCompare'].length > 0 ? data['marketCompare'] : data['market']);
        $('#chartMarketCompareModal #market-compare-market').selectpicker('val',market).trigger('changed.bs.select');

        $('#chartMarketCompareModal .market-compare input').data('ref', '').typeahead('val', '');
        marketRefreshTypeahead();

        //preset form when editing
        if (typeof data['compare'] !== 'undefined') {
            item = data['compare'].split(',');
            if (item.length > 0) {
                for (var i = 0; i <= item.length; i++) {
                    if (typeof item[i] !== 'undefined' && item[i] != '') {
                        var label = $('.chart-search-list .list-group-item[data-ref=' + item[i] + ']').data('label');
                        $('#chartMarketCompareModal .'+data['marketCompare']+'-compare input').data('ref', item[i]).typeahead('val', label).trigger('change');
                    }
                }
            }
        }

        validateSubmitBtn('market-compare');
    });

    $('#chartMarketCompareModal #market-compare-market').on('changed.bs.select', function () {
        var market = $(this).val();
        $('#chartMarketCompareModal .market-compare').slideUp();
        $('#chartMarketCompareModal .market-compare:not(.'+market+'-compare) input').data('ref', '').typeahead('val', '');
        $('#chartMarketCompareModal .market-compare.'+market+'-compare').slideDown();
        validateSubmitBtn('market-compare');
    });

    //submit compare form
    $('#chartMarketCompareModal .chart-submit-btn').click(function () {
        var btn = $(this),
            chartId = btn.parents('.modal').data('chart-id'),
            code = btn.data('code');

        if (typeof chartId !== 'undefined') {
            //get data
            var compare = [];
            $('#chartMarketCompareModal .market-compare input').each(function () {
                if (typeof $(this).data('ref') != 'undefined' && $(this).data('ref') != '' && $.inArray($(this).data('ref'), compare) === -1) {
                    compare.push($(this).data('ref'));
                }
            });
            compare = compare.join();

            $('.chart-item-data[data-chart-id=' + chartId + ']').data('compare', compare);
            $('.chart-item-data[data-chart-id=' + chartId + ']').data('market-compare', $('#chartMarketCompareModal #market-compare-market').val());

            drawChart(chartId);
            btn.parents('.modal').modal('hide');
        }
    });
});

//*******************************
// MARKET
//*******************************
$(function () {

    $('#chartMarketModal').on('show.bs.modal', function (e) {
        //reset macro form
        $('#chartMarketModal .model-title').text('Add Asset');
        $('#chartMarketModal .chart-submit-btn').html('<span class="fa fa-plus"></span> Add Asset');
        $('#chartMarketModal .chart-submit-btn').addClass('btn-disabled').removeClass('btn-primary btn-inverse').find('.fa').removeClass().addClass('fa fa-plus');
        $('.market-search input').data('ref', '').typeahead('val', '');
        $('#chartMarketModal .market-list').hide();
        $('#chartMarketModal .chart-submit-btn').data('update', 'add');

        //get related chartId
        if (typeof e.relatedTarget !== 'undefined' && $(e.relatedTarget).parents('.chart-group').length > 0) {
            var group = $(e.relatedTarget).parents('.chart-group');
            if(group.hasClass('add-fav')){
                $('#chartMarketModal .chart-submit-btn').data('update', 'favorite');
            }
        } else if (typeof $('#chartMarketModal').data('chart-id') !== 'undefined' && $('.chart-group[data-chart-id=' + $('#chartMarketModal').data('chart-id') + ']').length > 0) {
            var group = $('.chart-group[data-chart-id=' + $('#chartMarketModal').data('chart-id') + ']')
        } else {
            var group = $('.chart-group:first');
        }
        var market = group.data('chart-type');
        $('#chartMarketModal').data({
            'chart-id': group.data('chart-id'),
            'type': market
        });

        $('#chartMarketModal .market-search').hide();
        $('#chartMarketModal .market-search.'+market+'-search').show();

        //preset form on edit
        if ($(e.relatedTarget).hasClass('chart-edit-btn')) {
            var chartId = $(e.relatedTarget).parents('.chart-item').data('chart-id');
            marketFormPreset(chartId);
            $('#chartMarketModal').data({'chart-id': chartId});
        } else if (group.find('.chart-item-data').length > 0 && typeof group.find('.chart-item-data').data('require-editing') !== 'undefined' && group.find('.chart-item-data').data('require-editing')) {
            var chartId = group.find('.chart-item-data').data('chart-id');
            marketFormPreset(chartId);
            $('#chartMarketModal').data({'chart-id': chartId});
        }

        marketRefreshTypeahead();
        validateSubmitBtn('market');
    });

    $('.market-search input').on("typeahead:selected", function (object, datum, name) {
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

    //submit market form
    $('#chartMarketModal .chart-submit-btn').click(function () {
        var btn = $(this),
            chartId = btn.parents('.modal').data('chart-id');

        if(!btn.hasClass('btn-disabled')) {
            btn.parents('.modal').modal('hide');
            var market = btn.parents('.modal').data('type');

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

            var item = [];
            $('.market-search input').each(function (i) {
                if (typeof $(this).data('ref') != 'undefined' && $(this).data('ref') != '' && $.inArray($(this).data('ref'), item) === -1) {
                    item.push($(this).data('ref'));
                }
            });
            item = item.join();

            if (btn.data('update') == 'add' && typeof chartId !== 'undefined') {
                //add new chart
                $.ajax({
                    url: siteData.data('url') + '/'+market+'/chart/',
                    dataType: 'json',
                    data: {
                        'item': item,
                        'period': group.find('.chart-period').data('period'),
                        'view': 'data',
                        'editable': group.data('editable'),
                    },
                    success: function (returnData) {
                        if (returnData.valid) {
                            group.find('.chart-group-data').append(returnData.chart.data);
                            //addChart(btn.parents('.chart-group'),data)
                            drawChart(returnData.chart.chartId, triggerLoading);
                            $('.chart-group-empty').slideUp();
                        } else {
                            if (typeof returnData.error == 'string' && returnData.error != '') {
                                addFlashMessage('danger', returnData.error);
                                data.data('compare', '');
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

                    }
                });
            }

            if (btn.data('update') == 'favorite') {
                $.ajax({
                    url: siteData.data('url') + '/user/addFavorite/',
                    dataType: 'json',
                    method: 'post',
                    data: {
                        'item': item,
                        'period': group.find('.chart-period').data('period'),
                        'favorite-type': market,
                    },
                    success: function (returnData) {
                        if (returnData.valid) {
                            $('.market-add-fav:first').before('<div class="col-sm-6 user-favorite" style="display: none;">' + returnData.html + '</div>');
                            $('.chart-item-data[data-chart-id=' + returnData.chartId + ']').parents('.user-favorite').slideDown();
                            drawChart(returnData.chartId, triggerLoading);
                            if ($('.market-favorites .chart-group').length >= 7) {
                                $('.market-add-fav').slideDown();
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
                $('.chart-item-data[data-chart-id=' + chartId + ']').data('item', item);
                drawChart(chartId, triggerLoading);
            }
        }

    });


});

function marketFormPreset (chartId) {
    var data = $('.chart-item-data[data-chart-id=' + chartId + ']').data();

    $('#chartMarketModal').data('chart-id', chartId);

    if (typeof data['item'] !== 'undefined') {
        item = data['item'].split(',');
        if (item.length > 0) {
            for (var i = 0; i <= item.length; i++) {
                if (typeof item[i] !== 'undefined' && item[i] != '') {
                    var label = $('.chart-search-list .list-group-item[data-ref=' + item[i] + ']').data('label');
                    $('#chartMarketModal .'+data['market']+'-search input').data('ref', item[i]).typeahead('val', label);
                }
            }
        }
    }

    if (typeof data['requireEditing'] === 'undefined' || !data['requireEditing']) {
        $('#chartMarketModal .model-title').text('Edit Asset');
        $('#chartMarketModal .chart-submit-btn').addClass('btn-primary btn-inverse').removeClass('btn-disabled').find('.fa').removeClass().addClass('fa fa-pencil');
    }

    $('#chartMarketModal .chart-submit-btn').data('update', 'edit');

}

function marketRefreshTypeahead (init) {
    init = (typeof init !== 'undefined' ? init : false);

    var restrict = []
    $('.chart-item-data').each(function(){
        if(typeof $(this).data('item') !== 'undefined'){
            restrict.push($(this).data('item'));
        }
    });
    restrict.join();

    if (!init) {
        filterLists($('#chartMarketModal .chart-search-list-commodity, #chartMarketCompareModal .chart-search-list-commodity'), {
            type: 'commodity',
            //commodity: $('#commodity-search-1').data('ref'),
            restrict: restrict,
            selected: restrict,
        });
        filterLists($('#chartMarketModal .chart-search-list-currency, #chartMarketCompareModal .chart-search-list-currency'), {
            type: 'currency',
            //commodity: $('#commodity-search-1').data('ref'),
            restrict: restrict,
            selected: restrict,
        });
        filterLists($('#chartMarketModal .chart-search-list-equity, #chartMarketCompareModal .chart-search-list-equity'), {
            type: 'equity',
            //commodity: $('#commodity-search-1').data('ref'),
            restrict: restrict,
            selected: restrict,
        });
    }

    var datasets = [];

    datasets.push({
        'selector': '.commodity-search input',
        'url': siteData.data('url') + '/autoComplete/commodity?restrict='+restrict+'&term=%QUERY',
    });
    datasets.push({
        'selector': '.currency-search input',
        'url': siteData.data('url') + '/autoComplete/currency?restrict='+restrict+'&term=%QUERY',
    });
    datasets.push({
        'selector': '.equity-search input',
        'url': siteData.data('url') + '/autoComplete/equity?restrict='+restrict+'&term=%QUERY',
    });

    datasets.push({
        'selector': '.commodity-compare input',
        'url': siteData.data('url') + '/autoComplete/commodity?restrict='+restrict+'&term=%QUERY',
    });
    datasets.push({
        'selector': '.currency-compare input',
        'url': siteData.data('url') + '/autoComplete/currency?restrict='+restrict+'&term=%QUERY',
    });
    datasets.push({
        'selector': '.equity-compare input',
        'url': siteData.data('url') + '/autoComplete/equity?restrict='+restrict+'&term=%QUERY',
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
}
