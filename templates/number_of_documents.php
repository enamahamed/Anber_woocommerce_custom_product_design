<?php
add_action('woocommerce_checkout_before_customer_details', 'add_custom_number_field');

function add_custom_number_field($checkout) {
    echo '<div id="custom_number_field"><h3>' . __('Number of Documents') . '</h3>';
    
    woocommerce_form_field('number_of_documents', array(
        'type'        => 'number',
        'class'       => array('form-row-wide'),
        'label'       => __('Number of documents that need to be apostilled'),
        'required'    => true,
        'input_class' => array('custom-number-input'), // Add a custom class for styling
        'default'     => 1,
        'custom_attributes' => array(
            'min' => 1, // Minimum value
            'step' => 10 // Increment/Decrement step
        ),
    ), $checkout->get_value('number_of_documents'));

    echo '</div>';
}

add_action('woocommerce_checkout_update_order_meta', 'save_custom_number_field');

function save_custom_number_field($order_id) {
    if (!empty($_POST['number_of_documents'])) {
        update_post_meta($order_id, 'number_of_documents', sanitize_text_field($_POST['number_of_documents']));
    }
}

add_action('woocommerce_admin_order_data_after_order_details', 'display_custom_number_field_in_admin');

function display_custom_number_field_in_admin($order) {
    $number_of_documents = get_post_meta($order->get_id(), 'number_of_documents', true);
    if (!empty($number_of_documents)) {
        echo '<p><strong>' . __('Number of Documents') . ':</strong> ' . esc_html($number_of_documents) . '</p>';
    }
}

add_filter('woocommerce_email_order_meta_fields', 'add_custom_number_field_to_email', 10, 3);

function add_custom_number_field_to_email($fields, $sent_to_admin, $order) {
    $number_of_documents = $order->get_meta('number_of_documents');
    if (!empty($number_of_documents)) {
        $fields['number_of_documents'] = array(
            'label' => __('Number of Documents'),
            'value' => $number_of_documents,
        );
    }
    return $fields;
}


// Add custom radio field value to the order invoice
add_action('woocommerce_order_item_meta_end', 'display_number_of_documents_option_in_order_invoice', 10, 4);

function display_number_of_documents_option_in_order_invoice($item_id, $item, $order, $plain_text) {
    $number_of_documents = $order->get_meta('number_of_documents');
    if (!empty($number_of_documents)) {
        echo '<p><strong>' . __('Number of Documents', 'your-text-domain') . ':</strong> ' . esc_html($number_of_documents) . '</p>';
    }
}

