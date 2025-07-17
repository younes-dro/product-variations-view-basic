<?php
/**
 * WooCommerce Variation Data Provider Implementation.
 *
 * Provides reusable methods to retrieve variation data for WooCommerce variable products.
 *
 * @package     DRO\PVVP\Includes\Providers
 * @version     1.0.0
 * @since       1.0.0
 * @author      Younes DRO <younesdro@gmail.com>
 * @license     GPL-2.0-or-later
 */

namespace DRO\PVVP\Includes\Providers;

use WC_Product;
use WC_Product_Variable;
use WC_Product_Variation;
use DRO\PVVP\Includes\Gallery\Interfaces\DRO_PVVP_Variation_Data_Provider_Interface;
use DRO\PVVP\Includes\Exceptions\DRO_PVVP_Invalid_Product_Exception as Invalid_Product_Exception;

defined( 'ABSPATH' ) || exit;

/**
 * Class DRO_PVVP_Variation_Data_Provider
 *
 * Implements data retrieval logic for WooCommerce variable products and their variations.
 */
class DRO_PVVP_Variation_Data_Provider implements DRO_PVVP_Variation_Data_Provider_Interface {

	/**
	 * Current WooCommerce product context.
	 *
	 * @var WC_Product|null
	 */
	private ?WC_Product $product = null;

	/**
	 * Singleton instance.
	 *
	 * @var self|null
	 */
	private static ?self $instance = null;

	/**
	 * Get singleton instance.
	 *
	 * @return self
	 */
	public static function get_instance(): self {
		return self::$instance ??= new self();
	}

	/**
	 * Set the product context for data retrieval.
	 *
	 * @param WC_Product $product WooCommerce product.
	 * @return self
	 */
	public function set_product( WC_Product $product ): self {
		$this->product = $product;
		return $this;
	}

	/**
	 * Get available variations for the current product.
	 *
	 * @return array List of available variation data.
	 * @throws Invalid_Product_Exception If the product is not set or not variable.
	 */
	public function get_available_variations(): array {
		if ( ! $this->product || ! $this->product->is_type( 'variable' ) ) {
			throw new Invalid_Product_Exception( 'The product is either not set or is not a variable product.' );
		}

		/** @var WC_Product_Variable $product */
		$product = $this->product;
		return $product->get_available_variations();
	}

	/**
	 * Get main image data for a given variation.
	 *
	 * @param int $variation_id Variation ID.
	 * @return array|null Main image data or null if not found.
	 */
	public function get_variation_main_image( int $variation_id ): ?array {
		$variation = $this->get_variation_product( $variation_id );
		if ( ! $variation ) {
			return null;
		}

		$image_id = $variation->get_image_id();

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
	 * Get thumbnail images for a given variation.
	 *
	 * @param int    $variation_id Variation ID.
	 * @param string $size          Image size.
	 * @return array List of thumbnails.
	 */
	public function get_variation_thumbs( int $variation_id, string $size = 'thumbnail' ): array {
		$variation = $this->get_variation_product( $variation_id );
		if ( ! $variation ) {
			return array();
		}

		$gallery_ids = get_post_meta( $variation_id, 'dro_pvvp_variation_images', true );

		if ( empty( $gallery_ids ) || ! is_array( $gallery_ids ) ) {
			return array();
		}

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
	 * Get variation attributes.
	 *
	 * @param int $variation_id Variation ID.
	 * @return array Attributes array.
	 */
	public function get_variation_attributes( int $variation_id ): array {
		$variation = $this->get_variation_product( $variation_id );
		return $variation ? $variation->get_attributes() : array();
	}

	/**
	 * Get stock data for a given variation.
	 *
	 * @param int $variation_id Variation ID.
	 * @return array|null Stock data or null if not available.
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
	 * Get price data for a given variation.
	 *
	 * @param int $variation_id Variation ID.
	 * @return array|null Price data or null if not available.
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
	 * Validate a variation by ID.
	 *
	 * @param int $variation_id Variation ID.
	 * @return bool True if valid variation.
	 */
	public function is_valid_variation( int $variation_id ): bool {
		$variation = $this->get_variation_product( $variation_id );
		return $variation && $variation->exists() && $variation->is_type( 'variation' );
	}

	/**
	 * Get variation product object from ID.
	 *
	 * @param int $variation_id Variation ID.
	 * @return WC_Product_Variation|null Product variation object or null.
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
