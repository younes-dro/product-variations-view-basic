<?php

/**
 * Template Functions
 *
 * Functions for the Variation Carousel for WooCommerce templating system.
 *
 * @author   Younes DRO
 * @category Core
 * @package  Variation Carousel for WooCommerce/Functions
 * @since    1.0.0
 * @version  1.0.0
 */
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use function DRO\ProductVariationsViewPro\product_variations_view_pro;

if ( ! function_exists( 'wc_cvp_variation_attribute_options' ) ) {

	/**
	 * Output a list of variation attributes for use in the cart forms.
	 *
	 * @param array $args Arguments.
	 * @since 1.0.0
	 */
	function wc_cvp_variation_attribute_options( $args = array() ) {

		$product              = $args['product'];
		$attribute_name       = $args['attribute_name'];
		$options              = $args['options'];
		$variation_attributes = $args['variation_attributes'];

		$selected_key = 'attribute_' . sanitize_title( $attribute_name );

		// echo '<pre>' . var_dump($variation_attributes).'</pre><hr>';
		// echo '<pre>' . var_dump($options).'</pre>';
		// var_dump($variation_attributes[ $selected_key]);

		$current_attribute = $variation_attributes[ $selected_key ];

		// var_dump( $current_attribute );

		if ( isset( $current_attribute ) && ! empty( $current_attribute ) ) {

			foreach ( $variation_attributes as $key_attribute => $value ) {

				if ( $selected_key === $key_attribute && ! empty( $value ) ) {
					echo '<span name="attribute_' . $attribute_name. '" class="attribute-name">';
					$tax = str_replace( 'attribute_', '', $key_attribute );
					if ( $product && taxonomy_exists( $attribute_name ) && get_term_by( 'slug', $value, $tax ) ) {

						// echo get_term_by('slug', $value, $tax)->name;
						$attr_name = esc_html( apply_filters( 'woocommerce_variation_option_name', get_term_by( 'slug', $value, $tax )->name, get_term_by( 'slug', $value, $tax ), $attribute_name, $product ) );
						echo $attr_name;
						// var_dump(get_term_by('slug', $value, $tax));
						// return;
					} else {
						$attr_name = esc_html( apply_filters( 'woocommerce_variation_option_name', $value, null, $attribute_name, $product ) );
						echo $attr_name;
						// echo wc_attribute_label($value, $product);
						// return;
					}
					echo '</span>';
					return;
				}
			}
			// Any
		} else {
			$html  = '<select name="attribute_' . $attribute_name. '" id="' . esc_attr( $attribute_name ) . '">';
			$html .= '<option value="">' . esc_html__( 'Choose an option', 'product-variations-view' ) . '</option>';

			// $tax = str_replace('attribute_', '', $key_attribute);

			foreach ( $options as $key_option => $option ) {

				if ( $product && taxonomy_exists( $attribute_name ) && get_term_by( 'slug', $option, $attribute_name ) ) {

					$attr_name = esc_html( apply_filters( 'woocommerce_variation_option_name', get_term_by( 'slug', $option, $attribute_name )->name, get_term_by( 'slug', $option, $attribute_name ), $attribute_name, $product ) );

					// $attr_name =  get_term_by('slug', $option, $attribute_name)->name ;

				} else {
					$attr_name = esc_html( apply_filters( 'woocommerce_variation_option_name', $option, null, $attribute_name, $product ) );
				}

				$html .= '<option value="' . esc_attr( $option ) . '">' . $attr_name . '</option>';
			}
			$html .= '</select>';
			echo $html;
		}
	}
}

if ( ! function_exists( 'wc_cvp_template_variation_data' ) ) {

	function wc_cvp_template_variation_data( $current_variation ) {

		global $current_variation;

		wc_get_template(
			'single-product/cvp/cvp-variation-data.php',
			array( 'variation' => $current_variation ),
			'woocommerce',
			Product_Variations_View_Pro()->plugin_path() . '/templates/'
		);
	}
}

if ( ! function_exists( 'wc_cvp_template_carousel_indicators' ) ) {

	function wc_cvp_template_carousel_indicators( $indicators ) {

		wc_get_template(
			'single-product/cvp/cvp-carousel-indicators.php',
			array(
				'indicators' => $indicators,
			),
			'woocommerce',
			Product_Variations_View_Pro()->plugin_path() . '/templates/'
		);
	}
}

if ( ! function_exists( 'wc_cvp_template_reset_button' ) ) {

	function wc_cvp_template_reset_button() {

		global $product;

		wc_get_template(
			'single-product/cvp/cvp-reset.php',
			array(
				'product' => $product,
			),
			'woocommerce',
			Product_Variations_View_Pro()->plugin_path() . '/templates/'
		);
	}
}

if ( ! function_exists( 'wc_cvp_template_add_to_cart_wrap' ) ) {

	function wc_cvp_template_add_to_cart_wrap() {

		global $product;
		// consider to use wc_get_template_html()
		wc_get_template(
			'single-product/add-to-cart/cvp-add-to-cart-wrap.php',
			array(
				'product' => $product,
			),
			'woocommerce',
			Product_Variations_View_Pro()->plugin_path() . '/templates/'
		);
	}
}

