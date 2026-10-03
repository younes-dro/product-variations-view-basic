<?php
/**
 * Unit tests for the DRO_PVVP_Gallery_Factory class.
 *
 * @package DRO_PVVP
 * @subpackage Tests
 * @group unit
 */

declare(strict_types=1);

use DRO\PVVP\Includes\Gallery\Factories\DRO_PVVP_Gallery_Factory as Gallery_Factory;
use DRO\PVVP\Includes\Gallery\Layouts\DRO_PVVP_Default_Layout as Default_Layout;
use DRO\PVVP\Includes\Gallery\Interfaces\DRO_PVVP_Gallery_Interface as Gallery_Interface;


/**
 * Unit tests for the DRO_PVVP_Gallery_Factory class.
 *
 * @coversDefaultClass \DRO\PVVP\Includes\Factories\DRO_PVVP_Gallery_Factory
 */
class DRO_PVVP_Gallery_Factory_Test extends WP_UnitTestCase {


	/**
	 * Gallery Factory instance
	 *
	 * @var DRO_PVVP_Gallery_Factory
	 */
	private ?Gallery_Factory $gallery_factory;

	/**
	 * Set up the test environment.
	 *
	 * @return void
	 */
	public function setUp(): void {
		parent::setUp();
		$this->gallery_factory = Gallery_Factory::get_instance();
	}

	/**
	 * Tear down the test environment.
	 *
	 * @return void
	 */
	public function tearDown(): void {
		$this->gallery_factory = null;
		parent::tearDown();
	}

	/**
	 * Test the get_instance method of the Gallery Factory.
	 *
	 * @covers ::get_instance
	 * @return void
	 */
	public function test_get_instance(): void {
		$gallery_factory_1 = Gallery_Factory::get_instance();
		$gallery_factory_2 = Gallery_Factory::get_instance();

		$this->assertInstanceOf( Gallery_Factory::class, $this->gallery_factory, 'Gallery Factory instance is not of the correct class.' );
		$this->assertSame( $gallery_factory_1, $this->gallery_factory, 'Gallery Factory instance is not the same as the one returned by get_instance() method.' );
		$this->assertSame( $gallery_factory_1, $gallery_factory_2, 'Multiple calls to get_instance() should return the same instance.' );
		$this->assertSame( $gallery_factory_1, $gallery_factory_2 );
	}

	/**
	 * Test the create_gallery_layout method of the Gallery Factory.
	 *
	 * @covers ::create_gallery_layout
	 * @return void
	 */
	public function test_create_gallery_layout(): void {
		$layout_config = array( 'layout' => 'default' );

		$presumed_default_layout = $this->gallery_factory->create_gallery_layout( $layout_config );

		$this->assertInstanceOf( Default_Layout::class, $presumed_default_layout );
		$this->assertInstanceOf( Gallery_Interface::class, $presumed_default_layout );
	}
	/**
	 * Test the create_gallery_layout method with an unknown layout.
	 *
	 * @covers ::create_gallery_layout
	 * @return void
	 */
	public function test_create_gallery_layout_with_unknown_layout(): void {
		$this->expectException( \InvalidArgumentException::class );
		$this->expectExceptionMessage( 'Unknown gallery layout: unknown' );

		$this->gallery_factory->create_gallery_layout( array( 'layout' => 'unknown' ) );
	}

	/**
	 * Test the create_gallery_layout method with an empty layout value.
	 * It should fall back to the 'default' layout.
	 *
	 * @covers ::create_gallery_layout
	 * @return void
	 */
	public function test_create_gallery_layout_with_empty_layout(): void {
		$empty_layout_config     = array( 'layout' => '' );
		$expected_default_layout = $this->gallery_factory->create_gallery_layout( $empty_layout_config );

		$this->assertInstanceOf( Default_Layout::class, $expected_default_layout );
		$this->assertInstanceOf( Gallery_Interface::class, $expected_default_layout );
	}
}
