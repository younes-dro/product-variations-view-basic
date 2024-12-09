<?php
/**
 * WooCommerce Custom Variable Product add to cart
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
 * @package Variation Carousel for WooCommerce/Templates
 * @since   1.0.0
 * @version 1.0.0
 */
defined( 'ABSPATH' ) || exit;

global $product, $post;

$variations = $product->get_available_variations();

$product_attributes = $product->get_variation_attributes();

// echo '<pre>';
// var_dump($product_attributes);
// echo '</pre>';

/**
 * woocommerce_before_add_to_cart_form hook.
 */
do_action( 'woocommerce_before_add_to_cart_form' );
?>

<form class="cart cvp-form dro-variable-products-form" action="<?php echo esc_url( $product->add_to_cart_url() ); ?>" method="post" enctype="multipart/form-data">
	<input type="texy" name="cvp-product-parent-id" value="<?php echo esc_attr( $post->ID)?>">
	<?php
	/**
	 * woocommerce_before_variations_form hook.
	 */
	do_action( 'woocommerce_before_variations_form' );
	?>
	<div id="variable-products-carousel" class="carousel slide" data-interval="false" data-ride="carousel">


		<?php apply_filters( 'woocommerce_cvp_carousel_indicators', count( $variations ) ); ?>
		<div class="carousel-inner" role="listbox">
			<?php
			foreach ( $variations as $variation ) {
				$active = ( $variation === reset( $variations ) ) ? 'active' : '';
				?>
				<div class="carousel-item  <?php echo $active; ?>">
					<!--<div class="container">--> 
					<div class="carousel-content">
						<div class="row">                          
							<div class=" col-6 col-sm-8" style="text-align: left">
								<div class="attribute-thumb-container">
								<img class="  attribute-thumb" src="<?php echo esc_url( $variation['image']['url'] ); ?>"  alt="<?php echo esc_attr( $variation['image']['alt'] ); ?>" />
								</div>
							</div>
							<div class="col-6 col-sm-4 carousel-nav" >
								
								<a class="carousel-control-prev" href="#variable-products-carousel" role="button" data-slide="prev">
									<span class="carousel-control-prev-icon" aria-hidden="true"></span>
									
									<span class="sr-only"><?php esc_html_e( 'Previous', 'product-variations-view' ); ?></span>
								   
								</a>
								<a class="carousel-control-next" href="#variable-products-carousel" role="button" data-slide="next">
									<span class="carousel-control-next-icon" aria-hidden="true"></span>
									<span class="sr-only"><?php esc_html_e( 'Next', 'product-variations-view' ); ?></span>
								</a>                                
							</div>                          
						</div>
						<div class="row">                                                               
							<div class="col-12">

								<?php foreach ( $product_attributes as $attribute_name => $options ) : ?>
									<div class="row attibutes-wrapper">
										<div class="col-12 col-md-4 attribute-title-col">
											<label for="" class="attribute-title"><?php echo wc_attribute_label( $attribute_name ); ?>:</label>
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
									<a href="#" class="description-toggle"><?php esc_html_e( 'Description', 'product-variations-view' ); ?><span class="toggle"></span></a>
								</div>
								<div class="col-12 description-variation-container">
									<p class="description-variation"><?php echo wp_kses( $variation['variation_description'], array( '' ) ); ?></p>
								</div>                                
							</div>
						<?php endif; ?>

						<div class="row">
							<div class="col-12">
								<?php echo $variation['price_html']; ?>
								<input type="hidden" class="display_regular_price" value="<?php print_r( $variation['display_regular_price'] ); ?>" />
								<input type="hidden" class="display_price" value="<?php print_r( $variation['display_price'] ); ?>" />
								<input type="hidden" name="variation_id[]" value="<?php echo $variation['variation_id']; ?>" />
								<input type="hidden" name="product_id[]" value="<?php echo esc_attr( $post->ID ); ?>" />
								<input type="hidden" name="add-to-cart" value="<?php echo esc_attr( $post->ID ); ?>" />
								
								<?php
								if ( ! empty( $variation['attributes'] ) ) {
									foreach ( $variation['attributes'] as $attr_key => $attr_value ) {
										?>
										<input type="hidden" name="<?php echo $attr_key; ?>" value="<?php echo $attr_value; ?>">
										<?php
									}
								}
								?>
							</div>
						</div>
						<div class="row">
							<div class="col-12">
							<?php
								$input_id = uniqid( 'quantity_' ); // Unique ID for the input field

								// TODO : use product name 
								$label = ! empty( $variation['attributes'] ) 
									? sprintf( esc_html__( '%s quantity', 'woocommerce' ), wp_strip_all_tags( $attribute_name ) )
									: esc_html__( 'Quantity', 'woocommerce' );
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
							 * woocommerce_cvp_variation_data hook
							 */
							global $current_variation;
							$current_variation = wc_get_product( $variation['variation_id'] );
							do_action( 'woocommerce_cvp_variation_data', $current_variation );
						?>
<!--                        <div class="row">
							<div class="col-12">
								<?php
								// var_dump(wc_get_price_to_display($current_variation, array('price'=>$current_variation->get_regular_price())));
								?>
								<br>
								<?php
								// var_dump(wc_get_price_to_display($current_variation, array('price'=>$current_variation->get_price())));
								?>
								<br>
								<?php
								// var_dump(wc_get_price_including_tax($current_variation));
								?>
								<br>
								<?php
								// var_dump(wc_get_price_excluding_tax($current_variation));
								?>
								<?php
								// echo wc_get_formatted_variation($current_variation);
								?>
							</div>
						</div>-->
<!--                        <div class="row carousel-nav-bottom">
							<div class="col-6 carousel-nav">
								<a  class="float-left carousel-control-prev" href="#variable-products-carousel" role="button" data-slide="prev">
									<span class="carousel-control-prev-icon" aria-hidden="true"></span>
									<span class="sr-only"><?php esc_html_e( 'Previous', 'product-variations-view' ); ?></span>
								</a>
							</div>
							<div class="col-6  carousel-nav">
								<a  class="float-right carousel-control-next" href="#variable-products-carousel" role="button" data-slide="next">
									<span class="carousel-control-next-icon" aria-hidden="true"></span>
									<span class="sr-only"><?php esc_html_e( 'Next', 'product-variations-view' ); ?></span>
								</a>
							</div>
						</div>                        -->
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
 * woocommerce_after_add_to_cart_form hook.
 */
do_action( 'woocommerce_after_add_to_cart_form' );


