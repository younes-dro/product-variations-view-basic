<?php
/**
 * Front-End Display
 *
 * @class    Product_Variations_View_Pro_Display
 * @version  1.0.0
 * @since    1.0.0
 * @package  Product_Variations_View_Pro
 * @author   Younes DRO
 * @email    younesdro@gmail.com
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Handles the front-end display and functionality for Product Variations View Pro.
 *
 * @since 1.0.0
 */
class Product_Variations_View_Pro_Display {

	/**
	 * Constructor.
	 *
	 * Sets up actions and filters for the front-end functionality.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'remove_woocommerce_variable_add_to_cart' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'frontend_scripts' ) );
		add_action( 'wp_ajax_wc_cvp_add_to_cart', array( $this, 'cvp_add_bulk_variation' ) );
		add_action( 'wp_ajax_nopriv_wc_cvp_add_to_cart', array( $this, 'cvp_add_bulk_variation' ) );
	}

	/**
	 * Replaces WooCommerce's default add-to-cart functionality for variable products.
	 *
	 * @since 1.0.0
	 */
	public function remove_woocommerce_variable_add_to_cart() {
		remove_action( 'woocommerce_variable_add_to_cart', 'woocommerce_variable_add_to_cart', 30 );
		add_action( 'woocommerce_variable_add_to_cart', array( $this, 'cvp_variable_add_to_cart' ), 30 );
	}

	/**
	 * Loads the custom add-to-cart template for Product Variations View Pro.
	 *
	 * @since 1.0.0
	 */
	public function cvp_variable_add_to_cart() {
		global $product;

		wc_get_template(
			'single-product/add-to-cart/cvp.php',
			array(
				'container' => $product,
			),
			'',
			Product_Variations_View_Pro()->plugin_path() . '/templates/'
		);
	}

	/**
	 * Enqueues front-end scripts and styles.
	 *
	 * @since 1.0.0
	 */
	public function frontend_scripts() {
		$the_product = wc_get_product( get_the_ID() );

		if ( ! $the_product || ! $the_product->is_type( 'variable' ) || ! is_product() ) {
			return;
		}

		wp_register_style( 'wc-cvp-frontend', Product_Variations_View_Pro()->plugin_url() . '/assets/css/frontend/cvp-frontend.css', array(), Product_Variations_View_Pro()->version );
		wp_enqueue_style( 'wc-cvp-frontend' );

		wp_register_style( 'bootstrap-css', Product_Variations_View_Pro()->plugin_url() . '/assets/vendor/bootstrap/css/bootstrap.css', array(), Product_Variations_View_Pro()->version );
		wp_enqueue_style( 'bootstrap-css' );

		wp_register_script( 'bootstrap-js', Product_Variations_View_Pro()->plugin_url() . '/assets/vendor/bootstrap/js/bootstrap.js', array( 'jquery' ), Product_Variations_View_Pro()->version, true );
		wp_enqueue_script( 'bootstrap-js' );

		wp_register_script( 'wc-add-to-cart-cvp', Product_Variations_View_Pro()->plugin_url() . '/assets/js/frontend/add-to-cart-cvp.js', array( 'jquery', 'bootstrap-js' ), Product_Variations_View_Pro()->version, true );
		wp_enqueue_script( 'wc-add-to-cart-cvp' );

		$params = apply_filters(
			'woocommerce_cvp_add_to_cart_parameters',
			array(
				'i18n_total'       => esc_html__( 'Total: ', 'product-variations-view' ),
				'i18n_empty_error' => esc_html__( 'Please select at least 1 item to continue&hellip;', 'product-variations-view' ),
				'currency_symbol'  => get_woocommerce_currency_symbol(),
				'ajax_url'         => admin_url( 'admin-ajax.php' ),
				'cvp_nonce'        => wp_create_nonce( 'cvp_add_to_cart_nonce' ),
			)
		);

		wp_localize_script( 'wc-add-to-cart-cvp', 'wc_cvp_params', $params );
	}

	/**
	 * Handles the AJAX request to add multiple variations to the WooCommerce cart.
	 *
	 * Validates the nonce and input data, then adds each variation to the cart.
	 *
	 * @since 1.0.0
	 */
	public function cvp_add_bulk_variation() {
		if ( ! isset( $_POST['cvp_nonce'] ) || ! wp_verify_nonce( $_POST['cvp_nonce'], 'cvp_add_to_cart_nonce' ) ) {
			wp_send_json_error( array( 'message' => __( 'Invalid nonce.', 'product-variations-view' ) ) );
		}

		if ( ! isset( $_POST['products'] ) || ! is_array( $_POST['products'] ) ) {
			wp_send_json_error( array( 'message' => __( 'No products were provided.', 'product-variations-view' ) ) );
		}

		$products         = $_POST['products'];
		$cart_items_added = array();

		foreach ( $products as $product ) {
			if ( isset( $product['variation_id'], $product['quantity'] ) ) {
				$variation_id = intval( $product['variation_id'] );
				$quantity     = intval( $product['quantity'] );
				$variation    = wc_get_product( $variation_id );
				if ( ! $variation || 'variation' !== $variation->get_type() ) {
					continue;
				}
				$added = WC()->cart->add_to_cart( $variation->get_parent_id(), $quantity, $variation_id );
				if ( $added ) {
					$cart_items_added[] = array(
						'variation_id'  => $variation_id,
						'quantity'      => $quantity,
						'cart_item_key' => $added,
					);
				}
			}
		}

		if ( ! empty( $cart_items_added ) ) {
			wp_send_json_success(
				array(
					'message'    => __( 'Products added to cart successfully.', 'product-variations-view' ),
					'cart_items' => $cart_items_added,
				)
			);
		} else {
			wp_send_json_error( array( 'message' => __( 'No products were added to the cart.', 'product-variations-view' ) ) );
		}
	}
}
