$(function(){

    tinymce.init({
        selector:'#FormAddAlert_message',
        plugins: "link, image, code",
        branding: false,
        //statusbar: false,
        menubar: false,
        toolbar: 'undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | outdent indent hr | removeformat | link image | code',
        image_list: siteData.data('url')+"/admin/tinyMceImages"
    });

    $('.save-btn').click(function(e){
        e.preventDefault();
        $('#FormAddAlert_save').val(1);
        $(this).parents('form').submit();
        $('#FormAddAlert_save').val(0);
    });

});