jQuery(document).ready(
    (function ($) {
        $("#services").on("change", function (e) {
            let permission = true;
            let is_add_order = false;
            let checkField = $(".step-1 ").find(
                ".services__form-field input, .services__form-field select"
            );
            let next = $(this).val();
            $("#order_name").val(next);

            let selectedOption = $('#services option[value="' + next + '"]');

            $(this).prop("selectedIndex", 0);
            $(".alert-text").remove();

            checkField.each((i, e) => {
                if ($(e).val() == "") {
                    permission = false;
                    $(
                        "<p class='alert-text' style='margin: 5px 0 0; color: red;'>Please, Insert your value</p>"
                    ).appendTo($(e).parents(".services__form-field"));
                }
            });

            if (permission) {
                selectedOption.prop("selected", true);
                $(".service").hide();
                $("." + next).slideDown(300);

                let title = $(this).find("option:selected").text();
                let info = $(this).attr("data-info");
                let cl_name = $("#name").val();
                let email = $("#email").val();
                let address = $("#address").val();

                if ("0" == info && is_add_order == false) {
                    $.ajax({
                        url: ajax_helper.ajaxurl,
                        type: "POST",
                        data: {
                            action: "insert_order",
                            security: ajax_helper.security,
                            title,
                            cl_name,
                            email,
                            address,
                        },

                        beforeSend: function () {
                            is_add_order = true;
                        },

                        success: function (res) {
                            console.log(res);
                            $("#ser_id").val(res.id);
                        },

                        error: function (error) {
                            console.log(error);
                        },
                    });
                    $(this).attr("data-info", "1");
                }
            }
        });

        //   Show meter through input field
        function setData(params) {}

        $(".price-plan").on("change", function () {
            let value = $(".ser_rang_1").val();
            let basePrice = $(this).data("base-price");
            let actualPrice = value * basePrice;
            let discount_val =
                (actualPrice / 100) * $(".service-1").attr("data-discount");

            if ($(this).find("input").is(":checked")) {
                $(".discount").text(discount_val.toFixed(2));
                $(".srv_c_1_total").text(actualPrice - discount_val);
                $(".service-1").find(".price").text(actualPrice);
            }
        });
        $("#specify-duty").on("change", function () {
            let value = $(".ser_rang_2").val();
            let price = $(".specify-duty").val();
            if (1 == price) {
                price = $("#ser_2_ceiling_price").val();
            } else if (2 == price) {
                price = $("#ser_2_floor_price").val();
            } else {
                price = $("#ser_2_floor_and_ceiling_price").val();
            }

            let actualPrice = (value * price).toFixed(2);
            let discount_val =
                (actualPrice / 100) * $(".service-2").attr("data-discount");
            $(".discount_ser_2").text(discount_val.toFixed(2));
            $(".ser_2_total").text((actualPrice - discount_val).toFixed(2));

            $(".service-2").find(".price").text(actualPrice);
        });
        $(".meter-count").on("change", function () {
            let value = $(this).val();
            let input = `#${$(this).data("input_val")}`;
            $(input).val(value);

            console.log(input);
            const progress = (value / $(input).prop("max")) * 100;
            $(input).css(
                "background",
                `linear-gradient(to right, #27d0ff ${progress}%, #ccc ${progress}%)`
            );
            // Show Actual price
            if ($(this).parents(".service").hasClass("service-1")) {
                $(".service-1 .price-plan").each((i, e) => {
                    let basePrice = $(e).data("base-price");
                    let actualPrice = value * basePrice;
                    let discount_val =
                        (actualPrice / 100) *
                        $(".service-1").attr("data-discount");

                    if ($(e).find("input").is(":checked")) {
                        $(".discount").text(discount_val.toFixed(2));
                        $(".srv_c_1_total").text(
                            (actualPrice - discount_val).toFixed(2)
                        );
                        $(".service-1")
                            .find(".price")
                            .text(actualPrice.toFixed(2));
                    }
                });
            } else if ($(this).parents(".service").hasClass("service-2")) {
                let price = $(".specify-duty").val();
                if (1 == price) {
                    price = $("#ser_2_ceiling_price").val();
                } else if (2 == price) {
                    price = $("#ser_2_floor_price").val();
                } else {
                    price = $("#ser_2_floor_and_ceiling_price").val();
                }

                let actualPrice = (value * price).toFixed(2);
                let discount_val =
                    (actualPrice / 100) * $(".service-2").attr("data-discount");
                $(".discount_ser_2").text(discount_val.toFixed(2));
                $(".ser_2_total").text((actualPrice - discount_val).toFixed(2));
                $(".service-2").find(".price").text(actualPrice);
            } else if ($(this).parents(".service").hasClass("service-3")) {
                $(".service-3 .price-card__amount").each((i, e) => {
                    let basePrice = $(e).data("base-price");
                    let actualPrice = (value * basePrice).toFixed(2);

                    let discount_val =
                        (actualPrice / 100) * $(e).data("discount");
                    let price =
                        Math.round((actualPrice - discount_val) * 100) / 100;
                    $(e)
                        .next(".price-card__offer-text") // Corrected selector
                        .find(".discount_ser_3") // Corrected selector
                        .text(Math.round(discount_val * 100) / 100); // Limit to two decimal places

                    $(e).find(".price").text(price); // Limit to two decimal places
                });
            }
        });
        $(".custom-range-input").on("change", function () {
            let value = $(this).val();

            $(this)
                .parents(".step__meter-count")
                .find(".meter-count")
                .val(value);

            // Show Actual price
            if ($(this).parents(".service").hasClass("service-1")) {
                $(".service-1 .price-plan").each((i, e) => {
                    let basePrice = $(e).data("base-price");
                    let actualPrice = value * basePrice;
                    let discount_val =
                        (actualPrice / 100) *
                        $(".service-1").attr("data-discount");

                    if ($(e).find("input").is(":checked")) {
                        $(".discount").text(discount_val.toFixed(2));
                        $(".srv_c_1_total").text(
                            (actualPrice - discount_val).toFixed(2)
                        );
                        $(".service-1")
                            .find(".price")
                            .text(actualPrice.toFixed(2));
                    }
                });
            } else if ($(this).parents(".service").hasClass("service-2")) {
                let price = $(".specify-duty").val();
                if (1 == price) {
                    price = $("#ser_2_ceiling_price").val();
                } else if (2 == price) {
                    price = $("#ser_2_floor_price").val();
                } else {
                    price = $("#ser_2_floor_and_ceiling_price").val();
                }

                let actualPrice = (value * price).toFixed(2);
                let discount_val =
                    (actualPrice / 100) * $(".service-2").attr("data-discount");
                $(".discount_ser_2").text(discount_val.toFixed(2));
                $(".ser_2_total").text((actualPrice - discount_val).toFixed(2));
                $(".service-2").find(".price").text(actualPrice);
            } else if ($(this).parents(".service").hasClass("service-3")) {
                $(".service-3 .price-card__amount").each((i, e) => {
                    let basePrice = $(e).data("base-price");
                    let actualPrice = (value * basePrice).toFixed(2);

                    let discount_val =
                        (actualPrice / 100) * $(e).data("discount");
                    let price =
                        Math.round((actualPrice - discount_val) * 100) / 100;
                    $(e)
                        .next(".price-card__offer-text") // Corrected selector
                        .find(".discount_ser_3") // Corrected selector
                        .text(Math.round(discount_val * 100) / 100); // Limit to two decimal places

                    $(e).find(".price").text(price); // Limit to two decimal places
                });
            }
        });
        let is_face = false;
        $("body").on("click", ".form__submit", function () {
            let form = $(this).closest("form");
            let val = $(this).attr("data-service-3");

            form.find("#service_3_value").val(val);

            form.submit();
        });

        $("#services__form").submit(function (event) {
            // Prevent the form from submitting normally
            event.preventDefault();

            // Call the function to submit the form via AJAX
            submitForm();
        });

        function submitForm() {
            // Serialize form data into an array
            var formDataArray = $("#services__form").serializeArray();
            // Create an empty object to store the structured data
            var formData = {};
            // Iterate over the form data array
            $.each(formDataArray, function (index, field) {
                // Split the field name into keys based on brackets
                var keys = field.name.match(/[^\[\]]+/g);
                // Reference to the current level of the structured data
                var currentLevel = formData;
                // Iterate over the keys to build the nested structure
                for (var i = 0; i < keys.length; i++) {
                    // If the key is the last one, assign the field value
                    if (i === keys.length - 1) {
                        currentLevel[keys[i]] =
                            currentLevel[keys[i]] || field.value;
                    } else {
                        // Create a new empty object if the key doesn't exist
                        currentLevel[keys[i]] = currentLevel[keys[i]] || {};
                        // Move to the next level
                        currentLevel = currentLevel[keys[i]];
                    }
                }
            });

            if (formData["ser_id"] && !is_face) {
                // console.log(formData);
                $.ajax({
                    url: ajax_helper.ajaxurl,
                    type: "POST",
                    data: {
                        action: "insert_order_details",
                        security: ajax_helper.security,
                        formData,
                    },
                    beforeSend: function () {
                        is_face = true;
                    },
                    success: function (res) {
                        console.log(res);
                        is_face = false;
                        if (res.redirect_to) {
                            window.location = res.redirect_to;
                        }
                    },
                    error: function (error) {
                        console.log(error);
                    },
                });
            }
        }

        let sliderEl = $(".custom-range-input");

        sliderEl.on("input", function (event) {
            const tempSliderValue = $(this).val();
            let input = `.${$(this).data("input_val")}`;
            $(input).val(tempSliderValue);

            const progress = (tempSliderValue / $(this).prop("max")) * 100;

            $(this).css(
                "background",
                `linear-gradient(to right, #27d0ff ${progress}%, #ccc ${progress}%)`
            );
        });
    })(jQuery)
);
