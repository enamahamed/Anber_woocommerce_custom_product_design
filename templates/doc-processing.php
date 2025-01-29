<?php
add_action('woocommerce_before_variations_form', 'display_docprocess_as_radio');

function display_docprocess_as_radio() {
    global $product;

    $document_types = carbon_get_post_meta($product->get_id(), 'awcpd_dpo');
    if ($document_types) {
        echo '<h3 class="sec_sub_heading">' . esc_html(carbon_get_post_meta($product->get_id(), 'awcpd_dpo_sectitle')) . '</h3>';
        echo '<div class="docprocess-group">';
        foreach ($document_types as $index => $doc) {
            $doc_title = $doc['awcpd_dpo_title'];
            $doc_icon = wp_get_attachment_url($doc['awcpd_dpo_icon']);
            $doc_note = $doc['awcpd_dpo_note'];
            ?>
            <div class="radio_style">
                <input 
                    type="radio" 
                    id="dpo_<?php echo $index; ?>" 
                    value="<?php echo esc_attr($doc_title); ?>" 
                    name="awcpd_dpo" 
                    data-note="<?php echo esc_attr($doc_note); ?>" 
                    required 
                />
                <label for="dpo_<?php echo $index; ?>">
                    <img src="<?php echo esc_url($doc_icon); ?>" alt="<?php echo esc_attr($doc_title); ?>" style="width: 20px; height: 20px; vertical-align: middle;" />
                    <p><?php echo esc_html($doc_title); ?></p>
                </label>
            </div>
            <?php
        }
        // Note container (hidden initially)
       
        echo '</div>';
         echo '<div id="doc_note" class="note" style="display: none;"></div>';
    }
}
add_action('wp_footer', 'add_docprocess_radio_script');
function add_docprocess_radio_script() {
    if (is_product()) { // Only include on single product pages
        ?>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                // Get all radio buttons
                const radios = document.querySelectorAll('input[name="awcpd_dpo"]');
                const noteContainer = document.getElementById('doc_note');

                radios.forEach(function (radio) {
                    radio.addEventListener('change', function () {
                        const note = this.dataset.note;

                        if (note) {
                            noteContainer.style.display = 'block';
                            noteContainer.innerHTML = note;
                        } else {
                            noteContainer.style.display = 'none';
                            noteContainer.innerHTML = '';
                        }
                    });
                });
            });
        </script>
        <?php
    }
}


add_filter('woocommerce_add_to_cart_validation', 'validate_custom_field', 10, 2);

function validate_custom_field($passed, $product_id) {
    if (empty($_POST['awcpd_dpo'])) {
        wc_add_notice(__('Please select a document type.', 'woocommerce'), 'error');
        $passed = false;
    }
    return $passed;
}

//Ensure Custom Data is Added to the Cart Item

add_action('woocommerce_add_cart_item_data', 'save_custom_field_in_cart_item', 10, 2);

function save_custom_field_in_cart_item($cart_item_data, $product_id) {
    if (isset($_POST['awcpd_dpo'])) {
        $cart_item_data['awcpd_dpo'] = sanitize_text_field($_POST['awcpd_dpo']);
    }
    return $cart_item_data;
}

//Display Custom Data in the Cart and Checkout
add_filter('woocommerce_get_item_data', 'display_custom_field_in_cart', 10, 2);

function display_custom_field_in_cart($item_data, $cart_item) {
    if (isset($cart_item['awcpd_dpo'])) {
        $item_data[] = array(
            'name' => __('Document Option', 'woocommerce'),
            'value' => $cart_item['awcpd_dpo'],
        );
    }
    return $item_data;
}

//Save Custom Data to Order Items
add_action('woocommerce_checkout_create_order_line_item', 'add_custom_field_to_order_items', 10, 4);

function add_custom_field_to_order_items($item, $cart_item_key, $values, $order) {
    if (isset($values['awcpd_dpo'])) {
        $item->add_meta_data('Option', $values['awcpd_dpo']);
    }
}

//Display Custom Data in Order Details

add_action('woocommerce_order_item_meta_end', 'display_custom_field_in_order_details', 10, 4);

function display_custom_field_in_order_details($item_id, $item, $order, $plain_text) {
    $custom_label = wc_get_order_item_meta($item_id, '_awcpd_dpo', true);
    if ($custom_label) {
        if ($plain_text) {
            echo "\n" . __('Document Type:', 'woocommerce') . ' ' . $custom_label . "\n";
        } else {
            echo '<p><strong>' . __('Document Type:', 'woocommerce') . ' </strong>' . $custom_label . '</p>';
        }
    }
}

//Display Custom Field Data in Order Emails:
add_filter('woocommerce_email_order_meta_fields', 'add_custom_field_to_order_emails', 10, 3);

function add_custom_field_to_order_emails($fields, $sent_to_admin, $order) {
    $items = $order->get_items();
    foreach ($items as $item) {
        if ($doc_type = $item->get_meta('_awcpd_dpo')) {
            $fields['custom_field'] = array(
                'label' => __('Document Option', 'woocommerce'),
                'value' => $doc_type,
            );
        }
    }
    return $fields;
}
