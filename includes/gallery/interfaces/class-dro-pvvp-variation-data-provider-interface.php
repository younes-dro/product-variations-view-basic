<?php
/**
 * Variation Data Provider Interface
 *
 * @package DRO\PVVP\Includes\Interfaces
 */

namespace DRO\PVVP\Includes\Gallery\Interfaces;

use DRO\PVVP\Includes\Exceptions\DRO_PVVP_Invalid_Product_Exception;
use WC_Product;

defined( 'ABSPATH' ) || exit;

interface DRO_PVVP_Variation_Data_Provider_Interface {

	/**
	 * Set the product context for variation operations
	 *
	 * @param WC_Product $product The WooCommerce product.
	 * @return self
	 */
	public function set_product( WC_Product $product ): self;

	/**
	 * Get all available variations for the current product.
	 *
	 * @return array List of available variation data.
	 * @throws DRO_PVVP_Invalid_Product_Exception If the product is not set or is not variable.
	 */
	public function get_available_variations(): array;


	/**
	 * Get main image for a variation
	 *
	 * @param int $variation_id Variation ID.
	 * @return array|null Image data or null if not found.
	 */
	public function get_variation_main_image( int $variation_id ): ?array;

	/**
	 * Get thumbnail images for a variation
	 *
	 * @param int    $variation_id Variation ID.
	 * @param string $size Image size.
	 * @return array Array of thumbnail data.
	 */
	public function get_variation_thumbs( int $variation_id, string $size = 'thumbnail' ): array;

	/**
	 * Get variation attributes
	 *
	 * @param int $variation_id Variation ID.
	 * @return array Variation attributes.
	 */
	public function get_variation_attributes( int $variation_id ): array;

	/**
	 * Get variation stock information
	 *
	 * @param int $variation_id Variation ID.
	 * @return array|null Stock information or null if not found.
	 */
	public function get_variation_stock( int $variation_id ): ?array;

	/**
	 * Get variation pricing information
	 *
	 * @param int $variation_id Variation ID.
	 * @return array|null Pricing information or null if not found.
	 */
	public function get_variation_pricing( int $variation_id ): ?array;

	/**
	 * Check if variation exists and is valid
	 *
	 * @param int $variation_id Variation ID.
	 * @return bool True if variation is valid.
	 */
	public function is_valid_variation( int $variation_id ): bool;
}
