<?php
// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

foreach ( $variation_images as $image_id ) :
	$image      = wp_get_attachment_image_src( $image_id );
	$input_name = sprintf( 'dro_pvvp_variation_image_collections[%d][]', $variation_id );
	?>
	<li class="image">
		<input class="dro-pvvp-variation_id_input" type="hidden" name="<?php echo esc_attr( $input_name ); ?>" value="<?php echo absint( $image_id ); ?>">
		<img data-id="<?php echo absint( $image_id ); ?>" src="<?php echo esc_url( $image[0] ); ?>">
		<a href="#" class="delete dro-pvvp-variation-images-remove-image"><span class="dashicons dashicons-dismiss"></span></a>
	</li>

<?php endforeach; ?>
