
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

document.addEventListener("DOMContentLoaded", function () {

    const form = document.getElementById("cfp-form");
    const messageBox = document.getElementById("cfp-message");

    if (!form) {
       // console.error("Form not found!");
        return;
    }

    form.addEventListener("submit", function (e) {
        e.preventDefault();

        const formData = new FormData(form);
        formData.append("action", "cfp_save");
        formData.append("nonce", cfp_ajax.nonce);

        // Disable button (UX improvement)
        const button = form.querySelector("button");
        if (button) button.disabled = true;

        fetch(cfp_ajax.ajax_url, {
            method: "POST",
            body: formData
        })
        .then(res => res.text())
        .then(data => {

            if (messageBox) {
                messageBox.innerText = data;
            }

            // Reset only if success
            if (data.includes("success")) {
                form.reset();
            }

        })
        .catch(error => {
            console.error("Error:", error);

            if (messageBox) {
                messageBox.innerText = "Something went wrong!";
            }
        })
        .finally(() => {
            if (button) button.disabled = false;
        });

    });

});
