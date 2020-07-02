$(function(){

    /*$('#subscription-redirect').click(function () {
        var redirectWindow = window.open($(this).attr('href'), '_blank');
        if(redirectWindow !== null) {
            console.log(redirectWindow);
            redirectWindow.location;
        }
    }).trigger('click');*/

    openInNewTab($('#subscription-redirect').attr('href'));

});