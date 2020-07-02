$(function(){

    $('.export-btn').click(function(e){
        e.preventDefault();
        $('#FormUserExport_export').val(1);
        $(this).parents('form').submit();
        $('#FormUserExport_export').val(0);
    });

});