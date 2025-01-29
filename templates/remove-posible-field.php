<?php

/**
  Remove all possible fields
 * */
add_filter('woocommerce_checkout_fields', 'wedevs_remove_checkout_fields');

function wedevs_remove_checkout_fields($fields) {
    // Billing fields
    unset($fields['billing']['billing_company']);
    //unset( $fields['billing']['billing_email'] );
    //unset( $fields['billing']['billing_phone'] );
    unset($fields['billing']['billing_state']);
    //unset( $fields['billing']['billing_first_name'] );
    //unset( $fields['billing']['billing_last_name'] );
    unset($fields['billing']['billing_address_1']);
    unset($fields['billing']['billing_address_2']);
    unset($fields['billing']['billing_city']);
    unset($fields['billing']['billing_postcode']);
    unset($fields['billing']['billing_country']);

    // Shipping fields
    unset($fields['shipping']['shipping_company']);
    unset($fields['shipping']['shipping_phone']);
    unset($fields['shipping']['shipping_state']);
    unset($fields['shipping']['shipping_first_name']);
    unset($fields['shipping']['shipping_last_name']);
    //unset($fields['shipping']['shipping_address_1']);
    unset($fields['shipping']['shipping_address_2']);
    //unset($fields['shipping']['shipping_city']);
    //unset($fields['shipping']['shipping_postcode']);
    // Order fields
     unset( $fields['order']['order_comments'] );
    return $fields;
}
add_filter('woocommerce_default_address_fields', 'disable_shipping_state_validation');
function disable_shipping_state_validation($fields) {
    if (isset($fields['state'])) {
        unset($fields['state']); // Disable validation for the state field
    }
    return $fields;
}
//add_filter('woocommerce_ship_to_different_address_checked', '__return_true');
add_filter("woocommerce_checkout_fields", "woocommerce_reorder_checkout_fields", 9999);
if (!function_exists('woocommerce_reorder_checkout_fields')) {

    function woocommerce_reorder_checkout_fields($fields) {
        // Reorder the fields
        $order = array(
            "billing_first_name",
            "billing_last_name",
            "billing_email",
            "billing_phone",
        );

        $ordered_fields = array();
        foreach ($order as $field) {
            $ordered_fields[$field] = $fields["billing"][$field];
        }

        // Set the reordered fields back to $fields['billing']
        $fields['billing'] = $ordered_fields;

        // Adjust the class for email and phone fields to display them side by side
        $fields['billing']['billing_email']['class'][0] = 'form-row-first';
        $fields['billing']['billing_phone']['class'][0] = 'form-row-last';

        return $fields;
    }
}


/**
  Optional  fields
 * */
add_filter('woocommerce_default_address_fields', 'adjust_requirement_of_checkout_address_fields');

function adjust_requirement_of_checkout_address_fields($fields) {
    $fields['company']['required'] = false;
    $fields['country']['required'] = false;
    $fields['address_1']['required'] = false;
    $fields['address_2']['required'] = false;
    $fields['city']['required'] = false;
    $fields['state']['required'] = false;
    $fields['postcode']['required'] = false;
    
//    $fields['first_name']['required'] = false;
//    $fields['last_name']['required'] = false;
//    $fields['email']['required'] = false;
//    $fields['phone']['required'] = false;

    return $fields;
}

//Change the billing details heading and the billing information tab label

function wc_billing_field_strings($translated_text, $text, $domain) {
    switch ($translated_text) {
        case 'Billing address' :
            $translated_text = __('Customer Information', 'woocommerce');
            break;
        case 'Billing details' :
            $translated_text = __('Customer Information', 'woocommerce');
            break;
    }
    return $translated_text;
}

add_filter('gettext', 'wc_billing_field_strings', 20, 3);

// Loop through billing fields and replace labels with placeholders
add_filter('woocommerce_checkout_fields', 'customize_woocommerce_checkout_fields');

function customize_woocommerce_checkout_fields($fields) {

    foreach ($fields['billing'] as $field_key => $field) {
        if (isset($fields['billing'][$field_key]['label'])) {
            $fields['billing'][$field_key]['placeholder'] = $fields['billing'][$field_key]['label']; // Set placeholder as the label
            unset($fields['billing'][$field_key]['label']); // Remove the label
        }
    }

    return $fields;
}





