jQuery(document).ready(function () {
    // Disable all input fields on page load
    jQuery(".ser_page .input").prop("disabled", true);
    jQuery(".ser_page .active .input").prop("disabled", false);

    jQuery(".tab-link").click(function () {
        var tabID = jQuery(this).attr("data-tab");

        jQuery(this).addClass("active").siblings().removeClass("active");

        jQuery("#tab-" + tabID)
            .addClass("active")
            .siblings()
            .removeClass("active");

        jQuery(".ser_page .input").prop("disabled", true);
        jQuery(".ser_page .active .input").prop("disabled", false);
    });
});
