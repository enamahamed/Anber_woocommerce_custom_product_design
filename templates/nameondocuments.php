<?php

// Save the return_shipping_address
add_action('woocommerce_checkout_create_order', 'save_name_on_documents', 10, 2);

function save_name_on_documents($order, $data) {
    if (!empty($_POST['name_of_documents'])) {
        $order->update_meta_data('_name_of_documents', sanitize_text_field($_POST['name_of_documents']));
    }
}

add_action('woocommerce_admin_order_data_after_billing_address', 'display_name_on_documents_fields_in_admin', 10, 1);

function display_name_on_documents_fields_in_admin($order) {
    if ($order->get_meta('_name_of_documents')) {
        echo '<h3>' . __('Name of documents', 'woocommerce') . '</h3>';
        echo '<p>' . esc_html($order->get_meta('_name_of_documents')) . '</p>';
    }
}
