$(document).ready(function() {
    // Show/hide bulk action bar
    function toggleBulkBar() {
        if ($('.rowCheckbox:checked').length > 0) {
            $('#bulkActionBar').show();
        } else {
            $('#bulkActionBar').hide();
        }
    }

    // Select all checkboxes
    $('#selectAll').on('change', function() {
        $('.rowCheckbox').prop('checked', this.checked);
        toggleBulkBar();
    });

    // Individual row checkbox
    $(document).on('change', '.rowCheckbox', function() {
        if (!this.checked) {
            $('#selectAll').prop('checked', false);
        }
        toggleBulkBar();
    });

    // Hide bar on load
    $('#bulkActionBar').hide();
});
