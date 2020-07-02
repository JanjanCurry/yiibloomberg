$(function(){

    $('#home-fav').on('slide.bs.carousel', function () {
        homeResizeCharts();
    });

    $('.favorites-list-toggle').click(function(){
        $(this).trigger('resizeEnd');

        var selector = '.'+$(this).data('toggle');
        if($(selector).data('state') == 'open'){
            $(selector).data('state', 'closed')
        }else{
            $(selector).data('state', 'open')
        }
        $.ajax({
            url: siteData.data('url') + '/site/index',
            dataType: 'json',
            method: 'post',
            data: {
                'displayPref': 1,
                'macro-fav-tab': $('.macro-favorites').data('state'),
                'trade-fav-tab': $('.trade-favorites').data('state'),
                'commodity-fav-tab': $('.commodity-favorites').data('state'),
            }
        });
    });

});


function homeResizeCharts(){
    if($('#home-fav').data('resized') != 'true') {
        var active = $('#home-fav .item.active')

        $('#home-fav .carousel-inner').css({
            width: '100%',
            height: active.height(),
            overflow: 'hidden',
        });

        $('#home-fav .item').each(function () {
            if(!$(this).hasClass('active')){
                $(this).addClass('active');

                $(this).find('.chart-group').each(function () {
                    var chartId = $(this).data('chart-id');

                    if (typeof chartsLog[chartId] !== 'undefined') {
                        toggleChartSeries(chartId);
                        //chartsLog[chartId].chartObj.draw(chartsLog[chartId].chartData, chartsLog[chartId].options);
                        //chartsLog[chartId].chartObjPrint.draw(chartsLog[chartId].chartData, chartsLog[chartId].printOptions);
                    }
                });

                $(this).removeClass('active');
            }
            $('#home-fav').data('resized', 'true');
        });
    }
}