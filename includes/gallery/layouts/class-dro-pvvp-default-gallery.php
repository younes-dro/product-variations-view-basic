<?php

declare(strict_types=1);

namespace DRO\PVVP\Includes\Gallery\Layouts;

use DRO\PVVP\Includes\Gallery\Interfaces\DRO_PVVP_Gallery_Interface as Gallery_Interface;
use DRO\PVVP\Includes\Gallery\Builders\DRO_PVVP_Default_Builder as Default_Builder;
use WC_Product;

class DRO_PVVP_Default_Gallery implements Gallery_Interface {

	protected Default_Builder $buidler;
	public function __construct() {
		$this->buidler = new Default_Builder();
	}
	public function render( WC_Product $product ): string {
		return $this->buidler->build( $product );
	}
}
