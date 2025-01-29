<?php

/* 
Single product customize
 *  */




/* 
Single product show_minimum_price_only
 *  */
add_filter('woocommerce_get_price_html', 'awcpd_show_minimum_price_only', 10, 2);
function awcpd_show_minimum_price_only($price, $product) {
    // Check if the product is a variable product
    if ($product->is_type('variable')) {
        // Get the minimum price
        $min_price = $product->get_variation_price('min', true);

        // Format the price with WooCommerce currency settings
        $formatted_price = wc_price($min_price);

        // Return only the minimum price
        return $formatted_price;
    }

    // Return the original price for other product types
    return $price;
}

add_filter('woocommerce_add_to_cart_redirect', 'custom_add_to_cart_redirect');

function custom_add_to_cart_redirect($url) {
    return wc_get_checkout_url(); // Redirect to the checkout page
}

/*
  Variation radio button
 *  */


add_action('woocommerce_variable_add_to_cart', function () {
    add_action('wp_print_footer_scripts', function () {
        ?>
        <script type="text/javascript">
            document.addEventListener('DOMContentLoaded', function () {
                console.log('Script loaded');

                // Find the variations form
                var variationsForm = document.querySelector('form.variations_form');
                if (!variationsForm) {
                    console.error('Variations form not found');
                    return;
                }

                console.log('Variations form found');
                var variationsData = variationsForm.getAttribute('data-product_variations');
                if (!variationsData) {
                    console.error('Product variations data not found');
                    return;
                }

                variationsData = JSON.parse(variationsData);
                console.log('Parsed variations data:', variationsData);

                // Locate the select dropdown
                document.querySelectorAll('table.variations select').forEach(function (select) {
                    console.log('Processing select element:', select);

                    // Create radio buttons for each option
                    select.querySelectorAll('option').forEach(function (option) {
                        if (!option.value)
                            return;

                        var span = document.createElement('span');
                        span.className = 'variation-radio-group';

                        // Create the image element
                        var image = document.createElement('img');
                        image.style.maxWidth = '16px';
                        image.style.maxHeight = '16px';
                        image.style.marginRight = '10px';
                        image.style.verticalAlign = 'middle';

                        // Find the variation data for the current option
                        var variation = variationsData.find(v => v.attributes[select.name] === option.value);
                        if (variation && variation.image) {
                            image.src = variation.image.src;
                        }

                        var radio = document.createElement('input');
                        radio.type = 'radio';
                        radio.name = select.name;
                        radio.value = option.value;
                        radio.id = option.value;

                        var label = document.createElement('div');
                        label.className = 'srv-opt';
                        label.htmlFor = option.value;

                        // Split the text into words and remove the first word
//                        var text = option.text.trim(); // Get the option text
//                        var words = text.split(' '); // Split into words
//                        if (words.length > 1) {
//                            words.shift(); // Remove the first word
//                        }
                        label.textContent = option.text; // Set the modified label text

                        // Create price element
                        var price = document.createElement('div');
                        price.className = 'variation-price';
                        if (variation && variation.price_html) {
                            price.innerHTML = variation.price_html;
                        }

                        // Create custom label element
                        var customLabel = document.createElement('div');
                        customLabel.className = variation.custom_variation_label;
                        if (variation.custom_variation_label) {
                            customLabel.textContent = variation.custom_variation_label;
                        }

                        var imgWrapper = document.createElement('div');
                        imgWrapper.className = 'img-title';
                        imgWrapper.htmlFor = option.value; // Associate the label with the radio button
                        imgWrapper.appendChild(image);
                        imgWrapper.appendChild(customLabel);


                        var labelWrapper = document.createElement('label');
                        labelWrapper.className = 'variation-label-wrapper';
                        labelWrapper.htmlFor = option.value; // Associate the label with the radio button

                        // Append the elements to the new label wrapper
                        labelWrapper.appendChild(imgWrapper);
                        labelWrapper.appendChild(label);
                        labelWrapper.appendChild(price);



                        span.appendChild(radio); // Append radio first
                        span.appendChild(labelWrapper);

                        select.closest('td').appendChild(span);

                        // Add event listener for radio button selection
                        radio.addEventListener('click', function () {
                            // Update the select dropdown value
                            select.value = radio.value;
                            jQuery(select).trigger('change');
                        });
                    });

                    // Hide the original select dropdown
                    select.style.display = 'none';
                });
            });
        </script>
        <?php

    });
});

/* raf*

 * /
 */

// Add custom label field to each variation
add_action('woocommerce_product_after_variable_attributes', 'add_custom_label_to_variations', 10, 3);

function add_custom_label_to_variations($loop, $variation_data, $variation) {
    woocommerce_wp_text_input(
            array(
                'id' => 'custom_variation_label[' . $variation->ID . ']',
                'label' => __('Service option', 'woocommerce'),
                'desc_tip' => 'true',
                'description' => __('Enter a custom label for this variation.', 'woocommerce'),
                'value' => get_post_meta($variation->ID, 'custom_variation_label', true)
            )
    );
}

// Save custom label field value
add_action('woocommerce_save_product_variation', 'save_custom_label_variation', 10, 2);

function save_custom_label_variation($variation_id, $i) {
    $custom_label = $_POST['custom_variation_label'][$variation_id];
    if (isset($custom_label)) {
        update_post_meta($variation_id, 'custom_variation_label', esc_attr($custom_label));
    }
}

// Make custom label available in variation data
add_filter('woocommerce_available_variation', 'add_custom_label_to_variation_data');

function add_custom_label_to_variation_data($variation_data) {
    $variation_data['custom_variation_label'] = get_post_meta($variation_data['variation_id'], 'custom_variation_label', true);
    return $variation_data;
}

// Add custom variation label to order item meta
add_filter('woocommerce_add_cart_item_data', 'add_custom_label_to_cart_item', 10, 2);

function add_custom_label_to_cart_item($cart_item_data, $product_id) {
    if (isset($_POST['variation_id'])) {
        $variation_id = $_POST['variation_id'];
        $custom_label = get_post_meta($variation_id, 'custom_variation_label', true);
        if ($custom_label) {
            $cart_item_data['custom_variation_label'] = $custom_label;
        }
    }
    return $cart_item_data;
}

// Display custom variation label in the cart and checkout
add_filter('woocommerce_get_item_data', 'display_custom_label_in_cart', 10, 2);

function display_custom_label_in_cart($item_data, $cart_item) {
    if (isset($cart_item['custom_variation_label'])) {
        $item_data[] = array(
            'name' => __('Service option', 'woocommerce'),
            'value' => $cart_item['custom_variation_label']
        );
    }
    return $item_data;
}

// Save custom variation label to order items
add_action('woocommerce_add_order_item_meta', 'save_custom_label_to_order_items', 10, 3);

function save_custom_label_to_order_items($item_id, $values, $cart_item_key) {
    if (isset($values['custom_variation_label'])) {
        wc_add_order_item_meta($item_id, 'Service option Type', $values['custom_variation_label']);
    }
}




// Wrap quantity input and buttons in a common div

add_action( 'woocommerce_before_add_to_cart_quantity', 'before_quantity_display_label' );

function before_quantity_display_label() {
   if ( ! is_product() ) return;
   echo '<h3 class="sec_sub_heading">Number of documents that need to be apostilled</h3>';
}

add_action( 'woocommerce_before_quantity_input_field', 'bbloomer_display_quantity_minus' );
 
function bbloomer_display_quantity_minus() {
   if ( ! is_product() ) return;
   echo '<button type="button" class="minus" >-</button>';
}
 
add_action( 'woocommerce_after_quantity_input_field', 'bbloomer_display_quantity_plus' );
 
function bbloomer_display_quantity_plus() {
   if ( ! is_product() ) return;
   echo '<button type="button" class="plus" >+</button>';
}
 
add_action( 'woocommerce_before_single_product', 'bbloomer_add_cart_quantity_plus_minus' );
 
function bbloomer_add_cart_quantity_plus_minus() {
   wc_enqueue_js( "
      $('form.cart').on( 'click', 'button.plus, button.minus', function() {
            var qty = $( this ).closest( 'form.cart' ).find( '.qty' );
            var val   = parseFloat(qty.val());
            var max = parseFloat(qty.attr( 'max' ));
            var min = parseFloat(qty.attr( 'min' ));
            var step = parseFloat(qty.attr( 'step' ));
            if ( $( this ).is( '.plus' ) ) {
               if ( max && ( max <= val ) ) {
                  qty.val( max );
               } else {
                  qty.val( val + step );
               }
            } else {
               if ( min && ( min >= val ) ) {
                  qty.val( min );
               } else if ( val > 1 ) {
                  qty.val( val - step );
               }
            }
         });
   " );
}



add_action('woocommerce_before_single_product', function() {
    remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_meta', 40);
});
