<?php
/**
 * Product Variations View Pro Dependencies
 *
 * This source file is subject to the GNU General Public License v3.0
 * that is bundled with this package in the file license.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.html
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to younesdro@gmail.com so we can send you a copy immediately.
 *
 * @package   Product Variations View Pro
 * @author    Younes DRO
 * @license   GPL-3.0+
 * @link      https://github.com/younes-dro/product-variations-view-pro
 * @version   1.0.0
 * @since     1.0.0
 */

namespace DRO\ProductVariationsViewPro\Includes;

use function DRO\ProductVariationsViewPro\product_variations_view_pro;
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Product_Variations_View_Pro_Dependencies class is responsible for checking the compatibility of the environment
 * in which the plugin is running, including PHP, WordPress, and WooCommerce versions.
 *
 * @class Product_Variations_View_Pro_Dependencies
 * @package Product Variations View Pro
 * @version 1.0.0
 * @since 1.0.0
 */
class Product_Variations_View_Pro_Dependencies {

	/** Minimum PHP version required by this plugin */
	const MINIMUM_PHP_VERSION = '7.4';

	/** Minimum WordPress version required by this plugin */
	const MINIMUM_WP_VERSION = '5.3.2';

	/** Minimum WooCommerce version required by this plugin */
	const MINIMUM_WC_VERSION = '3.7.0';

	/**
	 * Constructor for Product_Variations_View_Pro_Dependencies class.
	 * Initializes the dependency checks for PHP, WordPress, and WooCommerce versions.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {}

	/**
	 * Checks the PHP version for compatibility.
	 *
	 * This function compares the current PHP version with the minimum required version
	 * to ensure the plugin runs on a compatible environment.
	 *
	 * @return bool True if the PHP version is compatible, false otherwise.
	 * @since 1.0.0
	 */
	public static function check_php_version() {
		return version_compare( PHP_VERSION, self::MINIMUM_PHP_VERSION, '>=' );
	}

	/**
	 * Returns a notice message if the PHP version is not compatible.
	 *
	 * This message informs the user about the PHP version mismatch and suggests updating PHP.
	 *
	 * @return string The PHP version error message.
	 * @since 1.0.0
	 */
	public static function get_php_notice() {
		return sprintf(
			/* translators: %1$s is the minimum PHP version, %2$s is the current PHP version */
			esc_html__( 'The minimum PHP version required for this plugin is %1$s. You are running %2$s.', 'product-variations-view-pro' ),
			self::MINIMUM_PHP_VERSION,
			PHP_VERSION
		);
	}

	/**
	 * Checks the WordPress version for compatibility.
	 *
	 * This function compares the current WordPress version with the minimum required version
	 * to ensure the plugin functions correctly.
	 *
	 * @return bool True if the WordPress version is compatible, false otherwise.
	 * @since 1.0.0
	 */
	public static function check_wp_version() {
		if ( ! self::MINIMUM_WP_VERSION ) {
			return true;
		}
		return version_compare( get_bloginfo( 'version' ), self::MINIMUM_WP_VERSION, '>=' );
	}

	/**
	 * Returns a notice message if the WordPress version is not compatible.
	 *
	 * This message informs the user about the WordPress version mismatch and suggests updating WordPress.
	 *
	 * @return string The WordPress version error message.
	 * @since 1.0.0
	 */
	public static function get_wp_notice() {
		return sprintf(
			/* translators: %1$s is the plugin name, %2$s is the minimum required WordPress version, %3$s is the HTML for the "update WordPress" link, %4$s is the closing HTML tag for the link. */
			esc_html__( '%1$s is not active, as it requires WordPress version %2$s or higher. Please %3$supdate WordPress &raquo;%4$s', 'product-variations-view-pro' ),
			'<strong>' . Product_Variations_View_Pro()->plugin_name . '</strong>',
			self::MINIMUM_WP_VERSION,
			'<a href="' . esc_url( admin_url( 'update-core.php' ) ) . '">',
			'</a>'
		);
	}

	/**
	 * Checks the WooCommerce version for compatibility.
	 *
	 * This function compares the current WooCommerce version with the minimum required version
	 * to ensure the plugin runs on a compatible version of WooCommerce.
	 *
	 * @return bool True if the WooCommerce version is compatible, false otherwise.
	 * @since 1.0.0
	 */
	public static function check_wc_version() {
		if ( ! self::MINIMUM_WC_VERSION ) {
			return true;
		}
		return defined( 'WC_VERSION' ) && version_compare( WC_VERSION, self::MINIMUM_WC_VERSION, '>=' );
	}

	/**
	 * Returns a notice message if the WooCommerce version is not compatible.
	 *
	 * This message informs the user about the WooCommerce version mismatch and suggests updating WooCommerce.
	 *
	 * @return string The WooCommerce version error message.
	 * @since 1.0.0
	 */
	public function get_wc_notice() {
		return sprintf(
			/* translators: %1$s is the plugin name, %2$s is the minimum required WooCommerce version, %3$s is the HTML for the "update WooCommerce" link, %4$s is the closing HTML tag for the link. */
			esc_html__( '%1$s is not active, as it requires WooCommerce version %2$s or higher. Please %3$supdate WooCommerce &raquo;%4$s', 'product-variations-view-pro' ),
			'<strong>' . Product_Variations_View_Pro()->plugin_name . '</strong>',
			self::MINIMUM_WC_VERSION,
			'<a href="' . esc_url( admin_url( 'update-core.php' ) ) . '">',
			'</a>'
		);
	}
	/**
	 * Determines if all the requirements are met.
	 *
	 * @since 1.0.0
	 *
	 * @return bool True if all requirements are met, otherwise false.
	 */
	public function is_compatible() {
		return (
			self::check_php_version() &&
			self::check_wp_version() &&
			self::check_wc_version()
		);
	}
}
