<?php
/**
 * Template Functions
 *
 * Functions for the Product Variations View Pro templating system.
 *
 * @author   Younes DRO
 * @category Core
 * @package  Product Variations View Pro
 * @since    1.0.0
 * @version  1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use function DRO\PVVP\dro_pvvp;

if ( ! function_exists( 'dro_pvvp_variation_attribute_options' ) ) {

	/**
	 * Output a list of variation attributes for use in the cart forms.
	 *
	 * @param array $args Arguments.
	 * @since 1.0.0
	 */
	function dro_pvvp_variation_attribute_options( $args = array() ) {

		$product              = $args['product'];
		$attribute_name       = $args['attribute_name'];
		$options              = $args['options'];
		$variation_attributes = $args['variation_attributes'];
		$selected_key         = 'attribute_' . sanitize_title( $attribute_name );
		$current_attribute    = $variation_attributes[ $selected_key ];

		if ( isset( $current_attribute ) && ! empty( $current_attribute ) ) {

			foreach ( $variation_attributes as $key_attribute => $value ) {

				if ( $selected_key === $key_attribute && ! empty( $value ) ) {
					echo '<span name="attribute_' . esc_html( $attribute_name ) . '" class="attribute-name">';
					$tax = str_replace( 'attribute_', '', $key_attribute );
					if ( $product && taxonomy_exists( $attribute_name ) && get_term_by( 'slug', $value, $tax ) ) {

						$attr_name = esc_html( apply_filters( 'woocommerce_variation_option_name', get_term_by( 'slug', $value, $tax )->name, get_term_by( 'slug', $value, $tax ), $attribute_name, $product ) );
						echo esc_html( $attr_name );
					} else {
						$attr_name = esc_html( apply_filters( 'woocommerce_variation_option_name', $value, null, $attribute_name, $product ) );
						echo esc_html( $attr_name );
					}
					echo '</span>';
					return;
				}
			}
			// Any.
		} else {
			$html  = '<select name="attribute_' . $attribute_name . '" id="' . esc_attr( $attribute_name ) . '">';
			$html .= '<option value="">' . esc_html__( 'Choose an option', 'product-variations-view-pro' ) . '</option>';

			foreach ( $options as $key_option => $option ) {

				if ( $product && taxonomy_exists( $attribute_name ) && get_term_by( 'slug', $option, $attribute_name ) ) {
					$attr_name = esc_html( apply_filters( 'woocommerce_variation_option_name', get_term_by( 'slug', $option, $attribute_name )->name, get_term_by( 'slug', $option, $attribute_name ), $attribute_name, $product ) );

				} else {
					$attr_name = esc_html( apply_filters( 'woocommerce_variation_option_name', $option, null, $attribute_name, $product ) );
				}

				$html .= '<option value="' . esc_attr( $option ) . '">' . $attr_name . '</option>';
			}
			$html .= '</select>';
			echo wp_kses(
				$html,
				array(
					'select' => array(
						'name' => array(),
						'id'   => array(),
					),
					'option' => array(
						'value' => array(),
					),
				)
			);

		}
	}
}

if ( ! function_exists( 'dro_pvvp_template_variation_data' ) ) {

	function dro_pvvp_template_variation_data( $dro_pvvp_current_variation ) {

		global $dro_pvvp_current_variation;

		wc_get_template(
			'single-product/pvvp/dro-pvvp-variation-data.php',
			array( 'variation' => $dro_pvvp_current_variation ),
			'woocommerce',
			dro_pvvp()->plugin_path() . '/templates/'
		);
	}
}

if ( ! function_exists( 'dro_pvvp_template_carousel_indicators' ) ) {

	function dro_pvvp_template_carousel_indicators( $indicators ) {

		wc_get_template(
			'single-product/pvvp/dro-pvvp-carousel-indicators.php',
			array(
				'indicators' => $indicators,
			),
			'woocommerce',
			dro_pvvp()->plugin_path() . '/templates/'
		);
	}
}

if ( ! function_exists( 'dro_pvvp_template_reset_button' ) ) {

	function dro_pvvp_template_reset_button() {

		global $product;

		wc_get_template(
			'single-product/pvvp/dro-pvvp-reset.php',
			array(
				'product' => $product,
			),
			'woocommerce',
			dro_pvvp()->plugin_path() . '/templates/'
		);
	}
}

if ( ! function_exists( 'dro_pvvp_template_add_to_cart_wrap' ) ) {

	function dro_pvvp_template_add_to_cart_wrap() {

		global $product;
		// Consider to use wc_get_template_html().
		wc_get_template(
			'single-product/add-to-cart/dro-pvvp-add-to-cart-wrap.php',
			array(
				'product' => $product,
			),
			'woocommerce',
			dro_pvvp()->plugin_path() . '/templates/'
		);
	}
}
