<?php
/**
 * Front-End Display
 *
 * @class    PvvFront
 * @version  1.0.0
 * @since    1.0.0
 * @package  Pvv
 * @author   Younes DRO
 * @email    younesdro@gmail.com
 */

namespace DRO\Pvv;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

use function DRO\Pvv\Pvv;

/**
 * Handles the front-end display and functionality for Product Variations View Pro.
 *
 * @since 1.0.0
 */
class PvvFront {

	/**
	 * Instance of the PvvFront class.
	 *
	 * Verify the requirements
	 *
	 * @var PvvFront|null
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
		add_action( 'wp_ajax_wc_cvp_add_to_cart', array( $this, 'cvp_add_bulk_variation' ) );
		add_action( 'wp_ajax_nopriv_wc_cvp_add_to_cart', array( $this, 'cvp_add_bulk_variation' ) );
		add_filter( 'woocommerce_get_price_html', array( $this, 'remove_variable_price_range_on_product_page' ), 10, 2 );
		add_action( 'wp', array( $this, 'remove_short_description_from_product_page' ) );
	}

	/**
	 * Gets the Pvv_Disaply instance.
	 *
	 * @since 1.0.0
	 * @return PvvFront instance
	 */
	public static function start_display(): PvvFront {

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
			Pvv()->plugin_path() . '/templates/'
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

		wp_register_style( 'wc-cvp-frontend', Pvv()->plugin_url() . '/assets/css/frontend/cvp-frontend.css', array(), Pvv()->version );
		wp_enqueue_style( 'wc-cvp-frontend' );

		wp_register_style( 'bootstrap-css', Pvv()->plugin_url() . '/assets/vendor/bootstrap/css/bootstrap.css', array(), Pvv()->version );
		wp_enqueue_style( 'bootstrap-css' );

		wp_register_script( 'bootstrap-js', Pvv()->plugin_url() . '/assets/vendor/bootstrap/js/bootstrap.js', array( 'jquery' ), Pvv()->version, true );
		wp_enqueue_script( 'bootstrap-js' );

		wp_register_script( 'wc-add-to-cart-cvp', Pvv()->plugin_url() . '/assets/js/frontend/add-to-cart-cvp.js', array( 'jquery', 'bootstrap-js' ), fileatime( __FILE__ ), true );
		wp_enqueue_script( 'wc-add-to-cart-cvp' );

		$params = array(
			'currency_symbol'              => get_woocommerce_currency_symbol(),
			'currency_format_decimal_sep'  => wc_get_price_decimal_separator(),
			'currency_format_thousand_sep' => wc_get_price_thousand_separator(),
			'currency_format_num_decimals' => wc_get_price_decimals(),
			'currency_position'            => get_option( 'woocommerce_currency_pos' ),
			'currency_format_trim_zeros'   => get_option( 'woocommerce_price_trim_zeros', 'no' ),
			'ajax_url'                     => admin_url( 'admin-ajax.php' ),
			'cvp_nonce'                    => wp_create_nonce( 'cvp_add_to_cart_nonce' ),
			'pvv_show_product_gallery'     => (bool) get_option( 'pvv_show_product_gallery', true ),
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

		$products          = $_POST['products'];
		$product_parent_id = $_POST['parent_id'];
		// error_log( 'POSTS: ' . print_r( $_POST, true ) );
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
				// error_log( 'Attributes : ' . print_r( $product->get_attributes(), true));
				// foreach ( $product->get_attributes() as $attribute_name => $attribute ) {
				// $taxonomy = 'attribute_' . sanitize_title( $attribute_name );
				// error_log( print_r( $attributes[ $taxonomy ], true));
				// if ( isset( $attributes[ $taxonomy ] ) ) {
				// $variations[ $taxonomy ] = sanitize_text_field( $attributes[ $taxonomy ] );
				// } elseif ( $attribute->get_variation() && ! array_key_exists( $taxonomy, $variations ) ) {
				// wp_send_json_error( array( 'message' => sprintf( __( 'Missing attribute: %s', 'product-variations-view' ), wc_attribute_label( $attribute_name ) ) ) );
				// }
				// }
				// error_log( __METHOD__ .  ' : Variationss: '  . print_r( $variations, true) );
				// error_log("Parent ID: $parent_id, Quantity: $quantity, Variation ID: $variation_id, Variations: " . print_r($variations, true));

				$passed_validation = apply_filters( 'woocommerce_add_to_cart_validation', true, $product_parent_id, $quantity, $variation_id, $variations );
				// error_log( 'PAssed Validation: ' . print_r( $passed_validation, true ) );
				if ( ! $passed_validation ) {
					return false;
				}
						// Prevent parent variable product from being added to cart.
				if ( empty( $variation_id ) && $product && $product->is_type( 'variable' ) ) {
					/* translators: 1: product link, 2: product name */
					wc_add_notice( sprintf( __( 'Please choose product options by visiting <a href="%1$s" title="%2$s">%2$s</a>.', 'woocommerce' ), esc_url( get_permalink( $product_id ) ), esc_html( $product->get_name() ) ), 'error' );

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
					'message'    => __( 'Products added to cart successfully.', 'product-variations-view' ),
					'cart_items' => $cart_items_added,
				)
			);
		} else {
			wp_send_json_error( array( 'message' => __( 'No products were added to the cart.', 'product-variations-view' ) ) );
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

		$show_price = (bool) get_option( 'pvv_show_range_price', true );
		if ( ! $show_price && $product->get_type() === 'variable' && is_product() ) {
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

		if ( is_product() ) {
			$show_short_description = (bool) get_option( 'pvv_show_main_product_short_description', true );
			global $post;
			$product = wc_get_product( $post->ID );
			if ( ! $show_short_description && $product && $product->is_type( 'variable' ) ) {
				remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_excerpt', 20 );
			}
		}
	}
}