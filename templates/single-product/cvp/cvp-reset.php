<?php
/**
 * Product Variations View Pro Reset Button
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/cvp/cvp-reset.php.
 *
 * HOWEVER, on occasion Variation Carousel for WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @author  Younes DRO
 * @package Product Variations View Pro
 * @since   1.0.0
 * @version 1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="container-fluid">
	<div class="row">
		<div class="col-12">
		<a href="#" id="cvp-reset" class="col-12 cvp-reset"><?php esc_html_e( 'Reset', 'product-variations-view-pro' ); ?></a>
		</div>
	</div>
</div>
