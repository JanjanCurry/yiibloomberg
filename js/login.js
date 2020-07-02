"use strict";
var siteData = $('#siteData');

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

//*******************************
// NAVIGATION
//*******************************
$(function () {

    // Disable link clicks to prevent page scrolling
    $(document).on('click', 'a[href="#"]', function (e) {
        e.preventDefault();
    });

});

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