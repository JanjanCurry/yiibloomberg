"use strict";
var siteData = $('#siteData');
var chartsLog = {};

//*******************************
// INIT
//*******************************
$(function () {
    //switch bootstrap hidden to jquery hide() on load
    initHidden();
    initBreakpoint();
    deviceSpec();
    if (!isBreakpoint('xs')) {
        new WOW().init();
    }
    loadStyleSheets();
    positionFooter();


    setTimeout(function () {
        if($('.page-loading-default').is(':visible')){
            $('.page-loading').css({
                '-webkit-animation': 'none',
                '-moz-animation': 'none',
                '-ms-animation': 'none',
                'animation': 'none',
                'padding-top': '0'
            }).fadeOut('fast');
        }
    }, 500);
    setTimeout(function () {
        $('.page-loading').hide();
    }, 4000);
    $('.page-loading').click(function () {
        $(this).css({
            '-webkit-animation': 'none',
            '-moz-animation': 'none',
            '-ms-animation': 'none',
            'animation': 'none',
            'padding-top': '0'
        }).hide();
    });

    // Loader
    $(window).load(function () {
        $('.loader-bg').fadeOut(1000, function () {
            if (!isBreakpoint('xs')) {
                //$(".nav-btn").trigger('click');
            }
        });
    });

    $(window).on('resize', function () {
        positionFooter();
    });

    /*$(document).ajaxStart(function () {
        $('.ajaxLoading').stop().fadeIn(500);
    });
    $(document).ajaxStop(function () {
        $('.ajaxLoading').stop().fadeOut(500);
    });*/

    $(document).on('click', '.toggleBtn', function () {
        $('.' + $(this).data('toggle')).slideToggle();
        if ($(this).find('.fa-caret-down').length > 0) {
            $(this).find('.fa-caret-down').addClass('fa-caret-right').removeClass('fa-caret-down');
        } else if ($(this).find('.fa-caret-right').length > 0) {
            $(this).find('.fa-caret-right').addClass('fa-caret-down').removeClass('fa-caret-right');
        }
    });

});


function initHidden () {
    $('.hidden').hide().removeClass('hidden');
}

function loadStyleSheets() {
    var data = $('#deferred-css').data('src');
    if(typeof data !== 'undefined'){
        for(var key in data){
            if (document.createStyleSheet){
                document.createStyleSheet(data[key]);
            } else {
                $("head").append($("<link rel='stylesheet' href='"+data[key]+"' type='text/css' media='screen' />"));
            }
        }
    }
    $('#deferred-css').remove();
}


//*******************************
// WINDOW RESIZE
//*******************************
function deviceSpec () {
    /*$.ajax({
     url: siteData.data('url') + '/site/deviceSpec/',
     data: 'width=' + window.screen.width + '&height=' + window.screen.height
     });*/

}

function initBreakpoint () {
    var breakPoints = ['xs', 'sm', 'md', 'lg'];
    $.each(breakPoints, function (key, val) {
        $('body').append('<div class="device-' + val + ' visible-' + val + '"></div>');
    });
}

function getBreakpoint () {
    var breakPoints = ['xs', 'sm', 'md', 'lg'];
    for (var i in breakPoints) {
        if ($('.device-' + breakPoints[i]).is(':visible')) {
            return breakPoints[i];
        }
    }
}

function isBreakpoint (alias) {
    return $('.device-' + alias).is(':visible');
}

function positionFooter () {
    /*$('html, body').css({
        'overflow': 'hidden',
        'height': '100%'
    });*/
    //var contentHeight = ($(window).height() - ($('.header .navbar').outerHeight() + $('.footer').outerHeight())) - 29;
    var contentHeight = ($(window).height() - ($('.header .navbar').outerHeight())) - 29;
    $('.content').css('min-height', contentHeight);
    setTimeout(function () {
        /*$('html, body').css({
            'overflow': 'auto',
            'height': 'auto'
        });*/
    }, 800);
}

//*******************************
// NAVIGATION
//*******************************
$(function () {

    $('.btn').mouseup(function(){
        $(this).blur();
    });

    $(document).on('click', 'a[href="#"]', function (e) {
        e.preventDefault();
    });

    // Disable link clicks to prevent page scrolling
    $(document).on('click', 'a', function (e) {
        if(typeof $(this).attr('href') !== 'undefined' &&
            $(this).attr('href').indexOf('#') === -1 &&
            $(this).attr('href').indexOf('/images/') === -1 &&
            $(this).attr('target') != '_blank' &&
            !$(this).hasClass('delete-confirm-link')
        ){
            e.preventDefault();
            $('.page-loading .page-loading-content').hide();
            $('.page-loading').fadeIn('fast');
            window.location.href = $(this).attr('href');
        }
    });

    $('.main-menu-toggle').click(function () {
        var $menu = $('.wrap-menu');
        if($menu.hasClass('active')){
            $(this).removeClass('active').blur();
            $menu.removeClass('active');
        }else{
            $(this).addClass('active');
            $menu.addClass('active');
        }
    });

});

function openInNewTab(url) {
    var win = window.open(url, '_blank');
    if (win) {
        win.focus();
    }
}

//*******************************
// NOTIFICATIONS
//*******************************
$(function () {
    showFlashMessage();
    $('#notifications').on('click', '.notifications-overlay, .notifications-container', function () {
        $('#notifications').fadeOut(500, function(){
            $('#notifications').find('.alert').remove();
        });
    });

    $(document).on('click', '.delete-confirm-link', function (event) {
        event.preventDefault();
        if (confirm('Are you sure you want to delete this?')) {
            window.location = $(this).attr('href');
        }
    });

    $(document).on('submit', '.delete-confirm-form', function (event) {
        if (typeof $(this).data('delete-confirm') === 'undefined' || $(this).data('delete-confirm') != '1') {
            event.preventDefault();
            if (confirm('Are you sure you want to delete this?')) {
                $(this).data('delete-confirm', '1');
                $(this).submit();
            }
        }
    });

    $('.modal').addClass('animated bounceInDown fade').removeClass('fade-scale').find('.modal-dialog').addClass('');
    $('.modal').on('hidden.bs.modal', function ( evt ) {
        $(this).find('.modal-dialog').removeClass('bounceInDown animated').addClass('bounceInDown animated');
    });
});

function showFlashMessage () {
    if ($('#notifications .alert').length > 0) {
        $('#notifications').show();
        setTimeout(function(){
            $('#notifications').fadeOut(500, function(){
                $('#notifications').find('.alert').remove();
            });
        },4000)
    }
}

function addFlashMessage (type, msg) {
    if ($.isArray(msg)) {
        var temp = '';
        for (var key in msg) {
            temp += '<li>' + msg[key] + '</li>';
        }
        msg = temp;
    } else {
        msg = '<li>' + msg + '</li>';
    }
    $('#notifications .notifications-container').append('<div class="alert alert-' + type + '"><ul>' + msg + '</ul></div>');
    showFlashMessage();
}


//*******************************
// ALERTS
//*******************************
$(function(){
    $(document).on('click', '.user-alert-list .dropdown-toggle', function(){
        $.ajax({url: siteData.data('url') + '/alert/seen/'});
    });

    $(document).on('click', '.user-alert-list .user-alert-item', function(){
        var $item = $(this);
        $.ajax({
            url: siteData.data('url') + '/alert/view/'+$(this).data('id'),
            success: function(data){
                if(data.valid) {
                    $('#alertView .alert-content').html(data.message);
                    $('#alertView').modal('show');
                    $item.find('.fa').removeClass('fa-circle').addClass('fa-circle-o');
                }
            }
        });
    });
});


//*******************************
// SEARCH
//*******************************
$(function () {
    var searchField = $('#search'),
        defaultSearchTimer;

    var dataset = new Bloodhound({
        datumTokenizer: Bloodhound.tokenizers.obj.whitespace('value'),
        queryTokenizer: Bloodhound.tokenizers.whitespace,
        remote: {
            url: siteData.data('url') + '/autoComplete/search?term=%QUERY',
            wildcard: '%QUERY'
        },
        sufficient: 10
    });
    dataset.initialize();

    searchField.typeahead({
        hint: false,
        highlight: true,
        minLength: 2,
    }, {
        limit: 5000,
        name: 'search',
        displayKey: 'value',
        source: dataset.ttAdapter(),
        templates: {
            suggestion: Handlebars.compile(
                '<div class="search-item">' +
                '<div class="row">' +
                '<div class="col-sm-8 col-md-9">' +
                '{{value}}' +
                '</div>' +
                '<div class="col-sm-4 col-md-3 hidden-xs text-right">' +
                '<small><span class="premium">{{premiumDisplay}}</span> {{typeDisplay}}</small>' +
                '</div>' +
                '</div>' +
                '</div>'
            )
        }
    });

    searchField.on("typeahead:selected", function (object, datum, name) {
        if (datum.premium) {
            var win = window.open(siteData.data('url') + '/upgrade', '_blank');
            win.focus();
            //window.location.replace(siteData.data('url') + '/premium');
        } else {
            switch(datum.type){
                case 'commodity':
                    window.location.href = siteData.data('url') + '/commodity/code/' + datum.commodity;
                    break;

                case 'currency':
                    window.location.href = siteData.data('url') + '/currency/code/' + datum.currency;
                    break;

                case 'equity':
                    window.location.href = siteData.data('url') + '/equity/code/' + datum.equity;
                    break;

                case 'trade':
                    window.location.href = siteData.data('url') + '/trade/country/' + datum.reporter;
                    break;

                case 'tradeIndicator':
                    window.location.href = siteData.data('url') + '/trade/country/' + datum.reporter + '?indicator=' + datum.indicator;
                    break;

                case 'macro':
                    window.location.href = siteData.data('url') + '/macro/index?macro=' + datum.macro;
                    break;

                case 'macroReporter':
                    window.location.href = siteData.data('url') + '/macro/index?macro=' + datum.macro + '&reporter=' + datum.reporter;
                    break;

                default:
                    window.location.href = siteData.data('url') + '/search/';
            }
        }
        //$('#search').typeahead('val','');
    });

    searchField.on('keyup', function (e) {
        if (e.which == 13) {
            $(".tt-suggestion:first-child").trigger('click');
        }
    });

    searchField.on('focus', function () {
        if (searchField.val() == '') {
            searchField.val('default').trigger('input').val('');
        }
    });

    searchField.on('focusout', function () {
        setTimeout(function () {
            if (searchField.val() == 'default') {
                searchField.val('').trigger('input');
            }
        }, 10);
    });

});

//*******************************
// FORM UTILITIES
//*******************************
$(function () {
    refreshForm();
});

function refreshForm () {
    $(document).on('click', '.input-group-addon', function () {
        $(this).siblings('input, select').focus();
    });

    // Tooltips
    $('[data-toggle=tooltip]').tooltip({
        'html': true,
        trigger: 'hover'
    });

}


//clear all details for certain group of fields
function clearDetails (objectId, parent) {
    parent = typeof parent !== 'undefined' ? parent : '';
    $(parent + ' [id^=' + objectId + '_][type!=checkbox]').val('');
    $(parent + ' [id^=' + objectId + '_][type=checkbox]').prop('checked', false);
    $(parent + ' [id^=' + objectId + '_]').trigger('change');
}

//fill details for a certain group of fields
function fillDetails (objectId, details, parent) {
    parent = typeof parent !== 'undefined' ? parent : '';
    var key = '';
    for (key in details) {
        if ($.isArray(details[key])) {
            if ($(parent + ' #' + objectId + '_' + key).length > 0) {
                $(parent + ' #' + objectId + '_' + key).val(details[key].join());
                $(parent + ' #' + objectId + '_' + key).trigger('change');
            } else {
                $(parent + ' [id^=' + objectId + '_' + key + ']').each(function (i) {
                    if ($.inArray($(this).val(), details[key]) >= 0) {
                        $(this).prop('checked', true);
                    } else {
                        $(this).prop('checked', false);
                    }
                    $(this).trigger('change');
                });
            }
        } else {
            $(parent + ' #' + objectId + '_' + key).val(details[key]);
            $(parent + ' #' + objectId + '_' + key).trigger('change');
        }
    }
}