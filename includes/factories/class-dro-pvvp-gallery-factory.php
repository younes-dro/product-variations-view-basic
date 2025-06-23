<?php
/**
 * Gallery Factory Class
 *
 * @package DRO\PVVP\Includes\Factories
 */

namespace DRO\PVVP\Includes\Factories;

use DRO\PVVP\Includes\Interfaces\DRO_PVVP_Variation_Data_Provider_Interface;
use DRO\PVVP\Includes\Providers\DRO_PVVP_Variation_Data_Provider;
use DRO\PVVP\Includes\Gallery\DRO_PVVP_Gallery_Builder;
use WC_Product;

defined( 'ABSPATH' ) || exit;

class DRO_PVVP_Gallery_Factory {

	/**
	 * Singleton instance
	 *
	 * @var self|null
	 */
	private static ?self $instance = null;

	/**
	 * Variation data provider
	 *
	 * @var DRO_PVVP_Variation_Data_Provider_Interface
	 */
	private DRO_PVVP_Variation_Data_Provider_Interface $data_provider;

	/**
	 * Constructor
	 *
	 * @param DRO_PVVP_Variation_Data_Provider_Interface|null $data_provider Optional data provider
	 */
	public function __construct( ?DRO_PVVP_Variation_Data_Provider_Interface $data_provider = null ) {
		$this->data_provider = $data_provider ?: DRO_PVVP_Variation_Data_Provider::get_instance();
	}

	/**
	 * Get singleton instance
	 *
	 * @return self
	 */
	public static function get_instance(): self {
		return self::$instance ??= new self();
	}

	/**
	 * Create gallery for product using configuration
	 *
	 * @param WC_Product $product The WooCommerce product
	 * @param array      $config Gallery configuration
	 * @return string|null Gallery HTML or null if no variations
	 */
	public function create_gallery_for_product( WC_Product $product, array $config = array() ): ?string {
		if ( ! $product || ! $product->is_type( 'variable' ) ) {
			return null;
		}

		// Set product context in data provider
		$this->data_provider->set_product( $product );

		$available_variations = $this->data_provider->get_available_variations();

		if ( empty( $available_variations ) ) {
			return null;
		}

		$galleries = array();

		foreach ( $available_variations as $index => $variation ) {
			$variation_id = $variation['variation_id'];
			$is_active    = ( $index === 0 ); // First variation is active by default

			$main_image = $this->data_provider->get_variation_main_image( $variation_id );
			$thumbnails = $this->data_provider->get_variation_thumbs( $variation_id );

			// Skip if no images
			if ( empty( $main_image ) && empty( $thumbnails ) ) {
				continue;
			}

			// Create gallery using Builder pattern
			$gallery_html = $this->create_gallery_builder( $config )
				->set_variation_id( $variation_id )
				->set_main_image( $main_image )
				->set_thumbnails( $thumbnails )
				->set_active( $is_active )
				->add_css_class( 'variation-' . $variation_id )
				->build();

			$galleries[] = $gallery_html;
		}

		if ( empty( $galleries ) ) {
			return null;
		}

		// Wrap all galleries in container
		return $this->wrap_galleries( $galleries, $config );
	}

	/**
	 * Create gallery for specific variation (useful for AJAX)
	 *
	 * @param int   $variation_id Variation ID
	 * @param array $config Gallery configuration
	 * @return string|null Gallery HTML or null if variation not found
	 */
	public function create_gallery_for_variation( int $variation_id, array $config = array() ): ?string {
		if ( ! $this->data_provider->is_valid_variation( $variation_id ) ) {
			return null;
		}

		$variation      = wc_get_product( $variation_id );
		$parent_product = wc_get_product( $variation->get_parent_id() );

		if ( ! $parent_product ) {
			return null;
		}

		// Set product context
		$this->data_provider->set_product( $parent_product );

		$main_image = $this->data_provider->get_variation_main_image( $variation_id );
		$thumbnails = $this->data_provider->get_variation_thumbs( $variation_id );

		return $this->create_gallery_builder( $config )
			->set_variation_id( $variation_id )
			->set_main_image( $main_image )
			->set_thumbnails( $thumbnails )
			->set_active( true )
			->build();
	}

	/**
	 * Create gallery builder with configuration
	 *
	 * @param array $config Configuration array
	 * @return DRO_PVVP_Gallery_Builder
	 */
	private function create_gallery_builder( array $config ): DRO_PVVP_Gallery_Builder {
		$builder = new DRO_PVVP_Gallery_Builder();

		if ( isset( $config['layout'] ) ) {
			$builder->set_layout( $config['layout'] );
		}

		if ( isset( $config['thumb_position'] ) ) {
			$builder->set_thumb_position( $config['thumb_position'] );
		}

		if ( isset( $config['slider_enabled'] ) ) {
			$builder->enable_slider( $config['slider_enabled'] );
		}

		if ( isset( $config['lightbox_enabled'] ) ) {
			$builder->enable_lightbox( $config['lightbox_enabled'] );
		}

		if ( isset( $config['lazy_loading'] ) ) {
			$builder->enable_lazy_loading( $config['lazy_loading'] );
		}

		if ( isset( $config['thumb_size'] ) ) {
			$builder->set_thumb_size( $config['thumb_size'] );
		}

		if ( isset( $config['main_size'] ) ) {
			$builder->set_main_size( $config['main_size'] );
		}

		return $builder;
	}

	/**
	 * Wrap galleries in container
	 *
	 * @param array $galleries Array of gallery HTML strings
	 * @param array $config Configuration array
	 * @return string Wrapped galleries HTML
	 */
	private function wrap_galleries( array $galleries, array $config ): string {
		$css_classes = array(
			'dro-pvvp-gallery-container',
			'layout-' . ( $config['layout'] ?? 'default' ),
			'thumbs-' . ( $config['thumb_position'] ?? 'bottom' ),
		);

		if ( $config['slider_enabled'] ?? false ) {
			$css_classes[] = 'slider-enabled';
		}

		if ( $config['lightbox_enabled'] ?? false ) {
			$css_classes[] = 'lightbox-enabled';
		}

		$class_attr     = esc_attr( implode( ' ', $css_classes ) );
		$galleries_html = implode( '', $galleries );

		return sprintf(
			'<div class="%s" data-config="%s">%s</div>',
			$class_attr,
			esc_attr( json_encode( $config ) ),
			$galleries_html
		);
	}
}
