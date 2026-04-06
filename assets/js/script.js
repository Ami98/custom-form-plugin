jQuery(document).ready(function ($) {
    $('#cfp-form').submit(function (e) {
        e.preventDefault();

        var formData = $(this).serialize();

        $.post(cfp_ajax_obj.ajax_url, formData, function (response) {
            $('#cfp-response').html(response);

            // ✅ CLEAR FORM AFTER SUCCESS
            form.trigger("reset");
        });
    });
});