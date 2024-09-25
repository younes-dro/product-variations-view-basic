<?php

require_once 'dro-wcvc.php';
require_once 'includes/class-variation-carousel-dependencies.php';

class DRO_Variation_CarouselTest extends WP_UnitTestCase {

	public function setUp() {

		parent::setUp();
		$this->class_instance = new DRO_Variation_Carousel( new DRO_Variation_Carousel_Dependencies() );
	}

	public function test_start() {

		$result = $this->class_instance->start( new DRO_Variation_Carousel_Dependencies() );
		$this->assertInstanceOf( 'DRO_Variation_Carousel', $result );

	}

	/**
	 * Test the dependencies property.
	 */
	public function test_dependencies() {

		$dependenciesProperty = new ReflectionProperty( 'DRO_Variation_Carousel', 'dependencies' );
		$dependenciesProperty->setAccessible( true );

		$this->assertInstanceOf( 'DRO_Variation_Carousel_Dependencies', $dependenciesProperty->getValue() );
	}

	/**
	 * Test the activation check function.
	 */
	public function test_activation_check() {

		$activateCheck = new ReflectionMethod( 'DRO_Variation_Carousel', 'activation_check' );
		$activateCheck->setAccessible( true );
		$activateCheck->invokeArgs( $this->class_instance, array( '' ) );

		// $this->expectException($result);
	}

	/**
	 * Test decativate plugin function.
	 */
	public function test_deactivate_plugin() {

		$deactivatePlugin = new ReflectionMethod( 'DRO_Variation_Carousel', 'deactivate_plugin' );
		$deactivatePlugin->setAccessible( true );
		$result = $deactivatePlugin->invoke( $this->class_instance );

		$this->assertTrue( $result );
	}
	public function test_clone() {

	}

}
