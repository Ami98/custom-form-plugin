
/*
        e.preventDefault() → stop page reload
        fetch() → send AJAX request
        Response shown in #cfp-response
        form.reset() → reset form after submission
*/

document.addEventListener("DOMContentLoaded", function () {

    const form = document.getElementById("cfp-form-new");

    if (form) {
        form.addEventListener("submit", function (e) {
            e.preventDefault();

            const formData = new FormData(form);

            fetch(cfp_rest.rest_url, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-WP-Nonce": cfp_rest.nonce
                },
                body: JSON.stringify({
                    name: formData.get("name"),
                    email: formData.get("email"),
                    message: formData.get("message")
                })
            })
            .then(res => res.json())
            .then(data => {
                document.getElementById("cfp-message").innerText = data.message;

                if (data.status === "success") {
                    form.reset();
                }
            })
            .catch(error => {
                console.error("Error:", error);
            });
        });
    }

}); // ✅ THIS WAS MISSING