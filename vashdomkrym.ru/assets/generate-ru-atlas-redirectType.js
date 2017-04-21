
    $(document).ready(function() {
        var BASE_URL = '';

        $('.form').on('change', '#obj_type, #ap_type', function() {
            $('#update_overlay').show();
            $('#is_update').val(1);
            $('#Apartment-form').submit(); return false;
        });
    });
	