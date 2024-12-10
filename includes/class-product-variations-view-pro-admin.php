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
namespace DRO\ProductVariationsViewPro\Includes;

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
		add_action( 'admin_enqueue_scripts', array( $this, 'register_pvv_settings_script' ) );
		add_action( 'admin_menu', array( $this, 'add_pvv_menu' ) );
		add_action( 'wp_ajax_pvv_save_settings', array( $this, 'save_pvv_settings' ) );
		add_action( 'woocommerce_variation_header', array( $this, 'display_missing_attributes_warning' ), 10, 2 );
	}

	/**
	 * Adds a top-level menu for the plugin settings.
	 *
	 * @since 1.0.0
	 */
	public function add_pvv_menu() {
		add_menu_page(
			esc_html__( 'Product Variations View', 'product-variations-view' ),
			esc_html__( 'Variations View', 'product-variations-view' ),
			'manage_options',
			'product-variations-view',
			array( $this, 'render_pvv_settings' ),
			'dashicons-admin-generic',
			26
		);
	}

	/**
	 * Saves Product Variations View plugin settings via AJAX.
	 *
	 * This function handles the AJAX request to save plugin settings.
	 * The settings are saved in the WordPress options table.
	 *
	 * @since 1.0.0
	 *
	 * @return void Outputs a JSON response indicating success or failure.
	 */
	public function save_pvv_settings() {

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( array( 'message' => 'Unauthorized.' ), 403 );
		}

		check_ajax_referer( 'pvv_settings_nonce', 'security' );

		$is_enabled           = isset( $_POST['is_enabled'] ) ? filter_var( $_POST['is_enabled'], FILTER_VALIDATE_BOOLEAN ) : false;
		$show_price           = isset( $_POST['show_price'] ) ? filter_var( $_POST['show_price'], FILTER_VALIDATE_BOOLEAN ) : false;
		$show_description     = isset( $_POST['show_description'] ) ? filter_var( $_POST['show_description'], FILTER_VALIDATE_BOOLEAN ) : false;
		$show_product_gallery = isset( $_POST['show_product_gallery'] ) ? filter_var( $_POST['show_product_gallery'], FILTER_VALIDATE_BOOLEAN ) : false;

		update_option( 'pvv_is_enabled', $is_enabled );
		update_option( 'pvv_show_range_price', $show_price );
		update_option( 'pvv_show_main_product_short_description', $show_description );
		update_option( 'pvv_show_product_gallery', $show_product_gallery );

		wp_send_json_success( array( 'message' => 'Settings saved successfully.' ) );
	}


	/**
	 * Renders the plugin settings page.
	 *
	 * This function will display the ReactJS-based UI.
	 *
	 * @since 1.0.0
	 */
	public function render_pvv_settings() {
		wp_enqueue_script( 'product-variations-view-settings' );
		echo '<div id="pvv-app"></div>';
	}

	/**
	 * Registers the settings script and passes settings to React.
	 *
	 * @since 1.0.0
	 */
	public function register_pvv_settings_script() {

		$settings_version = file_exists( plugin_dir_path( __DIR__ ) . 'assets/js/admin/settings.js' )
		? filemtime( plugin_dir_path( __DIR__ ) . 'assets/js/admin/settings.js' )
		: '1.0.0';

		wp_register_script(
			'product-variations-view-settings',
			plugin_dir_url( __DIR__ ) . 'assets/js/admin/settings.js',
			array( 'wp-element' ),
			$settings_version,
			true
		);

		wp_register_style(
			'product-variations-view-settings',
			plugin_dir_url( __DIR__ ) . 'assets/css/admin/settings.css',
			array(),
			$settings_version
		);

		$current_settings = array(
			'is_enabled'           => (bool) get_option( 'pvv_is_enabled', true ),
			'show_price'           => (bool) get_option( 'pvv_show_range_price', true ),
			'show_description'     => (bool) get_option( 'pvv_show_main_product_short_description', true ),
			'show_product_gallery' => (bool) get_option( 'pvv_show_product_gallery', true ),
		);

		wp_localize_script(
			'product-variations-view-settings',
			'pvv_ajax_params',
			array(
				'ajax_url' => admin_url( 'admin-ajax.php' ),
				'nonce'    => wp_create_nonce( 'pvv_settings_nonce' ),
				'settings' => $current_settings, // Pass settings to React.
			)
		);
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
