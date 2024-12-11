<?php
/**
 * WooCommerce Custom Variable Product Variation Data.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/cvp/cvp-variation-data.php.
 *
 * HOWEVER, on occasion Variation Carousel for WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @author  Younes DRO
 * @package Variation Carousel for WooCommerce/Templates
 * @since   1.0.0
 * @version 1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$price_incl_tax = wc_get_price_including_tax( $variation );
$price_excl_tax = wc_get_price_excluding_tax( $variation );

?>
<div class="cvp-variation-data" 
	data-cvp_variation_id="<?php echo esc_attr( $variation->get_ID() ); ?>" 
	data-regular_price="<?php echo esc_attr( wc_get_price_to_display( $variation, array( 'price' => $variation->get_regular_price() ) ) ); ?>" 
	data-price="<?php echo esc_attr( wc_get_price_to_display( $variation, array( 'price' => $variation->get_price() ) ) ); ?>" 
	data-price_incl_tax="<?php echo esc_attr( $price_incl_tax ); ?>" 
	data-price_excl_tax="<?php echo esc_attr( $price_excl_tax ); ?>" >
</div>


