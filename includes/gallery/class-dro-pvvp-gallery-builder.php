<?php
/**
 * Gallery Builder Class
 *
 * @package DRO\PVVP\Includes\Gallery
 */

namespace DRO\PVVP\Includes\Gallery;

defined( 'ABSPATH' ) || exit;

class DRO_PVVP_Gallery_Builder {

	/**
	 * Gallery configuration
	 *
	 * @var array
	 */
	private array $config = [
		'layout'          => 'default', // default, sidebar, bottom, grid
		'thumb_position'  => 'bottom',  // bottom, left, right, top
		'thumb_size'      => 'thumbnail',
		'main_size'       => 'large',
		'slider_enabled'  => true,
		'lightbox_enabled'=> false,
		'lazy_loading'    => true,
		'css_classes'     => [],
		'data_attributes' => []
	];

	/**
	 * Gallery data
	 *
	 * @var array
	 */
	private array $data = [
		'variation_id' => 0,
		'main_image'  => [],
		'thumbnails'   => [],
		'is_active'    => false
	];

	/**
	 * Set gallery layout
	 *
	 * @param string $layout Layout type
	 * @return self
	 */
	public function set_layout( string $layout ): self {
		$this->config['layout'] = $layout;
		return $this;
	}

	/**
	 * Set thumbnail position
	 *
	 * @param string $position Thumbnail position
	 * @return self
	 */
	public function set_thumb_position( string $position ): self {
		$this->config['thumb_position'] = $position;
		return $this;
	}

	/**
	 * Set thumbnail size
	 *
	 * @param string $size Image size
	 * @return self
	 */
	public function set_thumb_size( string $size ): self {
		$this->config['thumb_size'] = $size;
		return $this;
	}

	/**
	 * Set main image size
	 *
	 * @param string $size Image size
	 * @return self
	 */
	public function set_main_size( string $size ): self {
		$this->config['main_size'] = $size;
		return $this;
	}

	/**
	 * Enable/disable slider
	 *
	 * @param bool $enable Enable slider
	 * @return self
	 */
	public function enable_slider( bool $enable = true ): self {
		$this->config['slider_enabled'] = $enable;
		return $this;
	}

	/**
	 * Enable/disable lightbox
	 *
	 * @param bool $enable Enable lightbox
	 * @return self
	 */
	public function enable_lightbox( bool $enable = true ): self {
		$this->config['lightbox_enabled'] = $enable;
		return $this;
	}

	/**
	 * Enable/disable lazy loading
	 *
	 * @param bool $enable Enable lazy loading
	 * @return self
	 */
	public function enable_lazy_loading( bool $enable = true ): self {
		$this->config['lazy_loading'] = $enable;
		return $this;
	}

	/**
	 * Set variation ID
	 *
	 * @param int $variation_id Variation ID
	 * @return self
	 */
	public function set_variation_id( int $variation_id ): self {
		$this->data['variation_id'] = $variation_id;
		return $this;
	}

	/**
	 * Set main images
	 *
	 * @param array $images Main images array
	 * @return self
	 */
	public function set_main_image( array $images ): self {
		$this->data['main_image'] = $images;
		return $this;
	}

	/**
	 * Set thumbnails
	 *
	 * @param array $thumbnails Thumbnails array
	 * @return self
	 */
	public function set_thumbnails( array $thumbnails ): self {
		$this->data['thumbnails'] = $thumbnails;
		return $this;
	}

	/**
	 * Set active state
	 *
	 * @param bool $is_active Is active variation
	 * @return self
	 */
	public function set_active( bool $is_active ): self {
		$this->data['is_active'] = $is_active;
		return $this;
	}

	/**
	 * Add CSS class
	 *
	 * @param string $class CSS class
	 * @return self
	 */
	public function add_css_class( string $class ): self {
		$this->config['css_classes'][] = $class;
		return $this;
	}

	/**
	 * Add data attribute
	 *
	 * @param string $key Attribute key
	 * @param string $value Attribute value
	 * @return self
	 */
	public function add_data_attribute( string $key, string $value ): self {
		$this->config['data_attributes'][$key] = $value;
		return $this;
	}

	/**
	 * Build the gallery HTML
	 *
	 * @return string Gallery HTML
	 */
	public function build(): string {
	
		if ( empty( $this->data['main_image'] ) && empty( $this->data['thumbnails'] ) ) {
			return '';
		}

		// Build gallery based on layout
		switch ( $this->config['layout'] ) {
			case 'sidebar':
				return $this->build_sidebar_layout();
			case 'bottom':
				return $this->build_bottom_layout();
			case 'grid':
				return $this->build_grid_layout();
			default:
				return $this->build_default_layout();
		}
	}

	/**
	 * Build default layout
	 *
	 * @return string HTML
	 */
	private function build_default_layout(): string {
		$main_image_html = $this->build_main_image_container();
		$thumbnails_html = $this->build_thumbnails_container();

		$css_classes = $this->get_css_classes( 'dro-pvvp-gallery-default' );
		$data_attributes = $this->get_data_attributes();

		return sprintf(
			'<div class="%s"%s>
				<div class="dro-pvvp-main-images">%s</div>
				<div class="dro-pvvp-thumbnails">%s</div>
			</div>',
			esc_attr( $css_classes ),
			$data_attributes,
			$main_image_html,
			$thumbnails_html
		);
	}

	/**
	 * Build sidebar layout
	 *
	 * @return string HTML
	 */
	private function build_sidebar_layout(): string {
		$main_image_html = $this->build_main_image_container();
		$thumbnails_html = $this->build_thumbnails_container();

		$css_classes = $this->get_css_classes( 'dro-pvvp-gallery-sidebar' );
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
	 * Build bottom layout
	 *
	 * @return string HTML
	 */
	private function build_bottom_layout(): string {
		return $this->build_default_layout(); // Same as default for now
	}

	/**
	 * Build grid layout
	 *
	 * @return string HTML
	 */
	private function build_grid_layout(): string {
		$all_images = array_merge( $this->data['main_image'], $this->data['thumbnails'] );
		$images_html = '';

		foreach ( $all_images as $index => $image ) {
			$images_html .= sprintf(
				'<div class="dro-pvvp-grid-item" data-index="%d">%s</div>',
				$index,
				$this->process_image( $image, $this->config['main_size'] )
			);
		}

		$css_classes = $this->get_css_classes( 'dro-pvvp-gallery-grid' );
		$data_attributes = $this->get_data_attributes();

		return sprintf(
			'<div class="%s"%s>
				<div class="dro-pvvp-grid-container">%s</div>
			</div>',
			esc_attr( $css_classes ),
			$data_attributes,
			$images_html
		);
	}

	/**
	 * Build main image container
	 *
	 * @return string HTML
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
				$this->process_image( $image, $this->config['main_size'] )
			);
		}

		return $images_html;
	}

	/**
	 * Build thumbnails container
	 *
	 * @return string HTML
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
				$this->process_image( $thumb, $this->config['thumb_size'] )
			);
		}

		return sprintf(
			'<div class="dro-pvvp-thumbs-wrapper">%s</div>',
			$thumbs_html
		);
	}

	/**
	 * Process image for output
	 *
	 * @param array|string $image Image data
	 * @param string $size Image size
	 * @return string Processed image HTML
	 */
	private function process_image( $image, string $size ): string {
		// Handle different image formats
		if ( is_array( $image ) ) {
			$image_id = $image['id'] ?? 0;
			$image_url = $image['url'] ?? '';
			$image_alt = $image['alt'] ?? '';
		} else {
			$image_url = $image;
			$image_id = attachment_url_to_postid( $image_url );
			$image_alt = get_post_meta( $image_id, '_wp_attachment_image_alt', true );
		}

		// Generate image HTML
		if ( $image_id ) {
			$image_html = wp_get_attachment_image( $image_id, $size, false, [
				'class' => 'dro-pvvp-image',
				'alt'   => $image_alt,
				'loading' => $this->config['lazy_loading'] ? 'lazy' : 'eager'
			]);
		} else {
			$image_html = sprintf(
				'<img src="%s" alt="%s" class="dro-pvvp-image" loading="%s">',
				esc_url( $image_url ),
				esc_attr( $image_alt ),
				$this->config['lazy_loading'] ? 'lazy' : 'eager'
			);
		}

		// Add lightbox wrapper if enabled
		if ( $this->config['lightbox_enabled'] ) {
			$full_size_url = $image_id ? wp_get_attachment_image_url( $image_id, 'full' ) : $image_url;
			$image_html = sprintf(
				'<a href="%s" class="dro-pvvp-lightbox" data-lightbox="gallery-%d">%s</a>',
				esc_url( $full_size_url ),
				$this->data['variation_id'],
				$image_html
			);
		}

		return $image_html;
	}

	/**
	 * Get CSS classes string
	 *
	 * @param string $base_class Base CSS class
	 * @return string CSS classes
	 */
	private function get_css_classes( string $base_class ): string {
		$classes = [ $base_class ];

		// Add configuration classes
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

		// Add custom classes
		$classes = array_merge( $classes, $this->config['css_classes'] );

		return implode( ' ', array_filter( $classes ) );
	}

	/**
	 * Get data attributes string
	 *
	 * @return string Data attributes
	 */
	private function get_data_attributes(): string {
		$attributes = [
			'variation-id' => $this->data['variation_id'],
			'layout' => $this->config['layout'],
			'slider' => $this->config['slider_enabled'] ? 'true' : 'false',
			'lightbox' => $this->config['lightbox_enabled'] ? 'true' : 'false'
		];

		// Add custom data attributes
		$attributes = array_merge( $attributes, $this->config['data_attributes'] );

		$attr_string = '';
		foreach ( $attributes as $key => $value ) {
			$attr_string .= sprintf( ' data-%s="%s"', esc_attr( $key ), esc_attr( $value ) );
		}

		return $attr_string;
	}
}