jQuery(document).ready(function () {

    // Show review popup
    jQuery("#ezd_notify_review a").on("click", function () {
        const thisElement = this;
        const fieldValue = jQuery(thisElement).attr("data");
        const freeLink = "https://wordpress.org/support/plugin/eazydocs/reviews/#new-post";
        let hidePopup = false;
        if (fieldValue == "rateNow") {
            window.open(freeLink, "_blank");
        } else {
            hidePopup = true;
        }

        jQuery
            .ajax({
                dataType: 'json',
                url: eazydocs_local_object.ajaxurl,
                type: "post",
                data: {
                    action: "ezd_notify_save_review",
                    field: fieldValue,
                    nonce: eazydocs_local_object.nonce,
                },
            })
            .done(function (result) {
                if (hidePopup == true) {
                    jQuery("#ezd_notify_review .notice-dismiss").trigger("click");
                }
            })
            .fail(function (res) {
                if (hidePopup == true) {
                    console.log(res.responseText);
                    jQuery("#ezd_notify_review .notice-dismiss").trigger("click");
                }
            });
    })
})