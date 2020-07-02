//*******************************
// SHARE
//*******************************

var addthis_config = addthis_config || {};
addthis_config.data_track_addressbar = false;
addthis_config.data_track_clickback = false;

$(function () {

    $(document).on('click', '.chart-btns .ci-share-btn', function () {
        var modal = $('#chartShareModal'),
            btn = $(this),
            chartItem = btn.parents('.chart-item'),
            chartId = chartItem.data('chart-id');

        //if ($('#siteData').data('adblock') === '0') {
            if (typeof chartId !== 'undefined') {
                var chart = chartsLog[chartId]['chartObj'],
                    data = $('.chart-item-data[data-chart-id=' + chartId + ']').data();

                modal.find('#FormShare_title').val(chartItem.find('.chart-item-title').text());

                renderPrintableChart(chartId, 'image', true);
            }
        //}else{
            //console.log('AddThis is blocked');
        //}
    });

    $('#share-mail-form').on('submit', function (e) {
        e.preventDefault();
        $.ajax({
            url: siteData.data('url') + '/site/share/',
            type: 'post',
            dataType: 'json',
            data: $(this).serialize(),
            success: function (returnData) {
                if (returnData.valid) {
                    $('#chartShareModal').modal('hide');
                    addFlashMessage('success', 'Your email has been successfully sent');
                }
            }
        });
    });

});