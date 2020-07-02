var tour = {};

$(function () {
    tour = new Tour({
        name: 'systemDemoTour',
        backdrop: true,
        keyboard: false,
        smartPlacement: false,
        debug: false,
        //basePath: '/complete-intel',
        onShown: function (tour) {
            var step = tour.getCurrentStep();
            $("body").removeClass(function (index, css) {
                return (css.match(/(^|\s)tour-step-\S+/g) || []).join(' ');
            }).addClass('tour-step-' + step);
            $('.tour #tourOnLogin').prop('checked',(siteData.data('tour-login') == "1" ? true : false))
        },
        onEnd: function (tour) {
            $('body').removeClass('tour');
            $.ajax({
                url: siteData.data('url') + '/user/tourToggle',
                dataType: 'json',
                data: 'ajax=1'
            });
        },
        template: "<div class='popover tour'>" +
        "<div class='arrow'></div>" +
        "<h3 class='popover-title'></h3>" +
        "<div class='popover-content'></div>" +
        "<div class='popover-navigation'>" +
        "<div class='row'>" +
        "<div class='col-xs-8'>" +
        "<p class='btn-group'>" +
        "<button class='btn btn-default btn-sm' data-role='prev'><span class='fa fa-arrow-left'></span> Prev</button>" +
        "<button class='btn btn-default btn-sm' data-role='next'>Next <span class='fa fa-arrow-right'></span></button>" +
        "</p>" +
        "</div>" +
        "<div class='col-xs-4'>" +
        "<p class='btn-group'>" +
        "<button class='btn btn-default btn-sm' data-role='end'><span class='fa fa-times'></span> End</button>" +
        "</p>" +
        "</div>" +
        "</div>" +
        "<div class='checkbox margin-0'><label class='control-label'><input type='checkbox' value='1' name='tourOnLogin' id='tourOnLogin' " + (siteData.data('tour-login') == "1" ? "checked='checked'" : "") + " /> Show tutorial on next login</label></div>" +
        "</div>" +
        "</div>",
        delay: {
            show: 500,
            hide: 100
        },
        steps: [
            {//STEP 0
                orphan: true,
                title: "Complete Intelligence Tutorial",
                content: "Welcome to our quick and easy tutorial. Click 'Next' to continue or click 'End' to close the tutorial.",
                placement: "bottom",
                path: '/',
            },
            {//STEP 1
                element: "#tour-step-1",
                title: "Searching for a country",
                content: "This allows you to select a country. We will try searching for Canada.",
                placement: "bottom",
                path: '/',
                onShown: function (tour) {
                    var step = tour.getCurrentStep();
                    $("body").removeClass(function (index, css) {
                        return (css.match(/(^|\s)tour-step-\S+/g) || []).join(' ');
                    }).addClass('tour-step-' + step);
                    $('.tour #tourOnLogin').prop('checked',(siteData.data('tour-login') == "1" ? true : false))
                    $('.tour-step-background').height($('#chartTimeModal .modal-content').height()).width($('#chartTimeModal .modal-content').width());

                    setTimeout(function () {
                        $('#search').typeahead('val', 'C');
                    }, 300);
                    setTimeout(function () {
                        $('#search').typeahead('val', 'Ca');
                    }, 400);
                    setTimeout(function () {
                        $('#search').typeahead('val', 'Can');
                    }, 500);
                    setTimeout(function () {
                        $('#search').typeahead('val', 'Cana');
                    }, 600);
                    setTimeout(function () {
                        $('#search').typeahead('val', 'Canad');
                    }, 700);
                    setTimeout(function () {
                        $('#search').typeahead('val', 'Canada').typeahead('open');
                    }, 800);
                },
            },
            {//STEP 2
                element: ".chart-group",
                title: "Time Range",
                content: "By default, the graph shows total trade for a 24 month period.",
                placement: "top",
                path: '/trade/country/CAN',
            },
            {//STEP 3
                element: '.chart-group-header .btn-group:last .dropdown-item[data-target=#chartTradeModal]',
                title: "Adding an indicator",
                content: "The 'Add Indicator' button allows you to add more data reports.",
                placement: "left",
                path: '/trade/country/CAN',
                onShow: function (tour) {
                    $('.chart-group-header .btn-group:last').addClass('forcedopen');
                },
                onHide: function (tour) {
                    $('.chart-group-header .btn-group:last').removeClass('forcedopen');
                }
            },
            {//STEP 4
                element: '#chartTradeModal .modal-body',
                title: "Selecting an indicator",
                content: "Here you can choose any indicator based on several factors. We will try this with 'Top 10 Export Partners'.",
                placement: "top",
                path: '/trade/country/CAN',
                onShow: function (tour) {
                    $('#chartTradeModal').modal("show");
                    $('#chartTradeModal').data('chart-id', $('.chart-group:first').data('chart-id'));
                },
                onShown: function (tour) {
                    var step = tour.getCurrentStep();
                    $("body").removeClass(function (index, css) {
                        return (css.match(/(^|\s)tour-step-\S+/g) || []).join(' ');
                    }).addClass('tour-step-' + step);
                    $('.tour #tourOnLogin').prop('checked',(siteData.data('tour-login') == "1" ? true : false))

                    $('#aimIndicator').selectpicker('val', 'top10-partner-ev').trigger('change');
                },
                onNext: function (tour) {
                    $('.chart-item').each(function (i) {
                        if (i > 0) {
                            $(this).find('.chart-btn-remove').trigger('click');
                        }
                    });

                    $('#chartTradeModal').data('chart-id', $('.chart-group:first').data('chart-id'));
                    $('.chart-submit-btn').data('update', 'add');
                    $('#chartTradeModal .chart-submit-btn').trigger('click');
                    $('#chartTradeModal').modal("hide");
                },
                onHide: function (tour) {
                    $('#chartTradeModal').modal("hide");
                },
            },
            {//STEP 5
                element: ".chart-period",
                title: "Report periods",
                content: "By default, data is shown in monthly intervals. You can change the interval to quarterly or annual by clicking these button. Let's try 'Quarterly'.",
                placement: "bottom",
                path: '/trade/country/CAN',
                onNext: function (tour) {
                    $('.chart-period .btn[data-period="quarter"]').trigger('click');
                },
                onPrev: function (tour) {
                    $('.chart-period .btn[data-period="month"]').trigger('click');
                },
            },
            {//STEP 6
                element: ".chart-time",
                title: "Changing the date",
                content: "With this button you can customize the date range for the indicator.",
                placement: "bottom",
                path: '/trade/country/CAN'
            },
            {//STEP 7
                element: '#chartTimeModal .modal-content',
                title: "Selecting dates",
                content: "You can select dates as early as 2010 to as advanced as 2020. We will try " + moment().subtract(3, 'year').format('YYYY') + " to " + moment().add(1, 'year').format('YYYY') + ".",
                placement: "top",
                path: '/trade/country/CAN',
                onShow: function (tour) {
                    var startDate = moment().subtract(3, 'year').startOf('month'),
                        endDate = moment().startOf('month').add(1, 'year'),
                        data = $('.chart-item-data[data-chart-id=' + $('.chart-item:first').data('chart-id') + ']');

                    data.data('start-time', startDate.format('MMM YYYY'));
                    data.data('end-time', endDate.format('MMM YYYY'));

                    $('#chartTimeModal').modal("show");
                    //$('#chartTimeModal').data('chart-id', $('.chart-item:first').data('chart-id'));
                },
                onShown: function (tour) {
                    var step = tour.getCurrentStep();
                    $("body").removeClass(function (index, css) {
                        return (css.match(/(^|\s)tour-step-\S+/g) || []).join(' ');
                    }).addClass('tour-step-' + step);
                    $('.tour #tourOnLogin').prop('checked',(siteData.data('tour-login') == "1" ? true : false))

                    $('.tour-step-background').height($('#chartTimeModal .modal-content').height()).width($('#chartTimeModal .modal-content').width());
                    //$('.tour.popover').css('left', Math.round($('#chartTimeModal .modal-content').width() / 2) + (Math.round($('.tour.popover').width() / 5)));
                },
                onNext: function (tour) {
                    //$('#chartTimeModal').data('chart-id', $('.chart-item:first').data('chart-id'));
                    $('#chartTimeModal .chart-submit-btn').trigger('click');
                    $('#chartTimeModal').modal("hide");
                },
                onPrev: function (tour) {
                    $('#chartTimeModal').modal("hide");
                },
            },
            {//STEP 8
                element: ".chart-item-table:first",
                title: "Large tables",
                content: "You can scroll this bar to see more of the data.",
                placement: "bottom",
                path: '/trade/country/CAN'
            },
            {//STEP 9
                element: ".chart-item:first .dropdown-menu .chart-edit-btn",
                title: "Changing an indicator",
                content: "This time let's try 'Edit Indicator' and change the indicator to 'Imports by Partner'.",
                placement: "left",
                path: '/trade/country/CAN',
                onShow: function (tour) {
                    $('.chart-item:first .btn-group:first').addClass('forcedopen');
                },
                onHide: function (tour) {
                    $('.chart-item:first .btn-group:first').removeClass('forcedopen');
                }

            },
            {//STEP 10
                element: '#chartTradeModal .modal-body',
                title: "Selecting a sector",
                content: "We need to select a sector. You can search for a sector or click 'List All' to see all available sectors.",
                placement: "top",
                path: '/trade/country/CAN',
                onShow: function (tour) {
                    $('#chartTradeModal').modal("show");
                    if (typeof tradeFormPreset === "function") {
                        tradeFormPreset($('.chart-item:first').data('chart-id'));
                    }
                },
                onShown: function (tour) {
                    var step = tour.getCurrentStep();
                    $("body").removeClass(function (index, css) {
                        return (css.match(/(^|\s)tour-step-\S+/g) || []).join(' ');
                    }).addClass('tour-step-' + step);
                    $('.tour #tourOnLogin').prop('checked',(siteData.data('tour-login') == "1" ? true : false))

                    $('.tour-step-background').height($('#chartTradeModal .modal-content').height()).width($('#chartTradeModal .modal-content').width());
                    //$('.tour.popover').css('left', Math.round($('#chartTradeModal .modal-content').width() / 2) + (Math.round($('.tour.popover').width() / 5)));

                    $('#aimIndicator').selectpicker('val', 'trade-sector-iv').trigger('change');

                    setTimeout(function () {
                        $('#chartTradeModal #aimSector-1').typeahead('val', 'P');
                    }, 300);
                    setTimeout(function () {
                        $('#chartTradeModal #aimSector-1').typeahead('val', 'Pe');
                    }, 400);
                    setTimeout(function () {
                        $('#chartTradeModal #aimSector-1').typeahead('val', 'Pet');
                    }, 500);
                    setTimeout(function () {
                        $('#chartTradeModal #aimSector-1').typeahead('val', 'Petr');
                    }, 600);
                    setTimeout(function () {
                        $('#chartTradeModal #aimSector-1').typeahead('val', 'Petro');
                    }, 700);
                    setTimeout(function () {
                        $('#chartTradeModal #aimSector-1').typeahead('val', 'Petrol').typeahead('open');
                    }, 800);
                    setTimeout(function () {
                        $('#chartTradeModal #aimSector-1').typeahead('close');
                        $('#chartTradeModal .chart-search-list-sector').data('field', 'aimSector-1');
                        $('#chartTradeModal .chart-search-list-sector .list-group-item[data-ref=2709]').trigger('click');
                    }, 1800);
                },
                onNext: function (tour) {
                    $('#chartTradeModal .chart-search-list-sector').data('field', 'aimSector-1');
                    $('#chartTradeModal .chart-search-list-sector .list-group-item[data-ref=2709]').trigger('click');
                    $('#chartTradeModal .chart-submit-btn').trigger('click');
                    $('#chartTradeModal').modal("hide");
                },
                onPrev: function (tour) {
                    $('#chartTradeModal').modal("hide");
                },
            },
            {//STEP 11
                orphan: true,
                title: "<i class='fa fa-spinner fa-spin'></i> Loading...",
                content: "",
                placement: "bottom",
                path: '/trade/country/CAN',
                template: "<div class='popover tour'>" +
                "<div class='arrow'></div>" +
                "<h3 class='popover-title'></h3>" +
                "</div>",
                onShown: function (tour) {
                    var step = tour.getCurrentStep();
                    $("body").removeClass(function (index, css) {
                        return (css.match(/(^|\s)tour-step-\S+/g) || []).join(' ');
                    }).addClass('tour-step-' + step);
                    $('.tour #tourOnLogin').prop('checked',(siteData.data('tour-login') == "1" ? true : false))

                    if(typeof $("body").data('tour-force-prev') !== 'undefined' && $("body").data('tour-force-prev') === 'active'){
                        $("body").data('tour-force-prev', 'inactive');
                        tour.prev();
                    }
                },
            },
            {//STEP 12
                element: ".chart-item:first .dropdown-menu .dropdown-item[data-target=#chartCompareModal]",
                title: "Comparing countries",
                content: "Now let's use the 'Compare Country' button to add a comparison country.",
                placement: "left",
                path: '/trade/country/CAN',
                onShow: function (tour) {
                    $('.chart-item:first .btn-group:first').addClass('forcedopen');
                },
                onHide: function (tour) {
                    $('.chart-item:first .btn-group:first').removeClass('forcedopen');
                },
                onPrev: function (tour) {
                    $('body').data('tour-force-prev', 'active');
                },
            },
            {//STEP 13
                element: '#chartCompareModal .modal-body',
                title: "Selecting a comparison country",
                content: "We need to select a comparison country. You can search for a country or click 'List All' to see all available countries.",
                placement: "top",
                path: '/trade/country/CAN',
                onShow: function (tour) {
                    $('#chartCompareModal').modal("show");
                    $('#chartCompareModal').data('chart-id', $('.chart-item:first').data('chart-id'));
                    $('#chartCompareModal').data('chart-id', $('.chart-item:first').data('chart-id'));
                },
                onShown: function (tour) {
                    var step = tour.getCurrentStep();
                    $("body").removeClass(function (index, css) {
                        return (css.match(/(^|\s)tour-step-\S+/g) || []).join(' ');
                    }).addClass('tour-step-' + step);
                    $('.tour #tourOnLogin').prop('checked',(siteData.data('tour-login') == "1" ? true : false))
                    $('#aimIndicator').selectpicker('val', 'trade-sector-iv').trigger('change');

                    setTimeout(function () {
                        $('#chartCompareModal #ccmReporter-1').typeahead('val', 'G');
                    }, 300);
                    setTimeout(function () {
                        $('#chartCompareModal #ccmReporter-1').typeahead('val', 'Ge');
                    }, 400);
                    setTimeout(function () {
                        $('#chartCompareModal #ccmReporter-1').typeahead('val', 'Ger');
                    }, 500);
                    setTimeout(function () {
                        $('#chartCompareModal #ccmReporter-1').typeahead('val', 'Germ');
                    }, 600);
                    setTimeout(function () {
                        $('#chartCompareModal #ccmReporter-1').typeahead('val', 'Germa');
                    }, 700);
                    setTimeout(function () {
                        $('#chartCompareModal #ccmReporter-1').typeahead('val', 'German');
                    }, 800);
                    setTimeout(function () {
                        $('#chartCompareModal #ccmReporter-1').typeahead('val', 'Germany').typeahead('open');
                    }, 900);
                    setTimeout(function () {
                        $('#chartCompareModal #ccmReporter-1').typeahead('close');
                        $('#chartCompareModal .chart-search-list-reporter').data('field', 'ccmReporter-1');
                        $('#chartCompareModal .chart-search-list-reporter .list-group-item[data-ref=DEU]').trigger('click');
                    }, 1800);
                },
                onNext: function (tour) {
                    $('#chartCompareModal').data('chart-id', $('.chart-item:first').data('chart-id'));
                    $('#chartCompareModal .chart-search-list-reporter').data('field', 'ccmReporter-1');
                    $('#chartCompareModal .chart-search-list-reporter .list-group-item[data-ref=DEU]').trigger('click');
                    $('#chartCompareModal .chart-submit-btn').trigger('click');
                    $('#chartCompareModal').modal("hide");
                },
                onPrev: function (tour) {
                    $('#chartCompareModal').modal("hide");
                },
            },
            {//STEP 14
                element: "#tour-step-14",
                title: "Support",
                content: "Click the button in top right of the screen to open the main menu, and then select one of the following 3 options for extra help &amp; support.<br><br>Use the FAQ button to get answers to common questions, use the tutorial button to turn this tutorial on and off or click the support link to contact our support team.",
                placement: "left",
                path: '/trade/country/CAN',
                onShow: function (tour) {
                    $('.header .nav .dropdown').addClass('forcedopen');
                },
                onHide: function (tour) {
                    $('.header .nav .dropdown').removeClass('forcedopen');
                },
            },
            {//STEP 15
                orphan: true,
                title: "<span class='fa fa-trophy'></span> Tutorial Completed",
                content: "Congratulations! You've just finished our nifty tutorial.",
                placement: "bottom",
                path: '/trade/country/CAN',
            },
        ]
    });


    if (siteData.data('tour') == '0') {
        if ($('.pageLoading').is(":visible")) {
            setTimeout(function () {
                initTour(tour);
            }, 10000);
        } else {
            initTour(tour);
        }
    }

    $(document).on('change', '.tour #tourOnLogin', function(){
        var active = 0;
        if($(this).prop('checked')){
            active = 1;
        }
        siteData.data('tour-login',active);

        $.ajax({
            url: siteData.data('url') + '/user/tourLoginToggle',
            dataType: 'json',
            method: 'post',
            data: {
                'active': active,
            }
        });
    });

});

function initTour (tour) {
    if (isBreakpoint('xs')) {
        alert('Please use a larger screen to view the tutorial.');

    } else {
        $('#chartTradeModal').modal("hide");
        $('#chartTimeModal').modal("hide");
        tour.init();

        if (tour.getCurrentStep() > 2) {
            tour.setCurrentStep(2);
            tour.init();
        }

        tour.restart();
        tour.start(true);
        $('body').addClass('tour').addClass('tour-step-' + tour.getCurrentStep()).append('<div class="tour-lock"></div>');
    }
}

function tourAfterDrawn(){
    if ($('body').hasClass('tour-step-2')) {
        $('.tour-step-background').height($('.chart-group:first').height());
    } else if ($('body').hasClass('tour-step-8')) {
        $('.tour-step-background').height($('.chart-item-table:first-child').height()).width($('.chart-item-table:first-child').width());
        setTimeout(function () {
            $('.chart-item-table:first-child .table-responsive').animate({scrollLeft: '+=' + ($('.chart-item-table:first-child .table-responsive .table').width() / 3)}, 1000);
        }, 1000);
        setTimeout(function () {
            $('.chart-item-table:first-child .table-responsive').animate({scrollLeft: '-=' + ($('.chart-item-table:first-child .table-responsive .table').width() / 3)}, 1000);
        }, 2000);

        //$('.chart-item-table:first-child .table-responsive .table').scrollLeft( -250 );
    } else if ($('body').hasClass('tour-step-11')) {
        tour.next();
    } else if ($('body').hasClass('tour-step-12')) {
        $('.chart-item:first .btn-group:first').addClass('forcedopen');
    }
}