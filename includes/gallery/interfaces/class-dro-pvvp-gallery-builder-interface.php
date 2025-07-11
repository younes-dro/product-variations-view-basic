<?php
/**
 * Interface for Gallery Builder classes defining common builder methods.
 *
 * @author Younes DRO <younesdro@gmail.com>
 * @since 1.1.0
 * @version 1.1.0
 * @license GPL-2.0-or-later

 * @package DRO\PVVP\Includes\Interfaces
 */
declare(strict_types=1);

namespace DRO\PVVP\Includes\Gallery\Interfaces;

use WC_Product;

defined( 'ABSPATH' ) || exit;

interface DRO_PVVP_Gallery_Builder_Interface {

	/**
	 * Set gallery layout
	 *
	 * @param string $layout Layout type
	 * @return self
	 */
	public function set_layout( string $layout ): self;

	/**
	 * Set thumbnail position
	 *
	 * @param string $position Thumbnail position
	 * @return self
	 */
	public function set_thumb_position( string $position ): self;

	/**
	 * Set thumbnail size
	 *
	 * @param string $size Image size
	 * @return self
	 */
	public function set_thumb_size( string $size ): self;

	/**
	 * Set main image size
	 *
	 * @param string $size Image size
	 * @return self
	 */
	public function set_main_size( string $size ): self;

	/**
	 * Enable/disable slider
	 *
	 * @param bool $enable Enable slider
	 * @return self
	 */
	public function enable_slider( bool $enable = true ): self;

	/**
	 * Enable/disable lightbox
	 *
	 * @param bool $enable Enable lightbox
	 * @return self
	 */
	public function enable_lightbox( bool $enable = true ): self;

	/**
	 * Enable/disable lazy loading
	 *
	 * @param bool $enable Enable lazy loading
	 * @return self
	 */
	public function enable_lazy_loading( bool $enable = true ): self;

	/**
	 * Set variation ID
	 *
	 * @param int $variation_id Variation ID
	 * @return self
	 */
	public function set_variation_id( int $variation_id ): self;

	/**
	 * Set main image - handles both single image and array of images
	 *
	 * @param array|null $images Main images array or single image
	 * @return self
	 */
	public function set_main_image( ?array $images ): self;

	/**
	 * Set thumbnails
	 *
	 * @param array $thumbnails Thumbnails array
	 * @return self
	 */
	public function set_thumbnails( array $thumbnails ): self;

	/**
	 * Set active state
	 *
	 * @param bool $is_active Is active variation
	 * @return self
	 */
	public function set_active( bool $is_active ): self;

	/**
	 * Add CSS class
	 *
	 * @param string $class CSS class
	 * @return self
	 */
	public function add_css_class( string $class ): self;

	/**
	 * Add data attribute
	 *
	 * @param string $key Attribute key
	 * @param string $value Attribute value
	 * @return self
	 */
	public function add_data_attribute( string $key, string $value ): self;

	/**
	 * Build the gallery HTML
	 *
	 * @return string Gallery HTML
	 */
	public function build( WC_Product $product ): string;

	/**
	 * Reset builder for reuse
	 *
	 * @return self
	 */
	public function reset(): self;
}
