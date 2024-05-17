jQuery(document).ready(function($) {
    $('select[name="dropdown"]').change(function() {
        var selectedOption = $(this).val();
        var ID = $(this).data('id');
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'update_metadata',
                metakey: '_pl_search_engine',
                metavalue: selectedOption,
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