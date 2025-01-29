<?php

/**
 * Plugin Name: Anber WooCommerce Custom Product Design
 * Description: A custom product design Plugin
 * Version: 1.0
 * Author: Md Yeasir Arafat
 */
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

// Ensure Carbon Fields is loaded only if not already loaded
if (!class_exists('Carbon_Fields\Carbon_Fields')) {
    if (file_exists(plugin_dir_path(__FILE__) . 'vendor/autoload.php')) {
        require_once plugin_dir_path(__FILE__) . 'vendor/autoload.php';
    } else {
        wp_die('Carbon Fields dependency not found. Please install it using Composer.');
    }
}

use Carbon_Fields\Container;
use Carbon_Fields\Field;

// Initialize Carbon Fields only if not already booted
add_action('plugins_loaded', 'awcpd_init_carbon_fields');

function awcpd_init_carbon_fields() {
    if (class_exists('Carbon_Fields\Carbon_Fields')) {
        \Carbon_Fields\Carbon_Fields::boot();
    }
}

add_action('carbon_fields_register_fields', 'awcpd_add_carbon_fields');

function awcpd_add_carbon_fields() {
    Container::make('post_meta', 'Custom Product Details')
            ->where('post_type', '=', 'product')
            ->add_tab(__('Document Types'), array(
                Field::make('text', 'awcpd_dpo_sectitle', 'Section Title')->set_width(30),
                Field::make('complex', 'awcpd_dpo', __('Document Type Items'))->set_width(70)
                ->set_layout('tabbed-vertical')
                ->add_fields(array(
                    Field::make('image', 'awcpd_dpo_icon', 'Icon')->set_width(20),
                    Field::make('text', 'awcpd_dpo_title', 'Title')->set_width(80),
                    Field::make('textarea', 'awcpd_dpo_note', 'Note'),
                )),
                    // Field::make('text', 'awcpd_docnumber_text', 'Title for Number of documents'),
            ))
    ;
     
}

/*
  JS & CSS
 *  */
add_action('wp_enqueue_scripts', 'awcpd_enqueue_custom_scripts');

function awcpd_enqueue_custom_scripts() {
    if (is_singular('product')) {
        wp_enqueue_style(
                'awcpd-custom-style', plugin_dir_url(__FILE__) . 'assets/css/awcpd-custom-product.css', array(), '1.0'
        );

        wp_enqueue_script(
                'awcpd-custom-script',
                plugin_dir_url(__FILE__) . 'assets/js/awcpd-custom-product.js',
                array('jquery'),
                '1.0',
                true
        );
    }  
}

/*
  Google FOnts
 *  */
add_action('wp_enqueue_scripts', 'awcpd_add_google_fonts');

function awcpd_add_google_fonts() {
    // Add preconnect for Google Fonts
    wp_enqueue_style('awcpd-google-fonts-preconnect', false, [], null);
    add_action('wp_head', function () {
        echo '<link rel="preconnect" href="https://fonts.googleapis.com">' . "\n";
        echo '<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>' . "\n";
    }, 1);

    // Enqueue the Google Fonts stylesheet
    wp_enqueue_style(
            'awcpd-google-fonts',
            'https://fonts.googleapis.com/css2?family=Manrope:wght@200..800&display=swap',
            [],
            null
    );
}

/* * *
  Hook into WooCommerce Template Loading
 *  */
add_filter('template_include', 'awcpd_custom_single_product_template', 99);

function awcpd_custom_single_product_template($template) {
    if (is_singular('product')) {
        // Check if a custom template exists in your plugin directory
        $plugin_template = plugin_dir_path(__FILE__) . 'templates/single-product.php';
        if (file_exists($plugin_template)) {
            return $plugin_template;
        }
    }
    return $template;
}


/* Includes */
require_once plugin_dir_path(__FILE__) . 'templates/awcpd_custom_hook.php';
require_once plugin_dir_path(__FILE__) . 'templates/doc-processing.php';



add_filter('woocommerce_single_product_image_thumbnail_html', 'disable_variation_image_change', 10, 2);
function disable_variation_image_change($html, $attachment_id) {
    // Return the HTML only for the featured image
    global $product;
    $featured_image_id = $product->get_image_id();
    if ($attachment_id !== $featured_image_id) {
        return ''; // Do not render other images
    }
    return $html;
}

add_filter('woocommerce_available_variation', 'remove_variation_images');
function remove_variation_images($variation) {
    unset($variation['image']); // Remove variation-specific images
    return $variation;
}


add_action('woocommerce_init', 'remove_single_product_excerpt');
function remove_single_product_excerpt() {
    remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_excerpt', 20);
}




