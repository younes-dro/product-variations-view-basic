<?php
/**
 * Admin Class for Product Variations View Pro Plugin.
 *
 * Handles all admin-specific functionality.
 *
 * @version  1.0.0
 * @since    1.0.0
 * @package  Product Variations View Pro
 * @author   Younes DRO
 * @email    younesdro@gmail.com
 */

namespace DRO\PVVP\Includes;

use function DRO\PVVP\dro_pvvp;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Class DRO_PVVP_Admin
 *
 * This class encapsulates all the admin-specific functionality for the plugin.
 * It manages tasks like handling missing attribute warnings, adding admin notices,
 * and customizing WooCommerce backend behavior.
 *
 * @since 1.0.0
 */
class DRO_PVVP_Admin {


	/**
	 * Instance of the DRO_PVVP_Admin class.
	 *
	 * Verify the requirements
	 *
	 * @var DRO_PVVP_Admin|null
	 */
	private static $instance;

	/**
	 * Constructor.
	 * Initializes the class and hooks admin-specific actions.
	 *
	 * @since 1.0.0
	 */
	private function __construct() {
		add_action( 'admin_enqueue_scripts', array( $this, 'register_dro_pvvp_settings_script' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_custom_media_script_for_variable_products' ) );
		add_action( 'admin_menu', array( $this, 'add_dro_pvvp_menu' ) );
		add_action( 'wp_ajax_dro_pvvp_save_settings', array( $this, 'save_dro_pvvp_settings' ) );
		add_action( 'woocommerce_variation_header', array( $this, 'display_missing_attributes_warning' ), 10, 2 );
		add_action( 'woocommerce_product_after_variable_attributes', array( $this, 'add_varaition_image_collections' ), 10, 3 );
	}

	/**
	 * Gets the DRO_PVVP_Admin instance.
	 *
	 * Ensures only one instance of DRO_PVVP_Admin is loaded or can be loaded.
	 *
	 * @since 1.0.0
	 * @return DRO_PVVP_Admin instance
	 */
	public static function start_admin(): DRO_PVVP_Admin {

		self::$instance ??= new self();

		return self::$instance;
	}

	/**
	 * Adds a top-level menu for the plugin settings.
	 *
	 * @since 1.0.0
	 */
	public function add_dro_pvvp_menu() {
		add_menu_page(
			esc_html__( 'Product Variations View', 'product-variations-view-pro' ),
			esc_html__( 'Variations View', 'product-variations-view-pro' ),
			'manage_options',
			'product-variations-view-pro',
			array( $this, 'render_dro_pvvp_settings' ),
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
	public function save_dro_pvvp_settings() {

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( array( 'message' => 'Unauthorized.' ), 403 );
		}

		check_ajax_referer( 'dro_pvvp_settings_nonce', 'security' );

		$is_enabled           = isset( $_POST['is_enabled'] ) ? filter_var( wp_unslash( $_POST['is_enabled'] ), FILTER_VALIDATE_BOOLEAN ) : false;
		$show_price           = isset( $_POST['show_price'] ) ? filter_var( wp_unslash( $_POST['show_price'] ), FILTER_VALIDATE_BOOLEAN ) : false;
		$show_description     = isset( $_POST['show_description'] ) ? filter_var( wp_unslash( $_POST['show_description'] ), FILTER_VALIDATE_BOOLEAN ) : false;
		$show_product_gallery = isset( $_POST['show_product_gallery'] ) ? filter_var( wp_unslash( $_POST['show_product_gallery'] ), FILTER_VALIDATE_BOOLEAN ) : false;

		update_option( 'dro_pvvp_is_enabled', $is_enabled );
		update_option( 'dro_pvvp_show_range_price', $show_price );
		update_option( 'dro_pvvp_show_main_product_short_description', $show_description );
		update_option( 'dro_pvvp_show_product_gallery', $show_product_gallery );

		wp_send_json_success( array( 'message' => 'Settings saved successfully.' ) );
	}


	/**
	 * Renders the plugin settings page.
	 *
	 * This function will display the ReactJS-based UI.
	 *
	 * @since 1.0.0
	 */
	public function render_dro_pvvp_settings() {
		wp_enqueue_script( 'product-variations-view-settings' );
		echo '<div id="pvv-app"></div>';
	}

	/**
	 * Registers the settings script and passes settings to React.
	 *
	 * @since 1.0.0
	 */
	public function register_dro_pvvp_settings_script() {
		$min = WP_DEBUG ? '' : '.min';

		$settings_version = file_exists( plugin_dir_path( __DIR__ ) . 'assets/js/admin/settings' . $min . '.js' )
		? filemtime( plugin_dir_path( __DIR__ ) . 'assets/js/admin/settings' . $min . '.js' )
		: '1.0.0';

		wp_register_script(
			'product-variations-view-settings',
			plugin_dir_url( __DIR__ ) . 'assets/js/admin/settings' . $min . '.js',
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
			'is_enabled'           => (bool) filter_var( get_option( 'dro_pvvp_is_enabled', true ), FILTER_VALIDATE_BOOLEAN ),
			'show_price'           => (bool) filter_var( get_option( 'dro_pvvp_show_range_price', true ), FILTER_VALIDATE_BOOLEAN ),
			'show_description'     => (bool) filter_var( get_option( 'dro_pvvp_show_main_product_short_description', true ), FILTER_VALIDATE_BOOLEAN ),
			'show_product_gallery' => (bool) filter_var( get_option( 'dro_pvvp_show_product_gallery', true ), FILTER_VALIDATE_BOOLEAN ),
		);

		wp_localize_script(
			'product-variations-view-settings',
			'dro_pvvp_ajax_params',
			array(
				'ajax_url' => admin_url( 'admin-ajax.php' ),
				'nonce'    => wp_create_nonce( 'dro_pvvp_settings_nonce' ),
				'settings' => $current_settings, // Pass settings to React.
			)
		);
	}

	/**
	 * Enqueues the custom media script for variable products on the WooCommerce product edit page.
	 *
	 * This function checks if the current admin screen is the product edit screen and whether the product type
	 * is 'variable'. If both conditions are met, it enqueues the WordPress Media Library and a custom JavaScript file.
	 *
	 * @return void
	 */
	public function enqueue_custom_media_script_for_variable_products() {
		global $post;

		$screen = get_current_screen();

		if ( 'product' !== $screen->post_type ) {
			return;
		}

		$product_type = function_exists( 'wc_get_product' ) ? wc_get_product( $post )->get_type() : '';
		if ( 'variable' !== $product_type ) {
			return;
		}

		wp_enqueue_media();

		$min     = WP_DEBUG ? '' : '.min';
		$version = file_exists( plugin_dir_path( __DIR__ ) . 'assets/js/admin/dro-pvvp-add-variation-images' . $min . '.js' )
			? filemtime( plugin_dir_path( __DIR__ ) . 'assets/js/admin/dro-pvvp-add-variation-images' . $min . '.js' )
			: '1.0.0';

		wp_enqueue_script(
			'dro-pvvp-add-variation-images',
			plugin_dir_url( __DIR__ ) . 'assets/js/admin/dro-pvvp-add-variation-images' . $min . '.js',
			array( 'jquery','underscore' ),
			$version,
			true
		);

		wp_enqueue_style(
			'dro-pvvp-variation-image-collections',
			plugin_dir_url( __DIR__ ) . 'assets/css/admin/dro-pvvp-variation-image-collections' . $min . '.css',
			array(),
			$version,
			'all'
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
					/* translators: %1$d is the variation ID, %2$s is the attribute label. */
					__( 'Variation #%1$d has an undefined attribute: %2$s', 'product-variations-view-pro' ),
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

	/**
	 * Outputs the HTML for managing multiple variation images in a WooCommerce product variation.
	 *
	 * This function generates a customizable HTML block that allows the admin to
	 * manage multiple images for a specific variation. The block includes a collapsible
	 * postbox for better organization of variation image collections.
	 *
	 * @param int     $loop           The index of the current variation in the loop.
	 * @param array   $variation_data Array containing data related to the current variation.
	 * @param WP_Post $variation      The variation object, which is an instance of WP_Post.
	 */
	public function add_varaition_image_collections( $loop, $variation_data, $variation ) {
		$variation_id     = absint( $variation->ID );
		// $variation_images = get_post_meta( $variation_id, 'dro_pvvp_variation_images', true );
		$variation_images = get_post_meta( $variation_id, 'woo_variation_gallery_images', true );
		?>
		<div data-dro-pvvp-variation-id="<?php echo esc_attr( $variation_id ); ?>" class="form-row form-row-full dro-pvvp-variation-images-container">
			<div class="dro-pvvp-variation-images-postbox">
					<div class="dro-pvvp-variation-images-postbox-header">
						<h2><?php esc_html_e( 'Variation Image Collections', 'product-variations-view-pro' ); ?></h2>
						
						<button type="button" class="dro-pvvp-handlediv" aria-expanded="true">
						<span class="screen-reader-text"><?php esc_html_e( 'Toggle panel: Variation images', 'product-variations-view-pro' ); ?></span>
							<span class="toggle-indicator" aria-hidden="true"></span>
						</button>
					</div>

					<div class="dro-pvvp-variation-images-content">
						<div class="dro-pvvp-variation-images-grid-container">
								<ul class="dro-pvvp-variation-images-grid-images">
									<?php
									if ( is_array( $variation_images ) && ! empty( $variation_images ) ) {
										wc_get_template(
											'admin/views/html-variation-image-collections.php',
											array(
												'variation_images' => $variation_images,
											),
											'',
											dro_pvvp()->plugin_path() . '/includes/'
										);
									}
									?>
								</ul>
							</div>
							<div class="dro-pvvp-variation-images-add-wrapper hide-if-no-js">
								<a href="#" data-product_variation_loop="<?php echo absint( $loop ); ?>" 
								data-product_variation_id="<?php echo esc_attr( $variation_id ); ?>" 
								class="button-primary dro-pvvp-variation-images-add-image">
								<?php esc_html_e( 'Add Image', 'product-variations-view-pro' ); ?>
							</a>
							</div>
					</div>
			</div>

		</div>
		<?php
	}
}
