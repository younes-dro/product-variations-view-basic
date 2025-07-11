<?php
/**
 * Gallery Default Builder Class
 *
 * @author Younes DRO <younesdro@gmail.com>
 * @since 1.1.0
 * @version 1.1.0
 * @license GPL-2.0-or-later
 * @package DRO\PVVP\Includes\Gallery
 */
declare(strict_types=1);

namespace DRO\PVVP\Includes\Gallery\Builders;

use DRO\PVVP\Includes\Gallery\Interfaces\DRO_PVVP_Gallery_Builder_Interface as Builder_Interface;
use DRO\PVVP\Includes\Providers\DRO_PVVP_Variation_Data_Provider as Variation_Data_Provider;
use DRO\PVVP\Includes\Gallery\Traits\DRO_PVVP_Image_Renderer_Trait;
use WC_Product;

defined( 'ABSPATH' ) || exit;

/**
 * Default Gallery Builder
 *
 * Constructs the default layout HTML for a WooCommerce variation gallery.
 * Implements the Builder pattern and allows fluent configuration.
 *
 * @package DRO\PVVP\Includes\Gallery
 * @author  Younes
 * @since   1.1.0
 * @version 1.1.0
 * @license GPL-2.0-or-later
 */
class DRO_PVVP_Default_Builder implements Builder_Interface {

	use DRO_PVVP_Image_Renderer_Trait;

	/**
	 * Variation data provider instance.
	 *
	 * @var Variation_Data_Provider
	 */
	private Variation_Data_Provider $variation_data_provider;

	/**
	 * Gallery configuration options.
	 *
	 * @var array
	 */

	private array $config = array(
		'layout'           => 'default',
		'thumb_position'   => 'left', // default
		'thumb_size'       => 'thumbnail',
		'main_size'        => 'large',
		'slider_enabled'   => true,
		'lightbox_enabled' => false,
		'lazy_loading'     => true,
		'css_classes'      => array(),
		'data_attributes'  => array(),
	);
	/**
	 * Gallery image and variation data.
	 *
	 * @var array
	 */
	private array $data = array(
		'variation_id' => 0,
		'main_image'   => array(),
		'thumbnails'   => array(),
		'is_active'    => false,
	);

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->variation_data_provider = new Variation_Data_Provider();
	}
	/** @inheritDoc */
	public function set_layout( string $layout ): self {

		$this->config['layout'] = 'default';
		return $this;
	}
	/** @inheritDoc */
	public function set_thumb_position( string $position ): self {
		$this->config['thumb_position'] = $position;
		return $this;
	}
	/** @inheritDoc */
	public function set_thumb_size( string $size ): self {
		$this->config['thumb_size'] = $size;
		return $this;
	}
	/** @inheritDoc */
	public function set_main_size( string $size ): self {
		$this->config['main_size'] = $size;
		return $this;
	}
	/** @inheritDoc */
	public function enable_slider( bool $enable = true ): self {
		$this->config['slider_enabled'] = $enable;
		return $this;
	}
	/** @inheritDoc */
	public function enable_lightbox( bool $enable = true ): self {
		$this->config['lightbox_enabled'] = $enable;
		return $this;
	}
	/** @inheritDoc */
	public function enable_lazy_loading( bool $enable = true ): self {
		$this->config['lazy_loading'] = $enable;
		return $this;
	}
	/** @inheritDoc */
	public function set_variation_id( int $variation_id ): self {
		$this->data['variation_id'] = $variation_id;
		return $this;
	}
	/** @inheritDoc */
	public function set_main_image( ?array $images ): self {
		$this->data['main_image'] = $images ?? array();
		return $this;
	}
	/** @inheritDoc */
	public function set_thumbnails( array $thumbnails ): self {
		$this->data['thumbnails'] = $thumbnails;
		return $this;
	}
	/** @inheritDoc */
	public function set_active( bool $is_active ): self {
		$this->data['is_active'] = $is_active;
		return $this;
	}
	/** @inheritDoc */
	public function add_css_class( string $class ): self {
		$this->config['css_classes'][] = $class;
		return $this;
	}
	/** @inheritDoc */
	public function add_data_attribute( string $key, string $value ): self {
		$this->config['data_attributes'][ $key ] = $value;
		return $this;
	}

	/** @inheritDoc */
	public function reset(): self {
		$this->data                      = array(
			'variation_id' => 0,
			'main_image'   => array(),
			'thumbnails'   => array(),
			'is_active'    => false,
		);
		$this->config['css_classes']     = array();
		$this->config['data_attributes'] = array();

		$this->config['layout']         = 'default';
		$this->config['thumb_position'] = 'left';
		return $this;
	}

	/** @inheritDoc */
	public function build( WC_Product $product ): string {

		var_dump( $this->data );

		if ( empty( $this->data['main_image'] ) && empty( $this->data['thumbnails'] ) ) {
			return '';
		}

		$main_image_html = $this->build_main_image_container();
		$thumbnails_html = $this->build_thumbnails_container();

		$css_classes     = $this->get_css_classes( 'dro-pvvp-gallery-default' );
		$data_attributes = $this->get_data_attributes();

		$thumb_position = $this->config['thumb_position'];

		if ( $thumb_position === 'left' ) {
			$content = sprintf(
				'<div class="dro-pvvp-thumbnails dro-pvvp-thumbnails-left">%s</div>
                 <div class="dro-pvvp-main-images">%s</div>',
				$thumbnails_html,
				$main_image_html
			);
		} else {
			$content = sprintf(
				'<div class="dro-pvvp-main-images">%s</div>
                 <div class="dro-pvvp-thumbnails dro-pvvp-thumbnails-right">%s</div>',
				$main_image_html,
				$thumbnails_html
			);
		}

		return sprintf(
			'<div class="%s"%s>%s</div>',
			esc_attr( $css_classes ),
			$data_attributes,
			$content
		);
	}

	/**
	 * Build the HTML for the main image container.
	 *
	 * @return string HTML output of the main image(s).
	 */
	private function build_main_image_container(): string {
		if ( empty( $this->data['main_image'] ) ) {
			return '';
		}

		$images_html = '';
		foreach ( $this->data['main_image'] as $index => $image ) {
			$active_class = ( $index === 0 ) ? ' active' : '';
			$images_html .= sprintf(
				'<div class="dro-pvvp-main-image%s" data-index="%d">%s</div>',
				$active_class,
				$index,
				$this->render_image(
					$image,
					$this->config['main_size'],
					$this->config['lightbox_enabled'],
					$this->config['lazy_loading'],
					$this->data['variation_id']
				)
			);
		}
		return $images_html;
	}

	/**
	 * Build the HTML for the thumbnail images container.
	 *
	 * @return string HTML output of the thumbnails.
	 */
	private function build_thumbnails_container(): string {
		if ( empty( $this->data['thumbnails'] ) ) {
			return '';
		}

		$thumbs_html = '';
		foreach ( $this->data['thumbnails'] as $index => $thumb ) {
			$active_class = ( $index === 0 ) ? ' active' : '';
			$thumbs_html .= sprintf(
				'<div class="dro-pvvp-thumb%s" data-index="%d">%s</div>',
				$active_class,
				$index,
				$this->render_image(
					$thumb,
					$this->config['thumb_size'],
					$this->config['lightbox_enabled'],
					$this->config['lazy_loading'],
					$this->data['variation_id']
				)
			);
		}

		return sprintf( '<div class="dro-pvvp-thumbs-wrapper">%s</div>', $thumbs_html );
	}

	/**
	 * Get the CSS classes string for the outer gallery wrapper.
	 *
	 * @param string $base_class Base CSS class name.
	 * @return string Concatenated CSS classes.
	 */
	private function get_css_classes( string $base_class ): string {
		$classes = array( $base_class );

		$classes[] = 'thumbs-' . $this->config['thumb_position'];

		if ( $this->config['slider_enabled'] ) {
			$classes[] = 'slider-enabled';
		}

		if ( $this->config['lightbox_enabled'] ) {
			$classes[] = 'lightbox-enabled';
		}

		if ( $this->data['is_active'] ) {
			$classes[] = 'active';
		}

		$classes = array_merge( $classes, $this->config['css_classes'] );

		return implode( ' ', array_filter( $classes ) );
	}

	/**
	 * Get HTML data attributes for the gallery wrapper.
	 *
	 * @return string HTML-formatted data attributes.
	 */
	private function get_data_attributes(): string {
		$attributes = array(
			'variation-id' => $this->data['variation_id'],
			'layout'       => $this->config['layout'],
			'slider'       => $this->config['slider_enabled'] ? 'true' : 'false',
			'lightbox'     => $this->config['lightbox_enabled'] ? 'true' : 'false',
		);

		$attributes = array_merge( $attributes, $this->config['data_attributes'] );

		$attr_string = '';
		foreach ( $attributes as $key => $value ) {
			$attr_string .= sprintf( ' data-%s="%s"', esc_attr( $key ), esc_attr( $value ) );
		}

		return $attr_string;
	}
}
