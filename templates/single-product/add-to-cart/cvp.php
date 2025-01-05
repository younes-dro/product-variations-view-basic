<?php
/**
 * Product Variations View Pro add to cart
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/add-to-cart/cvp.php.
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

defined( 'ABSPATH' ) || exit;

global $product, $post;

$variations = $product->get_available_variations();

$product_attributes = $product->get_variation_attributes();

/**
 * Woocommerce_before_add_to_cart_form hook.
 */
do_action( 'woocommerce_before_add_to_cart_form' );
?>

<form class="cart cvp-form dro-variable-products-form" action="<?php echo esc_url( $product->add_to_cart_url() ); ?>" method="post" enctype="multipart/form-data">
	<input type="hidden" name="cvp-product-parent-id" value="<?php echo esc_attr( $post->ID ); ?>">
	<?php
	/**
	 * Woocommerce_before_variations_form hook.
	 */
	do_action( 'woocommerce_before_variations_form' );
	?>
	<div id="variable-products-carousel" class="carousel slide" data-interval="false" data-ride="carousel">

		<?php apply_filters( 'woocommerce_cvp_carousel_indicators', count( $variations ) ); ?>
		<div class="carousel-inner" role="listbox">
			<?php
			foreach ( $variations as $variation ) {
				$active = ( reset( $variations ) === $variation ) ? 'active' : '';
				?>
				<div class="carousel-item  <?php echo esc_attr( $active ); ?>">
					<div class="carousel-content">
					<div class="row pr-4">
						<div class="col-12">
							<div class="col-6 col-sm-4 carousel-nav ml-auto">
							<button class="carousel-control-prev" type="button" data-bs-target="#variable-products-carousel" data-bs-slide="prev">
    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
    <span class="visually-hidden">Previous</span>
  </button>
  <button class="carousel-control-next" type="button" data-bs-target="#variable-products-carousel" data-bs-slide="next">
    <span class="carousel-control-next-icon" aria-hidden="true"></span>
    <span class="visually-hidden">Next</span>
  </button>                               
							</div>
						</div>
					</div>
						<div class="row">                          
							<div class=" col-6 col-sm-8" style="text-align: left">
								<div class="attribute-thumb-container">
							<?php

							$image_url = esc_url( $variation['image']['url'] );
							$image_alt = esc_attr( $variation['image']['alt'] );
							$image_id  = attachment_url_to_postid( $image_url );
							if ( $image_id ) {
								$attachment_variation = wp_get_attachment_image(
									$image_id,
									'thumbnail',
									false,
									array(
										'class' => 'attribute-thumb',
										'alt'   => $image_alt,
									)
								);
									echo $attachment_variation; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
							} else {
								?>
								<img class="attribute-thumb" alt="No image defined" />
								<?php
							}
							?>
																
								</div>
							</div>                          
						</div>
						<div class="row">                                                               
							<div class="col-12">

								<?php foreach ( $product_attributes as $attribute_name => $options ) : ?>
									<div class="row attibutes-wrapper">
										<div class="col-12 col-md-4 attribute-title-col">
											<label for="" class="attribute-title">
												<?php
												/* translators: %s : attribute label */
												printf( esc_html__( '%s:', 'product-variations-view-pro' ), esc_html( wc_attribute_label( $attribute_name ) ) );


												?>
												</label>
										</div>
										<div class="col-12 col-md-8 attribute-name-col">

											<?php
											wc_cvp_variation_attribute_options(
												array(
													'var_id'  => $variation['variation_id'],
													'variation_attributes' => $variation['attributes'],
													'options' => $options,
													'attribute_name' => $attribute_name,
													'product' => $product,
												)
											);
											?>

										</div>
									</div>
								<?php endforeach; ?>

							</div>
						</div>
						<?php if ( ! empty( trim( $variation['variation_description'] ) ) ) : ?>
							<div class="row description-variation-wrapper">
								<div class="col-12">
									<a href="#" class="description-toggle"><?php esc_html_e( 'Description', 'product-variations-view-pro' ); ?><span class="toggle"></span></a>
								</div>
								<div class="col-12 description-variation-container">
									<p class="description-variation"><?php echo wp_kses( $variation['variation_description'], array( '' ) ); ?></p>
								</div>                                
							</div>
						<?php endif; ?>

						<div class="row">
							<div class="col-12">
								<?php
								$price_html = wp_kses_post( $variation['price_html'] );
								printf( '%s', $price_html );// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Output is already sanitized with wp_kses_post().

								?>
								<input type="hidden" class="display_regular_price" value="<?php echo esc_attr( $variation['display_regular_price'] ); ?>" />
								<input type="hidden" class="display_price" value="<?php echo esc_attr( $variation['display_price'] ); ?>" />
								<input type="hidden" name="variation_id[]" value="<?php echo esc_attr( $variation['variation_id'] ); ?>" />
								<input type="hidden" name="product_id[]" value="<?php echo esc_attr( $post->ID ); ?>" />
								<input type="hidden" name="add-to-cart" value="<?php echo esc_attr( $post->ID ); ?>" />
								
								<?php
								if ( ! empty( $variation['attributes'] ) ) {
									foreach ( $variation['attributes'] as $attr_key => $attr_value ) {
										?>
										<input type="hidden" name="<?php echo esc_attr( $attr_key ); ?>" value="<?php echo esc_attr( $attr_value ); ?>">
										<?php
									}
								}
								?>
							</div>
						</div>
						<div class="row">
							<div class="col-12">
							<?php
								$input_id = uniqid( 'quantity_' );

								/* translators: %s : attribute name */
								$label = ! empty( $variation['attributes'] ) ? sprintf( esc_html__( '%s quantity', 'product-variations-view-pro' ), wp_strip_all_tags( $attribute_name ) ) : esc_html__( 'Quantity', 'product-variations-view-pro' );
							?>

								<div class="quantity">
									<label class="screen-reader-text" for="<?php echo esc_attr( $input_id ); ?>"><?php echo esc_html( $label ); ?></label>
									<input 
										type="number" 
										id="<?php echo esc_attr( $input_id ); ?>" 
										class="input-text text" 
										name="cvp-quantity[]" 
										value="0" 
										min="0" 
										max="<?php echo esc_attr( $product->backorders_allowed() ? '' : $product->get_stock_quantity() ); ?>" 
										step="1" 
										placeholder="0" 
										data-variation-id="<?php echo esc_attr( $variation['variation_id'] ); ?>" 
										aria-label="<?php echo esc_attr( $label ); ?>" 
									/>
								</div>

							</div>
						</div>
						<?php

							/**
							 * Woocommerce_cvp_variation_data hook
							 */
							global $current_variation;
							$current_variation = wc_get_product( $variation['variation_id'] );
							do_action( 'woocommerce_cvp_variation_data', $current_variation );
						?>
					</div><!-- .carousel-content -->
				</div>
				<?php
			}
			?>

		</div><!-- .carousel-inner -->
	</div><!-- .carousel -->
	<?php
	do_action( 'woocommerce_cvp_add_to_cart_wrap', $product );
	?>
</form>

<?php
/**
 * Woocommerce_after_add_to_cart_form hook.
 */
do_action( 'woocommerce_after_add_to_cart_form' );


