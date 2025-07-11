<?php

/**
 * Unit tests for the DRO_PVVP_Gallery_Factory class.
 *
 * @package DRO_PVVP
 * @subpackage Tests
 * @group unit
 */

declare(strict_types=1);

use DRO\PVVP\Includes\Factories\DRO_PVVP_Gallery_Factory;
use DRO\PVVP\Includes\Interfaces\DRO_PVVP_Variation_Data_Provider_Interface;
use WC_Product;


/**
 * Unit tests for the DRO_PVVP_Gallery_Factory class.
 */
class DRO_PVVP_Gallery_Factory_Test extends WP_UnitTestCase {


	/**
	 * Gallery Factory instance
	 *
	 * @var DRO_PVVP_Gallery_Factory
	 */
	private ?DRO_PVVP_Gallery_Factory $gallery_factory;

	/**
	 * Set up the test environment.
	 *
	 * @return void
	 */
	public function setUp(): void {
		parent::setUp();
		$this->gallery_factory = DRO_PVVP_Gallery_Factory::get_instance();
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
	 * @return void
	 */
	public function test_get_instance(): void {
		$gallery_factory_1 = DRO_PVVP_Gallery_Factory::get_instance();
		$gallery_factory_2 = DRO_PVVP_Gallery_Factory::get_instance();

		$this->assertInstanceOf( DRO_PVVP_Gallery_Factory::class, $this->gallery_factory, 'Gallery Factory instance is not of the correct class.' );
		$this->assertSame( $gallery_factory_1, $this->gallery_factory, 'Gallery Factory instance is not the same as the one returned by get_instance() method.' );
		$this->assertSame( $gallery_factory_1, $gallery_factory_2, 'Multiple calls to get_instance() should return the same instance.' );
		$this->assertSame( $gallery_factory_1, $gallery_factory_2 );
	}

	/**
	 * Test the create_gallery_for_product method.
	 *
	 * @covers \DRO\PVVP\Includes\Factories\DRO_PVVP_Gallery_Factory::create_gallery_for_product
	 */
	public function test_create_gallery_for_product() {
		// Mock the data provider
		$data_provider_mock = $this->createMock( DRO_PVVP_Variation_Data_Provider_Interface::class );

		// Mock the product
		$product_mock = $this->createMock( WC_Product::class );
		$product_mock->method( 'is_type' )->with( 'variable' )->willReturn( true );

		
		// Case 1: Product is not a variable product (expect exception)
		$product_mock_not_variable = $this->createMock( WC_Product::class );
		$product_mock_not_variable->method( 'is_type' )->with( 'variable' )->willReturn( false );

		$factory = new DRO_PVVP_Gallery_Factory( $data_provider_mock );

		$this->expectException( \InvalidArgumentException::class );
		$this->expectExceptionMessage( 'Product must be a variable product.' );

		$factory->create_gallery_for_product( $product_mock_not_variable );

		// Case 2: No available variations
		$data_provider_mock->method( 'get_available_variations' )->willReturn( array() );
		$factory = new DRO_PVVP_Gallery_Factory( $data_provider_mock );
		$this->assertNull( $factory->create_gallery_for_product( $product_mock ) );

		// Case 3: Variations with no images
		$data_provider_mock->method( 'get_available_variations' )->willReturn( array( array( 'variation_id' => 1 ) ) );
		$data_provider_mock->method( 'get_variation_main_image' )->with( 1 )->willReturn( null );
		$data_provider_mock->method( 'get_variation_thumbs' )->with( 1 )->willReturn( array() );
		$factory = new DRO_PVVP_Gallery_Factory( $data_provider_mock );
		$this->assertNull( $factory->create_gallery_for_product( $product_mock ) );

		// Case 4: Variations with images
		$data_provider_mock->method( 'get_available_variations' )->willReturn( array( array( 'variation_id' => 1 ) ) );
		$data_provider_mock->method( 'get_variation_main_image' )->with( 1 )->willReturn( 'main_image.jpg' );
		$data_provider_mock->method( 'get_variation_thumbs' )->with( 1 )->willReturn( array( 'thumb1.jpg' ) );
		$factory = new DRO_PVVP_Gallery_Factory( $data_provider_mock );
		$this->assertIsString( $factory->create_gallery_for_product( $product_mock ) );
	}

	/**
	 * Test the create_gallery_for_variation method.
	 *
	 * @covers \DRO\PVVP\Includes\Factories\DRO_PVVP_Gallery_Factory::create_gallery_for_variation
	 */
	public function test_create_gallery_for_variation() {
		// Mock the data provider
		$data_provider_mock = $this->createMock( DRO_PVVP_Variation_Data_Provider_Interface::class );

		// Mock the product and variation
		$variation_mock = $this->createMock( WC_Product_Variation::class );
		$variation_mock->method( 'get_parent_id' )->willReturn( 1 );

		$product_mock = $this->createMock( WC_Product::class );

		// Mock wc_get_product
		if ( ! function_exists( 'wc_get_product' ) ) {
			function wc_get_product( $id ) {
				if ( $id === 1 ) {
					return $GLOBALS['product_mock'];
				}
				if ( $id === 123 ) {
					return $GLOBALS['variation_mock'];
				}
				return null;
			}
		}
		$GLOBALS['product_mock']   = $product_mock;
		$GLOBALS['variation_mock'] = $variation_mock;

		// Case 1: Invalid variation
		$data_provider_mock->method( 'is_valid_variation' )->with( 123 )->willReturn( false );
		$factory = new DRO_PVVP_Gallery_Factory( $data_provider_mock );
		$this->assertNull( $factory->create_gallery_for_variation( 123 ) );

		// Case 2: Valid variation
		$data_provider_mock->method( 'is_valid_variation' )->with( 123 )->willReturn( true );
		$data_provider_mock->method( 'get_variation_main_image' )->with( 123 )->willReturn( 'main_image.jpg' );
		$data_provider_mock->method( 'get_variation_thumbs' )->with( 123 )->willReturn( array( 'thumb1.jpg' ) );
		$factory = new DRO_PVVP_Gallery_Factory( $data_provider_mock );
		$this->assertIsString( $factory->create_gallery_for_variation( 123 ) );
	}
}
