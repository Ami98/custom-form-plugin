function cfp_enqueue_scripts() {
    wp_enqueue_script('cfp-script', plugin_dir_url(__FILE__) . 'js/script.js', array('jquery'), null, true);

    wp_localize_script('cfp-script', 'cfp_ajax_obj', array(
        'ajax_url' => admin_url('admin-ajax.php')
    ));
}
add_action('wp_enqueue_scripts', 'cfp_enqueue_scripts');

jQuery(document).ready(function ($) {
    $('#cfp-form').submit(function (e) {
        e.preventDefault();

        var formData = $(this).serialize();

        $.post(cfp_ajax_obj.ajax_url, formData, function (response) {
            $('#cfp-response').html(response);
        });
    });
});