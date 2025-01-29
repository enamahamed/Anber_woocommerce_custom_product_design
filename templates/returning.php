<?php

function get_returning_documents_option() {
    $options = array();
    // Retrieve fields from Carbon Fields 
    $fields = carbon_get_theme_option('awc_returning_op');
    if (!empty($fields)) {
        foreach ($fields as $field) {
            $options[] = array(
                'awc_returning_title' => $field['awc_returning_title'],
                'awc_returning_price' => $field['awc_returning_price']
            );
        }
    }
    return $options;
}

add_action('woocommerce_checkout_before_customer_details', 'render_returning_documents_options');

function render_returning_documents_options() {
    $returning_documents = get_returning_documents_options();
    if (!empty($returning_documents)) {
        ?> 
        <div id="custom-radio-field"> 
            <h3 class="tab-sub-title">Returning Documents</h3>
            <p>Select Return Option:</p>
            <div class="item-wrap"> 
                <?php
                foreach ($returning_documents as $index => $option) {
                    $returning_documents_title = $option['awc_returning_title'];
                    ?> 
                    <div class="button">                          
                        <label class="btn btn-default" for="returning_documents_options_<?php echo $index; ?>"> 
                            <input type="radio" id="returning_documents_options_<?php echo $index; ?>" value="<?php echo esc_attr($returning_documents_title); ?>" name="returning_documents_options" <?php checked($index === 0); ?> />                           
                            <span class="adition_title"><?php echo esc_html($returning_documents_title); ?></span>                            
                        </label> 
                    </div> 
        <?php } ?> 
            </div>
        </div> 
        <?php
    }
}

// Save the selected option in the session
add_action('woocommerce_checkout_update_order_meta', 'save_returning_documents_option');

function save_returning_documents_option($order_id) {
    if (isset($_POST['returning_documents_options'])) {
        $selected_option = sanitize_text_field($_POST['returning_documents_options']);
        update_post_meta($order_id, 'returning_documents_options', $selected_option);
    }
}

// Add custom radio field value to order admin page
add_action('woocommerce_admin_order_data_after_order_details', 'display_returning_documents_in_admin_order');

function display_returning_documents_in_admin_order($order) {
    $returning_documents_option = get_post_meta($order->get_id(), 'returning_documents_options', true);
    if (!empty($returning_documents_option)) {
        echo '<p><strong>' . __('Returning Documents', 'your-text-domain') . ':</strong> ' . esc_html($returning_documents_option) . '</p>';
    }
}

add_action('woocommerce_admin_order_data_after_order_details', 'display_returning_documents_in_admin_order');

/*
  return_shipping_address
 *  */


// Save the return_shipping_address
add_action('woocommerce_checkout_create_order', 'save_custom_return_address_fields', 10, 2);

function save_custom_return_address_fields($order, $data) {
    if (!empty($_POST['return_address_line'])) {
        $order->update_meta_data('_return_address_line', sanitize_text_field($_POST['return_address_line']));
    }
    if (!empty($_POST['return_address_city'])) {
        $order->update_meta_data('_return_address_city', sanitize_text_field($_POST['return_address_city']));
    }
    if (!empty($_POST['return_address_post'])) {
        $order->update_meta_data('_return_address_post', sanitize_text_field($_POST['return_address_post']));
    }
    if (!empty($_POST['return_address_country'])) {
        $order->update_meta_data('_return_address_country', sanitize_text_field($_POST['return_address_country']));
    }
}

add_action('woocommerce_admin_order_data_after_billing_address', 'display_return_address_fields_in_admin', 10, 1);

function display_return_address_fields_in_admin($order) {
    if ($order->get_meta('_return_address_line')) {
        echo '<h3>' . __('Return shipping Address Details', 'woocommerce') . '</h3>';
        echo '<p><strong>' . __('Address Line') . ':</strong> ' . esc_html($order->get_meta('_return_address_line')) . '</p>';
    }
    if ($order->get_meta('_return_address_city')) {
        echo '<p><strong>' . __('City') . ':</strong> ' . esc_html($order->get_meta('_return_address_city')) . '</p>';
    }
    if ($order->get_meta('_return_address_post')) {
        echo '<p><strong>' . __('Postcode') . ':</strong> ' . esc_html($order->get_meta('_return_address_post')) . '</p>';
    }
    if ($order->get_meta('_return_address_country')) {
        echo '<p><strong>' . __('Country') . ':</strong> ' . esc_html($order->get_meta('_return_address_country')) . '</p>';
    }
}

add_action('woocommerce_email_after_order_table', 'display_return_address_in_email', 10, 4);

function display_return_address_in_email($order, $sent_to_admin, $plain_text, $email) {
    echo '<h3>' . __('Return shipping Address Details', 'woocommerce') . '</h3>';
    echo '<p><strong>' . __('Address Line') . ':</strong> ' . esc_html($order->get_meta('_return_address_line')) . '</p>';
    echo '<p><strong>' . __('City') . ':</strong> ' . esc_html($order->get_meta('_return_address_city')) . '</p>';
    echo '<p><strong>' . __('Postcode') . ':</strong> ' . esc_html($order->get_meta('_return_address_post')) . '</p>';
    echo '<p><strong>' . __('Country') . ':</strong> ' . esc_html($order->get_meta('_return_address_country')) . '</p>';
}

add_action('woocommerce_order_details_after_customer_details', 'display_return_address_on_my_account', 10, 1);

function display_return_address_on_my_account($order) {
    echo '<h3>' . __('Return shipping Address Details', 'woocommerce') . '</h3>';
    echo '<p><strong>' . __('Address Line') . ':</strong> ' . esc_html($order->get_meta('_return_address_line')) . '</p>';
    echo '<p><strong>' . __('City') . ':</strong> ' . esc_html($order->get_meta('_return_address_city')) . '</p>';
    echo '<p><strong>' . __('Postcode') . ':</strong> ' . esc_html($order->get_meta('_return_address_post')) . '</p>';
    echo '<p><strong>' . __('Country') . ':</strong> ' . esc_html($order->get_meta('_return_address_country')) . '</p>';
}

/* Change   total Price */


add_action('woocommerce_review_order_before_submit', 'add_returning_documents_field');
function add_returning_documents_field() {
    ?>
    <input type="hidden" name="returning_documents_option" id="returning_documents_option" value="">
    <script>
        jQuery(document).ready(function ($) {
            $('input[name="returning_documents_options"]').change(function () {
                $('#returning_documents_option').val(
                    $('input[name="returning_documents_options"]:checked').data('title') + '|' +
                    $('input[name="returning_documents_options"]:checked').data('price')
                );
            });
        });
    </script>
    <?php
}
add_action('woocommerce_checkout_create_order', 'save_returning_documents_to_order', 10, 2);
function save_returning_documents_to_order($order, $data) {
    if (!empty($_POST['returning_documents_option'])) {
        list($title, $price) = explode('|', wc_clean($_POST['returning_documents_option']));
        $order->update_meta_data('_returning_documents_title', $title);
        $order->update_meta_data('_returning_documents_price', $price);
    }
}

add_action('woocommerce_cart_calculate_fees', 'add_returning_documents_fee');
function add_returning_documents_fee() {
    if (!empty($_POST['returning_documents_option'])) {
        list(, $price) = explode('|', wc_clean($_POST['returning_documents_option']));
        WC()->cart->add_fee(__('Returning Documents Fee', 'your-text-domain'), floatval($price));
    }
}

add_action('woocommerce_admin_order_data_after_order_details', 'display_returning_documents_in_admin');
function display_returning_documents_in_admin($order) {
    $title = $order->get_meta('_returning_documents_title');
    $price = $order->get_meta('_returning_documents_price');
    if ($title && $price) {
        echo '<p><strong>' . __('Returning Option and Fee:', 'your-text-domain') . '</strong> ' . esc_html($title) . ' (' . wc_price($price) . ')</p>';
    }
}

add_action('woocommerce_email_after_order_table', 'display_returning_documents_in_email', 10, 4);
function display_returning_documents_in_email($order, $sent_to_admin, $plain_text, $email) {
    $title = $order->get_meta('_returning_documents_title');
    $price = $order->get_meta('_returning_documents_price');
    if ($title && $price) {
        echo '<p><strong>' . __('Returning Option and Fee:', 'your-text-domain') . '</strong> ' . esc_html($title) . ' (' . wc_price($price) . ')</p>';
    }
}
