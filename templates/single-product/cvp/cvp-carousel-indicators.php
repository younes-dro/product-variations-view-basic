<?php
/**
 * WooCommerce Custom Variable Product Caroousel Indicators
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/cvp/cvp-carousel-indicators.php.
 *
 * HOWEVER, on occasion Variation Carousel for WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @author  Younes DRO
 * @package Variation Carousel for WooCommerce/Templates
 * @since   1.0.0
 * @version 1.0.0
 */
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$available_variations = sprintf( '%1$s %2$s %3$s', esc_html__( 'Available in ', 'product-variations-view' ), $indicators, esc_html__( 'variations', 'product-variations-view' ) );
echo '<h6 class="available-variations">' . $available_variations . '</h6>';

?>

<ol class="carousel-indicators">
	<?php
	for ( $indicator = 0; $indicator < $indicators; $indicator++ ) :

		$defaultActive = ( $indicator === 0 ) ? 'active' : '';
		?>
		<li 
			data-target="#variable-products-carousel" 
			data-slide-to="<?php esc_attr_e( $indicator ); ?>" 
			class="<?php esc_attr_e( $defaultActive ); ?>">
			<span><?php esc_html_e( $indicator + 1 ); ?></span>
		</li>
	<?php endfor; ?>  

</ol>
