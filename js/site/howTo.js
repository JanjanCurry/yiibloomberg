$(function(){

    var openDefault = true;
    if(window.location.hash) {
        var $cat = $(window.location.hash);
        if($cat.length > 0 && $cat.hasClass('collapse')){
            openDefault = false;
            $cat.addClass('in');
            $('html,body').animate({
                scrollTop: $cat.parents('.panel').offset().top - $('.navbar-fixed-top').height()
            });
        }
    }

    if(openDefault){
        var $cat = $('#videos-general');
        $cat.addClass('in');
    }

});