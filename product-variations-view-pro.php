<?php
/**
 * Plugin Name: Product Variations View Pro
 * Plugin URI: https://github.com/younes-dro/product-variations-view-pro
 * Description: Product Variation View Pro enhances WooCommerce variable products by displaying variations in an intuitive, carousel-style interface. It allows customers to add multiple product variations to the cart in a single action, streamlining the shopping experience.
 * Version: 1.1.0
 * Author: Younes DRO
 * Author URI: https://github.com/younes-dro
 * Text Domain: product-variations-view-pro
 * Domain Path: /languages
 * Requires PHP: 7.4
 * Requires at least: 5.7
 * Tested up to: 6.7
 * WC requires at least: 5.8
 * WC tested up to: 9.4
 * Requires Plugins: woocommerce
 *
 * Copyright: © 2024 Younes DRO
 * License: GNU General Public License v2.0 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 *
 * @package Product Variations View Pro
 */

namespace DRO\PVVP;

use DRO\PVVP\Includes\DRO_PVVP;
use DRO\PVVP\Includes\DRO_PVVP_Dependencies;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

define( 'DRO_PVVP_FILE', __FILE__ );
define( 'DRO_PVVP_NAME', 'Product Variations View Pro' );
define( 'DRO_PVVP_INCLUDES_FOLDER', untrailingslashit( plugin_dir_path( __FILE__ ) ) . '/includes/' );

/**
 * Checks the server environment and deactivates plugins as necessary.
 *
 * @since 1.0.0
 */
function activation_check() {
	$dependencies = new DRO_PVVP_Dependencies();
	if ( ! $dependencies->check_php_version() ) {

		deactivate_plugins( plugin_basename( DRO_PVVP_FILE ) );
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Nonce not needed as "activate" is used for display purposes only.
		if ( isset( $_GET['activate'] ) ) {
			unset( $_GET['activate'] );
		}

		wp_die(
			sprintf(
				/* translators: %s is the plugin name. */
				esc_html__( '%s could not be activated.', 'product-variations-view-pro' ),
				esc_html( DRO_PVVP_NAME )
			) . esc_html( $dependencies->get_php_notice() )
		);

	}
	update_option( 'dro_pvvp_is_enabled', 1 );
	update_option( 'dro_pvvp_show_range_price', 1 );
	update_option( 'dro_pvvp_show_main_product_short_description', 1 );
	update_option( 'dro_pvvp_show_product_gallery', 1 );
}
register_activation_hook( DRO_PVVP_FILE, __NAMESPACE__ . '\\activation_check' );
/**
 * Registers the custom autoloader for plugin classes.
 *
 * Supports class autoloading with nested subfolders based on namespace structure.
 *
 * - Filters classes by namespace prefix using __NAMESPACE__.
 * - Converts class name (underscored) to WordPress-style file format.
 * - Maps sub-namespaces to lowercased directory paths.
 *
 * Uses:
 * - array_pop() to extract class basename.
 * - array_map() and array_slice() to handle directory mapping.
 *
 * @since 1.1.0
 * @return void
 */
function register_autoloader() {
	spl_autoload_register(
		function ( $class_name ) {
			// Ensure the class is part of the current namespace.
			if ( strncmp( __NAMESPACE__ . '\\', $class_name, strlen( __NAMESPACE__ ) + 1 ) !== 0 ) {
				return;
			}

			$class_parts    = explode( '\\', $class_name );
			$class_basename = array_pop( $class_parts );
			$class_filename = strtolower( str_replace( '_', '-', $class_basename ) );

			$class_parts   = array_map( 'strtolower', $class_parts );
			$relative_path = implode( '/', array_slice( $class_parts, 2 ) );

			$full_path = __DIR__ . '/' . $relative_path . '/class-' . $class_filename . '.php';

			if ( file_exists( $full_path ) ) {
				require $full_path;
			}
		}
	);
}

/**
 * Returns the main instance of  DRO_PVVP.
 */
function dro_pvvp() {
	register_autoloader();
	return DRO_PVVP::start( new DRO_PVVP_Dependencies() );
}

dro_pvvp();

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
