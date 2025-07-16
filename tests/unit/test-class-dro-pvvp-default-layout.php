<?php

/**
 * Unit tests for DRO_PVVP_Default_Layout class
 *
 * @package DRO_PVVP
 * @subpackage Tests
 * @group unit
 */

declare(strict_types=1);

use DRO\PVVP\Includes\Gallery\Layouts\DRO_PVVP_Default_Layout as Default_Layout;

class DRO_PVVP_Default_Layout_Test extends WP_UnitTestCase {



	/**
	 * Default Layout instance
	 *
	 * @var Default_Layout|null
	 */
	private ?Default_Layout $default_layout;

	/**
	 * Set up the test environment
	 *
	 * @return void
	 */
	public function set_up(): void {
		parent::set_up();
		$this->default_layout = new Default_Layout();
	}

	/**
	 * Tear Down the test environment
	 *
	 * @return void
	 */
	public function tear_down(): void {
		$this->default_layout = null;
		parent::tear_down();
	}

	/**
	 * Tests whether the Default Layout constructor correctly passes configuration
	 * values to the underlying builder instance.
	 *
	 * @covers \DRO\PVVP\Includes\Gallery\Layouts\DRO_PVVP_Default_Layout::__construct
	 *
	 * @return void
	 */
	public function test_constructor(): void {

		$config = array(
			'layout'           => 'custom-layout',
			'thumb_position'   => 'bottom',
			'thumb_size'       => 'small',
			'main_size'        => 'large',
			'slider_enabled'   => false,
			'lightbox_enabled' => true,
			'lazy_loading'     => false,
			'css_classes'      => array( 'custom-class' ),
			'data_attributes'  => array( 'custom' => 'value' ),
		);

		$default_layout = new Default_Layout( $config );

		$default_layout_reflection = new ReflectionClass( $default_layout );
		$builder_prop              = $default_layout_reflection->getProperty( 'builder' );
		$builder_prop->setAccessible( true );
		$default_builder = $builder_prop->getValue( $default_layout );

		$config_reflection = new ReflectionClass( $default_builder );
		$config_prop       = $config_reflection->getProperty( 'config' );
		$config_prop->setAccessible( true );
		$builder_config = $config_prop->getValue( $default_builder );

		// var_dump( $builder_config);

		$this->assertSame( 'custom-layout', $builder_config['layout'] );
		$this->assertSame( 'bottom', $builder_config['thumb_position'] );
		$this->assertSame( 'small', $builder_config['thumb_size'] );
		$this->assertSame( 'large', $builder_config['main_size'] );
		$this->assertFalse( $builder_config['slider_enabled'] );
		$this->assertTrue( $builder_config['lightbox_enabled'] );
		$this->assertFalse( $builder_config['lazy_loading'] );
		$this->assertContains( 'custom-class', $builder_config['css_classes'] );
		$this->assertArrayHasKey( 'custom', $builder_config['data_attributes'] );
		$this->assertSame( 'value', $builder_config['data_attributes']['custom'] );
	}
}
