<?php
/**
 * Template Hooks
 *
 * Action/filter hooks used for Variation Carousel for WooCommerce functions/templates.
 *
 * @author   Younes DRO
 * @category Core
 * @package  Variation Carousel for WooCommerce/Templates
 * @since    1.0.0
 * @version  1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Carousel Indicators.
add_filter( 'woocommerce_cvp_carousel_indicators', 'wc_cvp_template_carousel_indicators', 10, 1 );

// Reset Button.
add_action( 'woocommerce_cvp_add_to_cart_wrap', 'wc_cvp_template_reset_button', 20 );

// Single product add-to-cart buttons area template.
add_action( 'woocommerce_cvp_add_to_cart_wrap', 'wc_cvp_template_add_to_cart_wrap', 10 );

// Variation data.
add_action( 'woocommerce_cvp_variation_data', 'wc_cvp_template_variation_data', 10, 1 );
