<?php
/**
 * Default Gallery Layout Class
 *
 * Handles rendering of the default gallery layout and asset loading for
 * WooCommerce variable product image galleries.
 *
 * @package DRO\PVVP\Includes\Gallery\Layouts
 * @version 1.1.0
 * @since 1.1.0
 * @author Younes DRO
 * @license GPL-2.0-or-later
 */

declare(strict_types=1);

namespace DRO\PVVP\Includes\Gallery\Layouts;

use DRO\PVVP\Includes\Gallery\Interfaces\DRO_PVVP_Gallery_Interface as Gallery_Interface;
use DRO\PVVP\Includes\Gallery\Interfaces\DRO_PVVP_Layout_Assets_Interface as Layout_Assets_Interface;
use DRO\PVVP\Includes\Gallery\Builders\DRO_PVVP_Default_Builder as Default_Builder;
use DRO\PVVP\Includes\Providers\DRO_PVVP_Variation_Data_Provider;
use DRO\PVVP\Includes\Exceptions\DRO_PVVP_Invalid_Product_Exception as Invalid_Product_Exception;
use WC_Product;

/**
 * Class DRO_PVVP_Default_Layout
 *
 * Implements the default layout for displaying product variation galleries.
 */
class DRO_PVVP_Default_Layout implements Gallery_Interface, Layout_Assets_Interface {

	protected Default_Builder $builder;

	public function __construct( array $config = array() ) {
		$this->builder = new Default_Builder();

		if ( isset( $config['layout'] ) ) {
			$this->builder->set_layout( $config['layout'] );
		}
		if ( isset( $config['thumb_position'] ) ) {
			$this->builder->set_thumb_position( $config['thumb_position'] );
		}
		if ( isset( $config['thumb_size'] ) ) {
			$this->builder->set_thumb_size( $config['thumb_size'] );
		}
		if ( isset( $config['main_size'] ) ) {
			$this->builder->set_main_size( $config['main_size'] );
		}
		if ( isset( $config['slider_enabled'] ) ) {
			$this->builder->enable_slider( (bool) $config['slider_enabled'] );
		}
		if ( isset( $config['lightbox_enabled'] ) ) {
			$this->builder->enable_lightbox( (bool) $config['lightbox_enabled'] );
		}
		if ( isset( $config['lazy_loading'] ) ) {
			$this->builder->enable_lazy_loading( (bool) $config['lazy_loading'] );
		}
		if ( isset( $config['css_classes'] ) && is_array( $config['css_classes'] ) ) {
			foreach ( $config['css_classes'] as $class ) {
				$this->builder->add_css_class( $class );
			}
		}
		if ( isset( $config['data_attributes'] ) && is_array( $config['data_attributes'] ) ) {
			foreach ( $config['data_attributes'] as $key => $value ) {
				$this->builder->add_data_attribute( $key, $value );
			}
		}
	}

	public function render( WC_Product $product ): string {
		$this->enqueue_assets();

		$provider   = DRO_PVVP_Variation_Data_Provider::get_instance()->set_product( $product );
		$variations = array();

		try {
			$variations = $provider->get_available_variations();
		} catch ( Invalid_Product_Exception $e ) {
			error_log( sprintf( 'Gallery Render Error: %s | Product ID: %d', $e->getMessage(), $product->get_id() ) );
		}

		$output = '';

		foreach ( $variations as $index => $variation ) {
			$variation_id = (int) $variation['variation_id'];
			$main_image   = $provider->get_variation_main_image( $variation_id );
			$thumbnails   = $provider->get_variation_thumbs( $variation_id );

			$this->builder
				->reset()
				->set_variation_id( $variation_id )
				->set_main_image( array( $main_image ) )
				->set_thumbnails( $thumbnails )
				->set_active( $index === 0 );

			$output .= $this->builder->build( $product );
		}

		return sprintf( '<div class="dro-pvvp-gallery-layout-wrapper">%s</div>', $output );
	}

	public function enqueue_assets(): void {
		wp_register_style(
			'dro-pvvp-layout-default',
			plugins_url( 'assets/css/frontend/layout-default.css', DRO_PVVP_FILE ),
			array(),
			DRO_PVVP_VERSION
		);

		wp_register_script(
			'dro-pvvp-layout-default',
			plugins_url( 'assets/js/frontend/layout-default.js', DRO_PVVP_FILE ),
			array( 'jquery' ),
			DRO_PVVP_VERSION,
			true
		);

		wp_enqueue_style( 'dro-pvvp-layout-default' );
		wp_enqueue_script( 'dro-pvvp-layout-default' );
	}
}
