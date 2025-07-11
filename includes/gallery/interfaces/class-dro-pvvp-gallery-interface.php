<?php
/**
 * Interface for Gallery layout classes defining common builder methods.
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

interface DRO_PVVP_Gallery_Interface {
	public function render( WC_Product $product ): string;
}
