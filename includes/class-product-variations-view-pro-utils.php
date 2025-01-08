<?php
/**
 * Utility Class for Product Variations View Pro Plugin.
 *
 * Provides static helper functions for data validation, sanitization, and miscellaneous tasks.
 *
 * @since 1.0.0
 * @version 1.0.0
 * @package ProductVariationsViewPro\Includes
 * @author Younes DRO <younesdro@gmail.com>
 */

namespace DRO\ProductVariationsViewPro\Includes;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class Product_Variations_View_Pro_Utils
 *
 * Utility class with static methods for data processing.
 *
 * @final
 */
class Product_Variations_View_Pro_Utils {

	/**
	 * Private constructor to prevent instantiation.
	 */
	private function __construct() {}

	/**
	 * Sanitizes array elements based on their keys for product variations.
	 *
	 * This method processes each array element and sanitizes its value according to the key:
	 * - Keys `variation_id` and `quantity` are sanitized as positive integers using `absint()`.
	 * - All other keys (typically variation attributes like `color`, `size`) are sanitized as strings
	 *   using `sanitize_text_field()`.
	 *
	 * This function is intended to be used with array processing functions like `array_walk_recursive`.
	 *
	 * @since 1.0.0
	 *
	 * @param int|string &$value The value to sanitize. Passed by reference, so the original array is updated.
	 * @param int|string $key    The key of the array element being processed. Numeric for `variation_id`
	 *                           and `quantity`, and string for variation attributes.
	 *
	 * @return void
	 */
	public static function dro_pvvp_sanitize_posted_product_variations( int|string &$value, int|string $key ): void {

		switch ( $key ) {
			case 'variation_id':
				$value = absint( $value );
				break;
			case 'quantity':
				$value = absint( $value );
				break;
			default:
				$value = sanitize_text_field( $value );

		}
	}
}
