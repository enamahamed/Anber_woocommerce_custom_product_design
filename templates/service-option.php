<?php
function get_service_option() {
    $options = array();
    // Retrieve fields from Carbon Fields 
    $fields = carbon_get_theme_option('awc-so');
    if (!empty($fields)) {
        foreach ($fields as $field) {
            $options[] = array(
                'awc_so_icon' => $field['awc_so_icon'],
                'awc_so_type' => $field['awc_so_type'],
                'awc_so_title' => $field['awc_so_title'],
                'awc_so_price' => $field['awc_so_price']
            );
        }
    }
    return $options;
}

add_action('woocommerce_checkout_before_customer_details', 'render_service_options');

function render_service_options() {
    $service_options = get_service_option();
    if (!empty($service_options)) {
        ?> 
        <div id="custom-radio-field"> 
            <h3 class="tab-sub-title">Service option</h3>
            <div class="item-wrap"> 
                <?php foreach ($service_options as $index => $option) {
                    $awc_so_type = $option['awc_so_type'];
                    $awc_so_icon_url = wp_get_attachment_url($option['awc_so_icon']);
                    $awc_so_title = $option['awc_so_title'];
                    $awc_so_price = $option['awc_so_price'];
                    ?> 
                    <div class="button"> 
                        <input type="radio" id="custom_service_options_<?php echo $index; ?>" value="<?php echo esc_attr($awc_so_type); ?>" name="custom_service_options" <?php checked($index === 0); ?> /> 
                        <label class="btn btn-default" for="custom_service_options_<?php echo $index; ?>"> 
                            <div class="icon_type">
                                <img src="<?php echo esc_url($awc_so_icon_url); ?>" alt="<?php echo esc_attr($awc_so_title); ?>" style="width: 20px; height: 20px; vertical-align: middle;" /> 
                                <span><?php echo esc_html($awc_so_type); ?></span>
                            </div>                            
                            <span class="awc_so_title"><?php echo esc_html($awc_so_title); ?></span> 
                            <span class="awc_so_price"><span><?php echo get_woocommerce_currency_symbol(); ?></span><?php echo esc_html($awc_so_price); ?></span> 
                        </label> 
                    </div> 
                <?php } ?> 
            </div>
        </div> 
        <?php
    }
}

// Save the selected option in the session
add_action('woocommerce_checkout_update_order_review', 'save_custom_service_option');

function save_custom_service_option($posted_data) {
    parse_str($posted_data, $output);
    if (isset($output['custom_service_options'])) {  // Fix the name to match the form field
        WC()->session->set('custom_service_options', sanitize_text_field($output['custom_service_options']));
    }
}



// Save the custom radio field value to order meta
add_action('woocommerce_checkout_create_order', 'save_custom_service_option_to_order', 10, 2);

function save_custom_service_option_to_order($order, $data) {
    if (!empty($_POST['custom_service_options'])) {  // Fix the name to match the form field
        $order->update_meta_data('custom_service_options', sanitize_text_field($_POST['custom_service_options']));
    }
}

// Add custom radio field value to order admin page
add_action('woocommerce_admin_order_data_after_order_details', 'display_custom_service_option_in_admin_order');

function display_custom_service_option_in_admin_order($order) {
    $custom_service_option = $order->get_meta('custom_service_options');  // Fix the meta key to match the saved data
    if (!empty($custom_service_option)) {
        echo '<p><strong>' . __('Service Option', 'your-text-domain') . ':</strong> ' . esc_html($custom_service_option) . '</p>';
    }
}

// Add custom radio field value to the order email
add_filter('woocommerce_email_order_meta_fields', 'add_custom_service_option_to_email_order_meta', 10, 3);

function add_custom_service_option_to_email_order_meta($fields, $sent_to_admin, $order) {
    $custom_service_option = $order->get_meta('custom_service_options');  // Fix the meta key to match the saved data
    if (!empty($custom_service_option)) {
        $fields['custom_service_options'] = array(
            'label' => __('Service Option', 'your-text-domain'),
            'value' => esc_html($custom_service_option),  // Use esc_html for plain text
        );
    }
    return $fields;
}

// Add custom radio field value to the order invoice
add_action('woocommerce_order_item_meta_end', 'display_custom_service_option_in_order_invoice', 10, 4);

function display_custom_service_option_in_order_invoice($item_id, $item, $order, $plain_text) {
    $custom_service_option = $order->get_meta('custom_service_options');  // Fix the meta key to match the saved data
    if (!empty($custom_service_option)) {
        echo '<p><strong>' . __('Service Option', 'your-text-domain') . ':</strong> ' . esc_html($custom_service_option) . '</p>';
    }
}
