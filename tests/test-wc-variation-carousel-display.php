<?php

require_once '../woocommerce/woocommerce.php';
require_once 'includes/class-variation-carousel-dependencies.php';

class DRO_Variation_Carousel_DisplayTest extends WP_UnitTestCase {

	public function setUp() {
		parent::setUp();
		$this->class_instance = new DRO_Variation_Carousel_Display();
	}

	public function test_frontend_scripts() {

	}
}
