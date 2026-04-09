
/*

        e.preventDefault() → stop page reload
        serialize() → collect form data
        $.post() → send AJAX request
        Response shown in #cfp-response
        form.trigger("reset") → reset form after submission
*/


// jQuery(document).ready(function ($) {
//     $('#cfp-form').submit(function (e) {
//         e.preventDefault();

//         let form = new FormData(this); //  store form
//         var formData = form.serialize();

//         $.post(cfp_ajax_obj.ajax_url, formData, function (response) {
//             $('#cfp-response').html(response);

//             // reset form (CORRECT)
//             form.trigger("reset");
//         });
//     });
// });


document.getElementById("cfp-form").addEventListener("submit", function (e) {
    e.preventDefault();
    let form = this; // define form here
    let formData = new FormData(this);
    formData.append("action", "cfp_save");
    formData.append("nonce", cfp_ajax.nonce);

    fetch(cfp_ajax.ajax_url, {
        method: "POST",
        body: formData
    })
        .then(res => res.text())
        .then(data => {
            document.getElementById("cfp-message").innerText = data;

            // Reset form after success
            form.reset();

        });
});