<?php
/**
 * Single Product Image
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/product-image.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 9.0.0
 */

use DRO\PVVP\Includes\Gallery\Factories\DRO_PVVP_Gallery_Factory as Gallery_Factory;

defined( 'ABSPATH' ) || exit;

// Note: `wc_get_gallery_image_html` was added in WC 3.3.2 and did not exist prior. This check protects against theme overrides being used on older versions of WC.
if ( ! function_exists( 'wc_get_gallery_image_html' ) ) {
	return;
}

global $product;

if ( ! $product ) {
	return;
}
$gallery_config = apply_filters(
	'dro_pvvp_gallery_config',
	array(
		'layout'           => get_option( 'dro_pvvp_gallery_layout', 'default' ),
		'thumb_position'   => get_option( 'dro_pvvp_thumb_position', 'left' ),
		'slider_enabled'   => get_option( 'dro_pvvp_slider_enabled', true ),
		'lightbox_enabled' => get_option( 'dro_pvvp_lightbox_enabled', false ),
		'lazy_loading'     => get_option( 'dro_pvvp_lazy_loading', true ),
		'thumb_size'       => get_option( 'dro_pvvp_thumb_size', 'thumbnail' ),
		'main_size'        => get_option( 'dro_pvvp_main_size', 'large' ),
	),
	$product
);


try {
	$gallery_html = Gallery_Factory::get_instance()
	->create_gallery_layout( $gallery_config['layout'] )
	->render( $product );
} catch ( Exception $e ) {
	
	error_log( 'DRO_PVVP Gallery Error: ' . $e->getMessage() );
	// Load default WooCommerce product image template
	wc_get_template( 'single-product/product-image.php' );
	return;
}

// Output the gallery
echo $gallery_html;

?>

