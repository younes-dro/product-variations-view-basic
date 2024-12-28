<?php
/**
 * Plugin Name: Product Variations View Pro
 * Plugin URI: https://github.com/younes-dro/product-variations-view-pro
 * Description: Product Variation View Pro enhances WooCommerce variable products by displaying variations in an intuitive, carousel-style interface. It allows customers to add multiple product variations to the cart in a single action, streamlining the shopping experience.
 * Version: 1.0.0
 * Author: Younes DRO
 * Author URI: https://github.com/younes-dro
 * Text Domain: product-variations-view-pro
 * Domain Path: /languages
 *
 * WC requires at least: 3.7.0
 * WC tested up to: 9.5.1
 *
 * Copyright: © 2024 Younes DRO
 * License: GNU General Public License v2.0 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 */

namespace DRO\ProductVariationsViewPro;

use DRO\ProductVariationsViewPro\Includes\Product_Variations_View_Pro;
use DRO\ProductVariationsViewPro\Includes\Product_Variations_View_Pro_Dependencies;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

define( 'PRODUCT_VARIATIONS_VIEW_PRO_FILE', __FILE__ );
define( 'PRODUCT_VARIATIONS_VIEW_PRO_NAME', 'Product Variations View Pro' );
define( 'INCLUDES_FOLDER', untrailingslashit( plugin_dir_path( __FILE__ ) ) . '/includes/' );

/**
 * Checks the server environment and deactivates plugins as necessary.
 *
 * @since 1.0.0
 */
function activation_check() {
	$dependencies = new Product_Variations_View_Pro_Dependencies();
	if ( ! $dependencies->check_php_version() ) {

		deactivate_plugins( plugin_basename( PRODUCT_VARIATIONS_VIEW_PRO_FILE ) );
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Nonce not needed as "activate" is used for display purposes only.
		if ( isset( $_GET['activate'] ) ) {
			unset( $_GET['activate'] );
		}

		wp_die(
			sprintf(
				/* translators: %s is the plugin name. */
				esc_html__( '%s could not be activated.', 'product-variations-view-pro' ),
				esc_html( PRODUCT_VARIATIONS_VIEW_PRO_NAME )
			) . esc_html( $dependencies->get_php_notice() )
		);

	}
}
register_activation_hook( PRODUCT_VARIATIONS_VIEW_PRO_FILE, __NAMESPACE__ .'\\activation_check' );
/**
 * Register the built-in autoloader
 */
function register_autoloader() {
	spl_autoload_register(
		function ( $class_name ) {
			$prefix   = 'DRO\\ProductVariationsViewPro\\includes\\';
			$base_dir = __DIR__ . '/includes/';
			$len      = strlen( $prefix );
			// Make sure the class name stats with 'DRO' to load only our classes.
			if ( strncmp( __NAMESPACE__, $class_name, 3 ) !== 0 ) {
				return;
			}
			$relative_class_name = substr( $class_name, $len );
			$class               = strtolower( str_replace( '_', '-', $relative_class_name ) );
			$file_class          = $base_dir . 'class-' . $class . '.php';

			if ( file_exists( $file_class ) ) {
				require_once $file_class;
			}
		}
	);
}

/**
 * Returns the main instance of Product_Variations_View_Pro.
 */
function product_variations_view_pro() {
	register_autoloader();
	return Product_Variations_View_Pro::start( new Product_Variations_View_Pro_Dependencies() );
}

product_variations_view_pro();

/**
 * Declare compatibility with WooCommerce Custom Order Tables.
 */
add_action(
	'before_woocommerce_init',
	function () {
		if ( class_exists( '\Automattic\WooCommerce\Utilities\FeaturesUtil' ) ) {
			\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', __FILE__, true );
		}
	}
);
