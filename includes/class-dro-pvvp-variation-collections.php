<?php
/**
 * Class DRO_PVVP_Variation_Collections
 *
 * Handles variation collections for WooCommerce products.
 *
 * @package DRO\PVVP\Includes
 * @version 1.1.0
 */

namespace DRO\PVVP\Includes;

use WC_Product;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class DRO_PVVP_Variation_Collections {

	/**
	 * Singleton instance of the class.
	 *
	 * @var self|null
	 */
	private static ?self $instance = null;

	/**
	 * The WooCommerce product object.
	 *
	 * @var WC_Product|null
	 */
	private $product;

	public function __construct() {
	}

	/**
	 * Get the singleton instance of the class.
	 *
	 * @return self The singleton instance.
	 */
	public static function get_instance(): self {
		return self::$instance ??= new self();
	}

	/**
	 * Set the WooCommerce product object.
	 *
	 * @param WC_Product|null $product The WooCommerce product object.
	 * @return self The current instance for method chaining.
	 */
	public function set_product( ?WC_Product $product ): self {
		$this->product = $product;
		return $this;
	}

	/**
	 * Get the WooCommerce product object.
	 *
	 * @return WC_Product|null The WooCommerce product object or null if not set.
	 */
	public function get_product(): ?WC_Product {
		return $this->product ?? null;
	}

	/**
	 * Get the variation image collections for the current product.
	 *
	 * @return string|null The HTML gallery for the variations or null if no variations are available.
	 */
	public function get_variation_image_collections() {
		$availabe_variations = $this->get_variation_collections( $this->product );
		if ( ! $availabe_variations ) {
			return;
		}
		foreach ( $availabe_variations as $variation ) {

			$active = ( reset( $availabe_variations ) === $variation ) ? 'active' : '';

			$variation_id = $variation['variation_id'];
			// Main variation image.
			$main_image = $this->get_variation_main_image( $variation_id );
			// Variation thumbs.
			$thumbs = $this->get_variation_thumbs( $variation_id );

			$galllery = $this->build_gallery( $main_image, $thumbs );

			return $galllery;
		}
	}

	/**
	 * Get the available variation collections for a product.
	 *
	 * @param WC_Product $product The WooCommerce product object.
	 * @return array The available variations as an array.
	 */
	private function get_variation_collections( WC_Product $product ): array {

		if ( ! $product || ! $product->is_type( 'variable' ) ) {
			return array();
		}
		$available_variations = $product->get_available_variations();
		if ( ! is_array( $available_variations ) || empty( $available_variations ) ) {
			return array();
		}
		return $available_variations;
	}

	/**
	 * Get the main image for a variation.
	 *
	 * @param int $variation_id The ID of the variation.
	 * @return string The HTML for the main variation image.
	 */
	private function get_variation_main_image( int $variation_id ): string {

		$image_url = esc_url( $variation['image']['url'] );
		$image_alt = esc_attr( $variation['image']['alt'] );
		$image_id  = attachment_url_to_postid( $image_url );
		$image     = wp_get_attachment_image(
			$image_id,
			'full',
			false,
			array(
				'class' => 'dro-pvvp-main-image',
				'alt'   => $image_alt,
			)
		);
		if ( ! $image ) {
			$image = '<img src="' . esc_url( wc_placeholder_img_src() ) . '" alt="' . esc_attr__( 'Placeholder', 'woocommerce' ) . '" />';
		}
		return $image;
	}

	/**
	 * Get the thumbnails for a variation.
	 *
	 * @param int $variation_id The ID of the variation.
	 * @return array The thumbnails as an array.
	 */
	private function get_variation_thumbs( int $variation_id ): array {

		$thumbs = get_post_meta( $variation_id, 'dro_pvvp_variation_images', true );

		return $thumbs;
	}

	/**
	 * Build the HTML gallery for a variation.
	 *
	 * @param string $main_image The main image HTML.
	 * @param array  $thumbs The thumbnails as an array.
	 * @return string The HTML gallery.
	 */
	private function build_gallery( string $main_image, array $thumbs ): string {

		/**
		 * Here we will build our gallery
		 *
		 * Divided into 2 columns:
		 * - Main image: Right column
		 * - Thumbs: Below the main image ( Default value )  or left column.
		 */
		return $main_image . implode( '', $thumbs );
	}
}
