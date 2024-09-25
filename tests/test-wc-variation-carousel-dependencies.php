<?php
require_once '../woocommerce/woocommerce.php';
require_once 'includes/class-variation-carousel-dependencies.php';

class DRO_Variation_Carousel_DependenciesTest extends WP_UnitTestCase {

	public function setUp() {
		parent::setUp();
		$this->class_instance = new DRO_Variation_Carousel_Dependencies();
	}


	public function test_check_php_version() {
		$result = $this->class_instance->check_php_version();
		$this->assertTrue( $result );
	}

	public function test_check_wp_version() {
		$reslut = $this->class_instance->check_wp_version();
		$this->assertTrue( $reslut );
	}

	public function test_check_wc_version() {

		$result = $this->class_instance->check_wc_version();
		$this->assertTrue( $result );
	}

}
