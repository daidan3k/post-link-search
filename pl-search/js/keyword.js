jQuery(document).ready(function($) {
    $('input[name="textinput"]').change(function() {
        var keyword = $(this).val();
        var ID = $(this).data('id');
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'update_metadata',
                metakey: '_pl_search_keyword',
                metavalue: keyword,
                row_id: ID
            },
            success: function(response) {
                console.log(response);
            },
            error: function(xhr, status, error) {
                console.error(xhr.responseText);
            }
        });
    });
});