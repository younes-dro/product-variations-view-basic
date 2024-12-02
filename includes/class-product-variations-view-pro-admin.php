<?php
/**
 * Admin Class for Product Variations View Pro Plugin.
 *
 * Handles all admin-specific functionality.
 *
 * @version  1.0.0
 * @since    1.0.0
 * @package  Product_Variations_View_Pro
 * @author   Younes DRO
 * @email    younesdro@gmail.com
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Class Product_Variations_View_Pro_Admin
 *
 * This class encapsulates all the admin-specific functionality for the plugin.
 * It manages tasks like handling missing attribute warnings, adding admin notices,
 * and customizing WooCommerce backend behavior.
 *
 * @since 1.0.0
 */
class Product_Variations_View_Pro_Admin {

	/**
	 * Constructor.
	 * Initializes the class and hooks admin-specific actions.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		add_action( 'woocommerce_variation_header', array( $this, 'display_missing_attributes_warning' ), 10, 2 );
	}

	/**
	 * Displays a warning in the Variations tab for variations with missing attributes.
	 *
	 * @param object $variation The WooCommerce variation object.
	 * @param int    $loop      The loop index for the variation.
	 *
	 * @since 1.0.0
	 */
	public function display_missing_attributes_warning( $variation, $loop ) {
		global $post;

		$product      = wc_get_product( $post->ID );
		$variation_id = $variation->ID;

		$variation  = wc_get_product( $variation_id );
		$attributes = $variation->get_attributes();
		$warnings   = array();

		// Check for missing attributes (e.g., set to "Any" or empty).
		foreach ( $attributes as $key => $value ) {
			if ( strtolower( $value ) === 'any' || empty( $value ) ) {
				$warnings[] = sprintf(
					__( 'Variation #%1$d has an undefined attribute: %2$s', 'product-variations-view' ),
					$variation_id,
					wc_attribute_label( $key, $product )
				);
			}
		}
		if ( ! empty( $warnings ) ) {
			echo '<div class="notice notice-warning" style="margin: 10px 0; padding: 10px; background: #ffebe8; border-left: 4px solid #dc3232;">';
			foreach ( $warnings as $warning ) {
				echo '<p style="margin: 0;">' . esc_html( $warning ) . '</p>';
			}
			echo '</div>';
		}
	}
}
