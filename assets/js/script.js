jQuery(document).ready(function ($) {
    $('#cfp-form').submit(function (e) {
        e.preventDefault();

        var form = $(this); //  store form
        var formData = form.serialize();

        $.post(cfp_ajax_obj.ajax_url, formData, function (response) {
            $('#cfp-response').html(response);

            // reset form (CORRECT)
            form.trigger("reset");
        });
    });
});