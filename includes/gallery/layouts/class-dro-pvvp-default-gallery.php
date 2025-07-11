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
	 * Constructor.
	 * Initializes the builder.
	 */
	public function __construct() {
		$this->buidler = new Default_Builder();
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
			'1.1.0'
		);

		wp_register_script(
			'dro-pvvp-layout-default',
			plugins_url( 'assets/js/frontend/layout-default.js', DRO_PVVP_FILE ),
			array( 'jquery' ),
			'1.1.0',
			true
		);

		wp_enqueue_style( 'dro-pvvp-layout-default' );
		wp_enqueue_script( 'dro-pvvp-layout-default' );
	}
}
