<?php

/**
 * Front-End Display
 *
 * @class Product_Variations_View_Pro_Display
 * @version 1.0.0
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

/**
 * Product_Variations_View_Pro_Display.
 *
 * Carousel Variable Products front-end functions.
 */
class Product_Variations_View_Pro_Display {

    function __construct() {
        add_action( 'init', array( $this, 'remove_woocommerce_variable_add_to_cart' ) );
        add_action( 'wp_enqueue_scripts', array( $this, 'frontend_scripts' ) );
    }

    public function remove_woocommerce_variable_add_to_cart() {
        remove_action( 'woocommerce_variable_add_to_cart', 'woocommerce_variable_add_to_cart', 30 );
        add_action( 'woocommerce_variable_add_to_cart', array( $this, 'cvp_variable_add_to_cart' ), 30 );
    }

    /**
     * Add-to-cart template for Product Variations View Pro.
     *
     * @global type $product
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

    /*
    -----------------------------------------------------------------------------------
      Scripts and Styles                                                              
    -----------------------------------------------------------------------------------*/

    /**
     * Load scripts only in Single Product Variable Type.
     */
    public function frontend_scripts() {
        $the_product = wc_get_product( get_the_ID() );

        // If we don't have the context Product we stop here.
        if ( ! $the_product ) {
            return;
        }

        // Make sure we are viewing a single product variable.
        if ( $the_product->is_type( 'variable' ) && is_product() ) {

            wp_register_style( 'wc-cvp-frontend', Product_Variations_View_Pro()->plugin_url() . '/assets/css/frontend/cvp-frontend.css', array(), Product_Variations_View_Pro()->version );
            wp_enqueue_style( 'wc-cvp-frontend' );

            wp_register_style( 'bootstrap-css', Product_Variations_View_Pro()->plugin_url() . '/assets/vendor/bootstrap/css/bootstrap.css', array(), Product_Variations_View_Pro()->version );
            wp_enqueue_style( 'bootstrap-css' );

            wp_register_script( 'bootstrap-js', Product_Variations_View_Pro()->plugin_url() . '/assets/vendor/bootstrap/js/bootstrap.js', array( 'jquery' ), Product_Variations_View_Pro()->version, true );
            wp_enqueue_script( 'bootstrap-js' );

            wp_register_script( 'wc-add-to-cart-cvp', Product_Variations_View_Pro()->plugin_url() . '/assets/js/frontend/add-to-cart-cvp.js', array( 'jquery', 'bootstrap-js' ), Product_Variations_View_Pro()->version, true );
            wp_enqueue_script( 'wc-add-to-cart-cvp' );

            /**
            * Trim Zeros setting.
            *
            * @param  array $params
            */
            $trim_zeros = apply_filters( 'woocommerce_price_trim_zeros', false );

            $params = apply_filters(
                'woocommerce_cvp_add_to_cart_parameters',
                array(
                    'i18n_total'                         => esc_html__( 'Total: ', 'product-variations-view' ),
                    'i18n_empty_error'                   => esc_html__( 'Please select at least 1 item to continue&hellip;', 'product-variations-view' ),
                    'currency_symbol'                    => get_woocommerce_currency_symbol(),
                    'currency_position'                  => esc_attr( stripslashes( get_option( 'woocommerce_currency_pos' ) ) ),
                    'currency_format_num_decimals'       => absint( wc_get_price_decimals() ),
                    'currency_format_precision_decimals' => absint( wc_get_rounding_precision() ),
                    'thousand_separator'                 => esc_attr( stripslashes( get_option( 'woocommerce_price_thousand_sep' ) ) ),
                    'decimal_separator'                  => esc_attr( stripslashes( get_option( 'woocommerce_price_decimal_sep' ) ) ),
                    'trim_zeros'                         => $trim_zeros ? 'yes' : 'no',
                    'price_display_suffix'               => esc_attr( get_option( 'woocommerce_price_display_suffix' ) ),
                    'prices_include_tax'                 => esc_attr( get_option( 'woocommerce_prices_include_tax' ) ),
                    'tax_display_shop'                   => esc_attr( get_option( 'woocommerce_tax_display_shop' ) ),
                    'calc_taxes'                         => esc_attr( get_option( 'woocommerce_calc_taxes' ) ),
                )
            );

            wp_localize_script( 'wc-add-to-cart-cvp', 'wc_cvp_params', $params );
        }
    }
}
