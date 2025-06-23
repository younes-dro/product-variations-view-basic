<?php
/**
 * WooCommerce Variation Data Provider Implementation
 *
 * @package DRO\PVVP\Includes\Providers
 */

namespace DRO\PVVP\Includes\Providers;

use DRO\PVVP\Includes\Interfaces\DRO_PVVP_Variation_Data_Provider_Interface;
use WC_Product;
use WC_Product_Variable;
use WC_Product_Variation;

defined( 'ABSPATH' ) || exit;

class DRO_PVVP_Variation_Data_Provider implements DRO_PVVP_Variation_Data_Provider_Interface {

	/**
	 * Current product context
	 *
	 * @var WC_Product|null
	 */
	private ?WC_Product $product = null;

	/**
	 * Singleton instance
	 *
	 * @var self|null
	 */
	private static ?self $instance = null;

	/**
	 * Get singleton instance
	 *
	 * @return self
	 */
	public static function get_instance(): self {
		return self::$instance ??= new self();
	}

	/**
	 * Set the product context for variation operations
	 *
	 * @param WC_Product $product The WooCommerce product.
	 * @return self
	 */
	public function set_product( WC_Product $product ): self {
		$this->product = $product;
		return $this;
	}

	/**
	 * Get all available variations for the current product
	 *
	 * @return array Array of available variations
	 */
	public function get_available_variations(): array {
		if ( ! $this->product || ! $this->product->is_type( 'variable' ) ) {
			return array();
		}

		/** @var WC_Product_Variable $product */
		$product = $this->product;
		return $product->get_available_variations();
	}

	/**
	 * Get main image for a variation
	 *
	 * @param int $variation_id Variation ID.
	 * @return array|null Image data or null if not found.
	 */
	public function get_variation_main_image( int $variation_id ): ?array {
		$variation = $this->get_variation_product( $variation_id );
		if ( ! $variation ) {
			return null;
		}

		$image_id = $variation->get_image_id();

		// Fallback to parent product image if variation has no image
		if ( ! $image_id && $this->product ) {
			$image_id = $this->product->get_image_id();
		}

		if ( ! $image_id ) {
			return null;
		}

		$image_src = wp_get_attachment_image_src( $image_id, 'full' );
		if ( ! $image_src ) {
			return null;
		}

		return array(
			'id'     => $image_id,
			'url'    => $image_src[0],
			'width'  => $image_src[1],
			'height' => $image_src[2],
			'alt'    => get_post_meta( $image_id, '_wp_attachment_image_alt', true ),
			'title'  => get_the_title( $image_id ),
		);
	}

	/**
	 * Get thumbnail images for a variation
	 *
	 * @param int    $variation_id Variation ID.
	 * @param string $size Image size.
	 * @return array Array of thumbnail data.
	 */
	public function get_variation_thumbs( int $variation_id, string $size = 'thumbnail' ): array {
		$variation = $this->get_variation_product( $variation_id );
		if ( ! $variation ) {
			return array();
		}
		$gallery_ids = get_post_meta( $variation_id, 'dro_pvvp_variation_images', true );
		// $gallery_ids = $variation->get_gallery_image_ids();

		$thumbnails = array();

		foreach ( $gallery_ids as $image_id ) {
			$image_src = wp_get_attachment_image_src( $image_id, $size );
			if ( $image_src ) {
				$thumbnails[] = array(
					'id'     => $image_id,
					'url'    => $image_src[0],
					'width'  => $image_src[1],
					'height' => $image_src[2],
					'alt'    => get_post_meta( $image_id, '_wp_attachment_image_alt', true ),
					'title'  => get_the_title( $image_id ),
				);
			}
		}

		return $thumbnails;
	}

	/**
	 * Get variation attributes
	 *
	 * @param int $variation_id Variation ID.
	 * @return array Variation attributes.
	 */
	public function get_variation_attributes( int $variation_id ): array {
		$variation = $this->get_variation_product( $variation_id );
		return $variation ? $variation->get_attributes() : array();
	}

	/**
	 * Get variation stock information
	 *
	 * @param int $variation_id Variation ID.
	 * @return array|null Stock information or null if not found.
	 */
	public function get_variation_stock( int $variation_id ): ?array {
		$variation = $this->get_variation_product( $variation_id );
		if ( ! $variation ) {
			return null;
		}

		return array(
			'in_stock'       => $variation->is_in_stock(),
			'stock_quantity' => $variation->get_stock_quantity(),
			'stock_status'   => $variation->get_stock_status(),
			'manage_stock'   => $variation->get_manage_stock(),
			'backorders'     => $variation->get_backorders(),
		);
	}

	/**
	 * Get variation pricing information
	 *
	 * @param int $variation_id Variation ID.
	 * @return array|null Pricing information or null if not found.
	 */
	public function get_variation_pricing( int $variation_id ): ?array {
		$variation = $this->get_variation_product( $variation_id );
		if ( ! $variation ) {
			return null;
		}

		return array(
			'price'         => $variation->get_price(),
			'regular_price' => $variation->get_regular_price(),
			'sale_price'    => $variation->get_sale_price(),
			'price_html'    => $variation->get_price_html(),
			'on_sale'       => $variation->is_on_sale(),
		);
	}

	/**
	 * Check if variation exists and is valid
	 *
	 * @param int $variation_id Variation ID
	 * @return bool True if variation is valid
	 */
	public function is_valid_variation( int $variation_id ): bool {
		$variation = $this->get_variation_product( $variation_id );
		return $variation && $variation->exists() && $variation->is_type( 'variation' );
	}

	/**
	 * Get variation product object
	 *
	 * @param int $variation_id Variation ID.
	 * @return WC_Product_Variation|null Variation product or null if not found.
	 */
	private function get_variation_product( int $variation_id ): ?WC_Product_Variation {
		$variation = wc_get_product( $variation_id );

		if ( ! $variation || ! $variation->is_type( 'variation' ) ) {
			return null;
		}

		/** @var WC_Product_Variation $variation */
		return $variation;
	}
}
