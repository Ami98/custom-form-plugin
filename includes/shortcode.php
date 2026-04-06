<?php

function cfp_form_shortcode()
{
    ob_start();
?>

    <form id="cfp-form">
        <input type="text" name="name" placeholder="Name" required>
        <input type="email" name="email" placeholder="Email" required>
        <textarea name="message" placeholder="Message"></textarea>

        <input type="hidden" name="action" value="cfp_submit_form">
        <?php wp_nonce_field('cfp_nonce_action', 'cfp_nonce'); ?>

        <button type="submit">Submit</button>
    </form>

    <div id="cfp-response"></div>

<?php
    return ob_get_clean();
}

add_shortcode('custom_form', 'cfp_form_shortcode');
