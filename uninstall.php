<?php
/**
 * Fired when the plugin is uninstalled.
 *
 * @since 1.0.0
 * @package Product Variations View Pro
 */

// If uninstall not called from WordPress, exit.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

// Delete plugin-specific options.
delete_option( 'dro_pvvp_is_enabled' );
delete_option( 'dro_pvvp_show_main_product_short_description' );
delete_option( 'dro_pvvp_show_product_gallery' );
delete_option( 'dro_pvvp_show_range_price' );
