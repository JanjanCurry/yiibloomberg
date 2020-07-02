$(function(){

    $('.reporterAvailability .item td').each(function(){
        if(typeof $(this).data('results') !== 'undefined'){
            var html = $(this).html();
            $(this).html('<a href="#" class="availability-inspector-btn">'+html+'</a>');
        }
    });

    $(document).on('click', '.availability-inspector-btn', function(){
        var data = $(this).parents('td').data('results'),
            group = $(this).parents('td').data('group');

        $('#availability-inspector .list-group-item').hide();
        $('#availability-inspector .list-group').hide();
        $('#availability-inspector .list-group-'+group).show();

        if(typeof data !== 'undefined'){
            for(var key in data){
                $('#availability-inspector .list-group-item-'+group+'-'+data[key]).show();
            }
        }

        $('#availability-inspector').modal('show');
    });

    $('.startUpdate').click(function(){
        $('.reporterAvailability .item').addClass('updateAvailability');
        $('.reporterAvailability .item .icon .fa').removeClass().addClass('fa fa-hourglass-start');
        $('.reporterAvailability .item .item-type').addClass('updateAvailabilityType');
        findNextCountry();
    });

    $('.startUpdateSingle').click(function(){
        var item = $(this).parents('.item');
        item.addClass('updateAvailability');
        item.find('.item-type').addClass('updateAvailabilityType');
        item.find('.icon .fa').removeClass().addClass('fa fa-hourglass-start');
        findNextCountry();
    });

});

function findNextCountry(){
    var country = $('.updateAvailability:first');
    if(country.length > 0){
        var icon = country.find('.icon .fa');
        icon.removeClass().addClass('fa fa-spinner fa-spin');

        var cell = country.find('.updateAvailabilityType:first');
        cell.html('<i class="fa fa-spinner fa-spin"></i>');

        $.ajax({
            url: siteData.data('url') + '/admin/reporterAvailabilityUpdate/',
            method: 'POST',
            dataType: 'json',
            data: {
                country: country.data('ref'),
                type: cell.data('type'),
            },
            success: function (returnData) {
                if (returnData.valid) {
                    for(var key in returnData){
                        if(key !== 'valid' && cell.hasClass(key)){
                            if(returnData[key] === 0){
                                returnData[key] = '-';
                            }
                            cell.html(returnData[key]);

                            //country.find('.'+key).html('<a href="#" class="availability-inspector-btn">'+returnData[key]+'</a>');
                        }
                    }
                } else {
                    cell.html('<i class="fa fa-times text-danger"></i>');
                }

                cell.removeClass('updateAvailabilityType');
                if(country.find('.updateAvailabilityType').length === 0) {
                    if(country.find('.fa-times').length > 0){
                        icon.removeClass().addClass('fa fa-times text-danger');
                    }else{
                        icon.removeClass().addClass('fa fa-check text-success');
                    }
                    country.removeClass('updateAvailability');
                }
                findNextCountry();
            },
            error: function(){
                icon.removeClass().addClass('fa fa-times text-danger');
                cell.html('<i class="fa fa-times text-danger"></i>');

                cell.removeClass('updateAvailabilityType');
                if(country.find('.updateAvailabilityType').length === 0) {
                    country.removeClass('updateAvailability');
                }
                findNextCountry();
            },
        });
    }
}