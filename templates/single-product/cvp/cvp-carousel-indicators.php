<?php
/**
 * Product Variations View Pro Indicators
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
 * @package Product Variations View Pro
 * @since   1.0.0
 * @version 1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$available_variations = sprintf( '%1$s %2$s %3$s', esc_html__( 'Available in ', 'product-variations-view-pro' ), $indicators, esc_html__( 'variations', 'product-variations-view-pro' ) );
echo '<h6 class="available-variations">' . esc_html( $available_variations ) . '</h6>'; // WPCS: XSS OK.


?>

<ol class="carousel-indicators">
	<?php
	for ( $indicator = 0; $indicator < $indicators; $indicator++ ) :

		$default_active = ( 0 === $indicator ) ? 'active' : '';
		?>
		<li 
			data-target="#variable-products-carousel" 
			data-slide-to="<?php echo esc_attr( $indicator ); ?>" 
			class="<?php echo esc_attr( $default_active ); ?>">
			<span><?php echo esc_html( $indicator + 1 ); ?></span>
		</li>
	<?php endfor; ?>  

</ol>
