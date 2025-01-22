<?php
/**
 * Template Hooks
 *
 * Action/filter hooks used for Product Variations View Pro functions/templates.
 *
 * @author   Younes DRO
 * @category Core
 * @package  Product Variations View Pro
 * @since    1.0.0
 * @version  1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Carousel Indicators.
add_filter( 'dro_pvvp_carousel_indicators', 'dro_pvvp_template_carousel_indicators', 10, 1 );

// Reset Button.
add_action( 'dro_pvvp_add_to_cart_wrap', 'dro_pvvp_template_reset_button', 20 );

// Single product add-to-cart buttons area template.
add_action( 'dro_pvvp_add_to_cart_wrap', 'dro_pvvp_template_add_to_cart_wrap', 10 );

// Variation data.
add_action( 'dro_pvvp_variation_data', 'dro_pvvp_template_variation_data', 10, 1 );
