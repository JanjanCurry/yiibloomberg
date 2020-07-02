$(function(){

    if ($('.colorPicker').length) {
        $('.colorPicker').spectrum({
            preferredFormat: "hex",
            showInput: true,
            allowEmpty: true
        });

        $('.colorPicker').on("change", function (e) {
            $(this).parents('.input-group').find('.fa').css('color', $(this).val());
        });
    }

});