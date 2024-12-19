<?php
/**
 * Custom Variable Product button wrapper template
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/add-to-cart/cvp-add-to-cart-wrap.php.
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

defined( 'ABSPATH' ) || exit;
?>
<div class="cvp-add-cart container-fluid ">

	<?php
	if ( $product->is_purchasable() ) {
		?>
		<div class="row">
			<div class="col-6">
				<span class="cvp-total"><?php esc_html_e( 'Total: ', 'product-variations-view-pro' ); ?></span>
			</div>
		</div>
		<div class="row">
			<div  class="col-12">
				<?php
				/**
				 * Woocommerce_before_add_to_cart_button hook.
				 */
				do_action( 'woocommerce_before_add_to_cart_button' );
				?>

				<input type="hidden" name="add-to-cart" value="<?php echo esc_attr( $product->get_id() ); ?>" />
		
				<div class="cvp-error"></div>
				<button type="submit" id="cvp-add-to-cart-button" class="button single_add_to_cart_button alt"><?php echo esc_html( $product->single_add_to_cart_text() ); ?></button>        

				<?php
				/**
				 * Woocommerce_after_add_to_cart_button hook.
				 */
				do_action( 'woocommerce_after_add_to_cart_button' );
				?>
			</div>
		</div>
		<?php
	} else {

		printf( '<div class="row"><div class="col-12"><p class="woocommerce-info">%s</p></div></div>', esc_html__( 'This product is currently unavailable.', 'product-variations-view-pro' ) );
	}
	?>

</div>

