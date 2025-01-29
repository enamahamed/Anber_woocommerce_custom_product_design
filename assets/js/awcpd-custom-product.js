jQuery(document).ready(function ($) {
    let currentStep = 1; // Start from step 1
    const totalSteps = $('.step').length;

    // Update the progress bar
    function updateProgressBar() {
        $('.progress-step').each(function () {
            const step = $(this).data('step');
            if (step < currentStep) {
                $(this).find('.step-icon').addClass('completed').text('✔');
                $(this).addClass('completed-step').removeClass('active-step');
            } else if (step === currentStep) {
                $(this).addClass('active-step').removeClass('completed-step');
                $(this).find('.step-icon').removeClass('completed').text(step);
            } else {
                $(this).removeClass('active-step completed-step');
                $(this).find('.step-icon').removeClass('completed').text(step);
            }
        });
    }

    // Show a specific step
    function showStep(step) {
        $('.step').hide();
        $(`.step[data-step="${step}"]`).show();
        currentStep = step;
        updateProgressBar();
    }

    // Validate WooCommerce fields in the current step


    // Next step button handler
    $('.next-step').click(function () {
        if (currentStep < totalSteps) {
            showStep(currentStep + 1);
        }

    });

    // Previous step button handler
    $('.previous-step').click(function () {
        if (currentStep > 1) {
            showStep(currentStep - 1);
        }
    });

    // Initialize: Show Step 1 and update progress bar
    showStep(currentStep);






//    $(function () {
//        $("#estimated_delivery_date").datepicker();
//    });
});

jQuery(document).ready(function ($) {
    // Function to update the price display
    function updatePrice() {
        var selectedPrice = 0;

        // Loop through each variation radio button group
        $('.variation-radios input[type="radio"]:checked').each(function () {
            var price = $(this).data('price');
            selectedPrice += parseFloat(price);
        });

        // Update the price display
        $('.awcpd_product_price .amount').text(selectedPrice.toFixed(2));
    }

    // Event listener for radio button change
    $('.variation-radios input[type="radio"]').on('change', function () {
        updatePrice();
    });

    // Initial update of the price display
    updatePrice();
});


//
