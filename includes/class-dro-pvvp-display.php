<?php
/**
 * Front-End Display
 *
 * @class    DRO_PVVP_Display
 * @version  1.0.0
 * @since    1.0.0
 * @package  Product Variations View Pro
 * @author   Younes DRO
 * @email    younesdro@gmail.com
 */

namespace DRO\PVVP\Includes;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

use function DRO\PVVP\dro_pvvp;



/**
 * Handles the front-end display and functionality for Product Variations View Pro.
 *
 * @since 1.0.0
 */
class DRO_PVVP_Display {

	/**
	 * Instance of the DRO_PVVP_Display class.
	 *
	 * Verify the requirements
	 *
	 * @var DRO_PVVP_Display|null
	 */
	private static $instance;

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
		add_action( 'wp_ajax_wc_cvp_add_to_cart', array( $this, 'dro_pvvp_add_bulk_variation' ) );
		add_action( 'wp_ajax_nopriv_wc_cvp_add_to_cart', array( $this, 'dro_pvvp_add_bulk_variation' ) );
		add_filter( 'woocommerce_get_price_html', array( $this, 'remove_variable_price_range_on_product_page' ), 10, 2 );
		add_action( 'wp', array( $this, 'remove_short_description_from_product_page' ) );
	}

	/**
	 * Gets the DRO_PVVP_Disaply instance.
	 *
	 * @since 1.0.0
	 * @return DRO_PVVP_Display instance
	 */
	public static function start_display(): DRO_PVVP_Display {

		self::$instance ??= new self();

		return self::$instance;
	}

	/**
	 * Replaces WooCommerce's default add-to-cart functionality for variable products.
	 *
	 * @since 1.0.0
	 */
	public function remove_woocommerce_variable_add_to_cart() {
		remove_action( 'woocommerce_variable_add_to_cart', 'woocommerce_variable_add_to_cart', 30 );
		add_action( 'woocommerce_variable_add_to_cart', array( $this, 'dro_pvvp_variable_add_to_cart' ), 30 );
	}

	/**
	 * Loads the custom add-to-cart template for Product Variations View Pro.
	 *
	 * @since 1.0.0
	 */
	public function dro_pvvp_variable_add_to_cart() {
		global $product;

		wc_get_template(
			'single-product/add-to-cart/dro-pvvp.php',
			array(
				'container' => $product,
			),
			'',
			dro_pvvp()->plugin_path() . '/templates/'
		);
	}

	/**
	 * Enqueues front-end scripts and styles.
	 *
	 * @since 1.0.0
	 */
	public function frontend_scripts() {
		$the_product = wc_get_product( get_the_ID() );

		if ( ! $the_product || ! $the_product->is_type( 'variable' ) || ! function_exists( 'is_product' ) || ! \is_product() ) {
			return;
		}

		$min = WP_DEBUG ? '' : '.min';

		wp_register_style( 'dro-pvvp-frontend', dro_pvvp()->plugin_url() . '/assets/css/frontend/dro-pvvp-frontend' . $min . '.css', array(), dro_pvvp()->version );
		wp_enqueue_style( 'dro-pvvp-frontend' );

		wp_register_style( 'bootstrap-css', dro_pvvp()->plugin_url() . '/assets/vendor/bootstrap/css/bootstrap' . $min . '.css', array(), dro_pvvp()->version );
		wp_enqueue_style( 'bootstrap-css' );

		wp_register_script( 'bootstrap-js', dro_pvvp()->plugin_url() . '/assets/vendor/bootstrap/js/bootstrap' . $min . '.js', array( 'jquery' ), dro_pvvp()->version, true );
		wp_enqueue_script( 'bootstrap-js' );

		wp_register_script( 'dro-pvvp-add-to-cart', dro_pvvp()->plugin_url() . '/assets/js/frontend/dro-pvvp-add-to-cart' . $min . '.js', array( 'jquery', 'bootstrap-js' ), fileatime( __FILE__ ), true );
		wp_enqueue_script( 'dro-pvvp-add-to-cart' );

		$params = array(
			'currency_symbol'               => get_woocommerce_currency_symbol(),
			'currency_format_decimal_sep'   => wc_get_price_decimal_separator(),
			'currency_format_thousand_sep'  => wc_get_price_thousand_separator(),
			'currency_format_num_decimals'  => wc_get_price_decimals(),
			'currency_position'             => sanitize_key( get_option( 'woocommerce_currency_pos' ) ),
			'currency_format_trim_zeros'    => sanitize_key( get_option( 'woocommerce_price_trim_zeros', 'no' ) ),
			'ajax_url'                      => admin_url( 'admin-ajax.php' ),
			'cvp_nonce'                     => wp_create_nonce( 'cvp_add_to_cart_nonce' ),
			'dro_pvvp_show_product_gallery' => (bool) filter_var( get_option( 'dro_pvvp_show_product_gallery', true ), FILTER_VALIDATE_BOOLEAN ),
		);

		wp_localize_script( 'dro-pvvp-add-to-cart', 'dro_pvvp_params', $params );
	}

	/**
	 * Handles the AJAX request to add multiple variations to the WooCommerce cart.
	 *
	 * Validates the nonce and input data, then adds each variation to the cart.
	 *
	 * @since 1.0.0
	 */
	public function dro_pvvp_add_bulk_variation() {
		$cvp_nonce = isset( $_POST['cvp_nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['cvp_nonce'] ) ) : null;
		if ( ! isset( $cvp_nonce ) || ! wp_verify_nonce( $cvp_nonce, 'cvp_add_to_cart_nonce' ) ) {
			wp_send_json_error( array( 'message' => esc_html__( 'Invalid nonce.', 'product-variations-view-pro' ) ) );
		}

		$products = ( isset( $_POST['products'] ) ) ? wp_unslash( $_POST['products'] ) : null;

		if ( is_array( $products ) ) {
			$products = array_map(
				function ( $product ) {
					// Sanitize individual product data.
					return array(
						'variation_id' => isset( $product['variation_id'] ) ? absint( $product['variation_id'] ) : 0,
						'quantity'     => isset( $product['quantity'] ) ? absint( $product['quantity'] ) : 0,
						'attributes'   => isset( $product['attributes'] ) && is_array( $product['attributes'] )
							? array_map( 'sanitize_text_field', $product['attributes'] )
							: array(),
					);
				},
				$products
			);
		} else {
			$products = array();
		}

		if ( ! isset( $products ) || ! is_array( $products ) ) {
			wp_send_json_error( array( 'message' => __( 'No products were provided.', 'product-variations-view-pro' ) ) );
		}

		$product_parent_id = isset( $_POST['parent_id'] ) ? sanitize_text_field( wp_unslash( $_POST['parent_id'] ) ) : '';

		$cart_items_added = array();

		foreach ( $products as $product ) {
			if ( isset( $product['variation_id'], $product['quantity'], $product['attributes'] ) ) {
				$variation_id = intval( $product['variation_id'] );
				$quantity     = intval( $product['quantity'] );
				$attributes   = $product['attributes'];
				$variation    = wc_get_product( $variation_id );

				if ( ! $variation || 'variation' !== $variation->get_type() ) {
					continue;
				}

				$parent_id = $variation->get_parent_id();
				$product   = wc_get_product( $parent_id );

				$variations = array();
				foreach ( $attributes as $key => $value ) {
					if ( 'attribute_' !== substr( $key, 0, 10 ) ) {
						continue;
					}

					$variations[ sanitize_title( wp_unslash( $key ) ) ] = wp_unslash( $value );
				}

				$passed_validation = apply_filters( 'woocommerce_add_to_cart_validation', true, $product_parent_id, $quantity, $variation_id, $variations );

				if ( ! $passed_validation ) {
					return false;
				}
				// Prevent parent variable product from being added to cart.
				if ( empty( $variation_id ) && $product && $product->is_type( 'variable' ) ) {
					/* translators: 1: product link, 2: product name */
					wc_add_notice( sprintf( __( 'Please choose product options by visiting <a href="%1$s" title="%2$s">%2$s</a>.', 'product-variations-view-pro' ), esc_url( get_permalink( $product_id ) ), esc_html( $product->get_name() ) ), 'error' );

					return false;
				}

				$added = WC()->cart->add_to_cart( $parent_id, $quantity, $variation_id, $variations );

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
					'message'    => __( 'Products added to cart successfully.', 'product-variations-view-pro' ),
					'cart_items' => $cart_items_added,
				)
			);
		} else {
			wp_send_json_error( array( 'message' => __( 'No products were added to the cart.', 'product-variations-view-pro' ) ) );
		}
	}


	/**
	 * Removes the price range display for variable products on the single product detail page.
	 *
	 * @param string $price   The HTML string for the product price.
	 * @param object $product The current product object.
	 *
	 * @return string The modified price HTML (empty for variable products on product detail page).
	 */
	public function remove_variable_price_range_on_product_page( $price, $product ) {

		$show_price = (bool) get_option( 'dro_pvvp_show_range_price', true );
		if ( ! $show_price && $product->get_type() === 'variable' && function_exists( 'is_product' ) && \is_product() ) {
			return '';
		}

		return $price;
	}

	/**
	 * Hides the short description for variable products on the single product detail page.
	 *
	 * @return void
	 */
	public function remove_short_description_from_product_page() {

		if ( function_exists( 'is_product' ) && \is_product() ) {
			$show_short_description = (bool) get_option( 'dro_pvvp_show_main_product_short_description', true );
			global $post;
			if ( isset( $post->ID ) ) {
				$product = wc_get_product( $post->ID );
				if ( ! $show_short_description && $product && $product->is_type( 'variable' ) ) {
					remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_excerpt', 20 );
				}
			}
		}
	}
}
