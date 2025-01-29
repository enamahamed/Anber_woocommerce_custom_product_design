//jQuery(document).ready(function ($) {
//    let currentStep = 1; // Start from step 1
//    const totalSteps = $('.step').length;
//
//    // Update the progress bar
//    function updateProgressBar() {
//        $('.progress-step').each(function () {
//            const step = $(this).data('step');
//            if (step < currentStep) {
//                $(this).find('.step-icon').addClass('completed').text('✔');
//                $(this).addClass('completed-step').removeClass('active-step');
//            } else if (step === currentStep) {
//                $(this).addClass('active-step').removeClass('completed-step');
//                $(this).find('.step-icon').removeClass('completed').text(step);
//            } else {
//                $(this).removeClass('active-step completed-step');
//                $(this).find('.step-icon').removeClass('completed').text(step);
//            }
//        });
//    }
//
//    // Show a specific step
//    function showStep(step) {
//        $('.step').hide();
//        $(`.step[data-step="${step}"]`).show();
//        currentStep = step;
//        updateProgressBar();
//    }
//
//    // Validate WooCommerce fields in the current step
//    function validateStep() {
//        let isValid = true;
//        let firstErrorField = null;
//
//        // Use WooCommerce's validation for checkout fields
//        $(`.step[data-step="${currentStep}"] .form-row .input-text, .step[data-step="${currentStep}"] .form-row input, .step[data-step="${currentStep}"] .form-row select`).each(function () {
//            const fieldWrapper = $(this).closest('.form-row');
//            const isRequired = fieldWrapper.hasClass('validate-required') || $(this).hasClass('required');
//            const fieldValue = $(this).val();
//
//            // If the field is required and empty, mark it as invalid
//            if (isRequired && (!fieldValue || fieldValue.trim() === '')) {
//                isValid = false;
//                fieldWrapper.addClass('woocommerce-invalid').removeClass('woocommerce-validated');
//                if (!firstErrorField) {
//                    firstErrorField = $(this);
//                }
//            } else {
//                fieldWrapper.addClass('woocommerce-validated').removeClass('woocommerce-invalid');
//            }
//        });
//
//        if (!isValid && firstErrorField) {
//            // Scroll to the first invalid field
//            $('html, body').animate({
//                scrollTop: firstErrorField.offset().top - 20
//            }, 500);
//            firstErrorField.focus();
//        }
//
//        return isValid;
//    }
//
//    // Next step button handler
//    $('.next-step').click(function () {
//        if (validateStep()) {
//            if (currentStep < totalSteps) {
//                showStep(currentStep + 1);
//            }
//        }
//    });
//
//    // Previous step button handler
//    $('.previous-step').click(function () {
//        if (currentStep > 1) {
//            showStep(currentStep - 1);
//        }
//    });
//
//    // Initialize: Show Step 1 and update progress bar
//    showStep(currentStep);
//
//    // Additional code from the second script
//    $('#custom_country_field').addClass('required');
//
//    function toggleRequiredFields() {
//        if ($('#sp_re_yes').is(':checked')) {
//            // Add required class to address fields
//            $('#return_address_line, #delivery_address_street, #return_address_city, #return_address_post, #return_address_country').addClass('required');
//        } else {
//            // Remove required class from address fields
//            $('#return_address_line, #delivery_address_street, #return_address_city, #return_address_post, #return_address_country').removeClass('required');
//        }
//    }
//
//    // Run the toggle function on page load (in case of a pre-selected radio button)
//    toggleRequiredFields();
//
//    // Listen for changes on the radio buttons
//    $('input[name="sp_re"]').change(function () {
//        toggleRequiredFields();
//    });
//
//    $(function () {
//        $("#estimated_delivery_date").datepicker();
//    });
//});
//





//showStep(currentStep); // Step 1 is active by default
//     $(function () {
//        $("#estimated_delivery_date").datepicker();
//    });

function showAdditionalField(value) {
    var delivery_address = document.getElementById('delivery_address');
    var returning_documents = document.getElementById('returning_documents');
    var spacific_returning = document.getElementById('spacific_returning_option');

    if (value === 'awc_inoffice_df') {
        delivery_address.style.display = 'flex';
        returning_documents.style.display = 'block';
        spacific_returning.style.display = 'none';
    } else if (value === 'awc_return_shipping') {
        returning_documents.style.display = 'block';
        delivery_address.style.display = 'none';
        spacific_returning.style.display = 'block';
    } else {
        delivery_address.style.display = 'none';
        returning_documents.style.display = 'none';
        spacific_returning.style.display = 'none';
    }
} // Call the function on load to set the initial state 

document.addEventListener('DOMContentLoaded', function () {
    var selectedOption = document.querySelector('input[name="delivery_option"]:checked');
    if (selectedOption) {
        showAdditionalField(selectedOption.value);
    }
});

document.addEventListener('DOMContentLoaded', function () {
    const inofficedrop = document.getElementById('delivery_option_awc_inoffice_df');
    const spReNo = document.getElementById('sp_re_no');
    const spReYes = document.getElementById('sp_re_yes');
    const returningDocumentsWrapper = document.getElementById('returning_documents_wrapper');
    const allOptions = returningDocumentsWrapper.querySelectorAll('.radio_button.returning_documents');
    const collectInPersonOption = document.getElementById('returning_documents_0');
    const rsa = document.getElementById('returnSA');

    function updateOptions() {
        if (spReNo.checked || inofficedrop.checked) {
            rsa.style.display = 'none';
            // Show only the "I'll Collect in Person" option
            allOptions.forEach(option => {
                if (option !== collectInPersonOption) {
                    option.style.display = 'none';
                } else {
                    option.style.display = 'flex'; // Show as needed (block, flex, etc.)
                }
            });
        } else {
            rsa.style.display = 'block';
            // Show all options
            allOptions.forEach(option => {
                option.style.display = 'flex';
            });
        }
    }

    // Add event listeners to the radio buttons
    spReNo.addEventListener('change', updateOptions);
    spReYes.addEventListener('change', updateOptions);
    inofficedrop.addEventListener('change', updateOptions);

    // Initialize the visibility based on the default state
    updateOptions();
});


document.addEventListener('DOMContentLoaded', function () {
    const ndNo = document.getElementById('name_of_documents_no');
    const ndYes = document.getElementById('name_of_documents_yes');
    const name_of_documents = document.getElementById('name_of_documents');
    function updateOptions() {
        if (ndNo.checked) {
            name_of_documents.style.display = 'block';
        } else {
            name_of_documents.style.display = 'none';
        }
    }
    // Add event listeners to the radio buttons
    ndNo.addEventListener('change', updateOptions);
    ndYes.addEventListener('change', updateOptions);

    // Initialize the visibility based on the default state
    updateOptions();
});
document.addEventListener('DOMContentLoaded', function () {
    const adsn1 = document.getElementById('adition_service_options_1');
    const which_language = document.getElementById('which_language');
    const other_language = document.querySelectorAll('input[name="adition_service_options"]');

    const adsn = document.getElementById('adition_service_options_2');
    const which_embassy = document.getElementById('which_embassy');

    function updateOptions() {
        if (adsn1.checked) {
            which_language.style.display = 'block';
        } else {
            which_language.style.display = 'none';
        }

        if (adsn.checked) {
            which_embassy.style.display = 'block';
        } else {
            which_embassy.style.display = 'none';
        }
    }

    other_language.forEach(option => option.addEventListener('change', updateOptions));

    // Initialize the visibility based on the default state
    updateOptions();
});


document.addEventListener('DOMContentLoaded', function () {
    const addressLineField = document.getElementById('return_address_line');
    const streetField = document.getElementById('delivery_address_street');
    const cityField = document.getElementById('return_address_city');
    const postcodeField = document.getElementById('return_address_post');
    const countryField = document.getElementById('return_address_country');

    const addressPreviewTitle = document.getElementById('preview_return_address');
    const previewAddressLine = document.querySelector('#preview_address_line');
    const previewStreet = document.querySelector('#preview_street');
    const previewCity = document.querySelector('#preview_city');
    const previewPostcode = document.querySelector('#preview_postcode');
    const previewCountry = document.querySelector('#preview_country span');


    function updatePreview() {
        const addressLineValue = addressLineField.value.trim();
        const streetValue = streetField.value.trim();
        const cityValue = cityField.value.trim();
        const postcodeValue = postcodeField.value.trim();
        const countryValue = countryField.value.trim();

        previewAddressLine.textContent = addressLineValue;
        previewStreet.textContent = streetValue;
        previewCity.textContent = cityValue;
        previewPostcode.textContent = postcodeValue;
        previewCountry.textContent = countryValue;

        // Show or hide the title based on the fields' values
        if (addressLineValue || streetValue || cityValue || postcodeValue || countryValue) {
            addressPreviewTitle.style.display = 'block';
        } else {
            addressPreviewTitle.style.display = 'none';
        }
    }

    // Add event listeners to update preview on input
    addressLineField.addEventListener('input', updatePreview);
    streetField.addEventListener('input', updatePreview);
    cityField.addEventListener('input', updatePreview);
    postcodeField.addEventListener('input', updatePreview);
    countryField.addEventListener('input', updatePreview);

    // Initial check to set the visibility of the title on page load
    updatePreview();
});



