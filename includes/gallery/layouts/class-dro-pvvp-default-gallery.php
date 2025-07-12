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
use WC_Product;

/**
 * Class DRO_PVVP_Default_Gallery
 *
 * Implements the default layout for displaying product variation galleries.
 */
class DRO_PVVP_Default_Gallery implements Gallery_Interface, Layout_Assets_Interface {

	/**
	 * The builder instance responsible for rendering the HTML structure.
	 *
	 * @var Default_Builder
	 */
	protected Default_Builder $buidler;

	/**
	 * Constructor
	 *
	 * @param array $config Optional configuration to be applied to the builder.
	 */
	public function __construct( array $config = array() ) {
		
		$this->buidler = new Default_Builder();

		if ( isset( $config['layout'] ) ) {
			$this->buidler->set_layout( $config['layout'] );
		}
		if ( isset( $config['thumb_position'] ) ) {
			$this->buidler->set_thumb_position( $config['thumb_position'] );
		}
		if ( isset( $config['thumb_size'] ) ) {
			$this->buidler->set_thumb_size( $config['thumb_size'] );
		}
		if ( isset( $config['main_size'] ) ) {
			$this->buidler->set_main_size( $config['main_size'] );
		}
		if ( isset( $config['slider_enabled'] ) ) {
			$this->buidler->enable_slider( (bool) $config['slider_enabled'] );
		}
		if ( isset( $config['lightbox_enabled'] ) ) {
			$this->buidler->enable_lightbox( (bool) $config['lightbox_enabled'] );
		}
		if ( isset( $config['lazy_loading'] ) ) {
			$this->buidler->enable_lazy_loading( (bool) $config['lazy_loading'] );
		}
		if ( isset( $config['css_classes'] ) && is_array( $config['css_classes'] ) ) {
			foreach ( $config['css_classes'] as $class ) {
				$this->buidler->add_css_class( $class );
			}
		}
		if ( isset( $config['data_attributes'] ) && is_array( $config['data_attributes'] ) ) {
			foreach ( $config['data_attributes'] as $key => $value ) {
				$this->buidler->add_data_attribute( $key, $value );
			}
		}
	}

	/**
	 * Render the gallery HTML for a given product.
	 *
	 * @param WC_Product $product WooCommerce product object.
	 * @return string Rendered HTML of the gallery.
	 */
	public function render( WC_Product $product ): string {
		$this->enqueue_assets();
		return $this->buidler->build( $product );
	}

	/**
	 * Enqueue CSS/JS specific to this layout.
	 * Called automatically before rendering.
	 */
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
