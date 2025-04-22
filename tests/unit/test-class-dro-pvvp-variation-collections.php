<?php
/**
 * Unit tests for the DRO_PVVP_Variation_Collections class.
 *
 * This file tests the functionality of the DRO_PVVP_Variation_Collections class
 *
 * @package DRO_PVVP
 * @subpackage Tests
 * @group unit
 * @since 1.1.0
 */

declare(strict_types=1);

use DRO\PVVP\Includes\DRO_PVVP_Variation_Collections;

/**
 * Unit tests for the DRO_PVVP_Variation_Collections class.
 *
 * This file contains tests for various methods of the DRO_PVVP_Variation_Collections class,
 * including singleton instance creation, setting, and retrieving a product.
 *
 * @package DRO_PVVP
 * @subpackage Tests
 * @group unit
 * @since 1.1.0
 */
class DRO_PVVP_Variation_Collections_Test extends WP_UnitTestCase {

	/**
	 * Instance of the class being tested.
	 *
	 * This property holds the singleton instance of the
	 * DRO_PVVP_Variation_Collections class for use in tests.
	 *
	 * @var DRO_PVVP_Variation_Collections
	 * @since 1.1.0
	 */
	private $variation_collections;

	/**
	 * Prepares the test environment for each test method.
	 *
	 * Initializes the $variation_collections property by retrieving
	 * the singleton instance of the DRO_PVVP_Variation_Collections class
	 * and ensures the product is set to null to maintain a clean state.
	 *
	 * @return void
	 * @see \DRO\PVVP\Includes\DRO_PVVP_Variation_Collections::get_instance()
	 * @since 1.1.0
	 */
	public function setUp(): void {
		parent::setUp();
		$this->variation_collections = DRO_PVVP_Variation_Collections::get_instance();
		$this->variation_collections->set_product( null );
	}

	/**
	 * Cleans up the test environment after each test method.
	 *
	 * Resets the $variation_collections property to null to ensure there
	 * is no residual state between tests.
	 *
	 * @return void
	 * @since 1.1.0
	 */
	protected function tearDown(): void {
		parent::tearDown();
		$this->variation_collections = null;
	}

	/**
	 * Test the singleton instance creation.
	 *
	 * Ensures that:
	 * - The get_instance() method returns an instance of the correct class.
	 * - The same instance is returned across multiple calls to get_instance().
	 * - The instance matches the pre-initialized property in the test class.
	 *
	 * @covers \DRO\PVVP\Includes\DRO_PVVP_Variation_Collections::get_instance
	 * @since 1.1.0
	 * @return void
	 */
	public function test_get_instance() {
		$instance1 = DRO_PVVP_Variation_Collections::get_instance();
		$instance2 = DRO_PVVP_Variation_Collections::get_instance();

		$this->assertInstanceOf( DRO_PVVP_Variation_Collections::class, $instance1 );
		$this->assertSame( $instance1, $instance2, 'get_instance should return the same instance.' );
		$this->assertSame( $this->variation_collections, $instance1, 'get_instance should return the same instance.' );
	}

	/**
	 * Test the set_product method.
	 *
	 * Ensures that:
	 * - The provided WC_Product object is correctly set in the $product property.
	 * - The method returns the current instance of DRO_PVVP_Variation_Collections.
	 *
	 * @covers \DRO\PVVP\Includes\DRO_PVVP_Variation_Collections::set_product
	 * @since 1.1.0
	 * @return void
	 */
	public function test_set_product() {
		if ( ! class_exists( 'WC_Product' ) ) {
			eval( 'class WC_Product {}' );
		}

		/** @var WC_Product&PHPUnit\Framework\MockObject\MockObject $product_mock */
		$product_mock = $this->createMock( WC_Product::class );

		$result = $this->variation_collections->set_product( $product_mock );
		$this->assertInstanceOf(
			DRO_PVVP_Variation_Collections::class,
			$result,
			'set_product should return an instance of DRO_PVVP_Variation_Collections.'
		);
		$this->assertSame(
			$product_mock,
			$this->variation_collections->get_product(),
			'set_product should set the product.'
		);
	}

	/**
	 * Test the get_product method.
	 *
	 * Ensures that:
	 * - The method correctly retrieves the product set by set_product().
	 * - Returns the same WC_Product object that was previously set.
	 *
	 * @covers \DRO\PVVP\Includes\DRO_PVVP_Variation_Collections::get_product
	 * @since 1.1.0
	 * @return void
	 */
	public function test_get_product() {
		if ( ! class_exists( 'WC_Product' ) ) {
			eval( 'class WC_Product {}' );
		}

		/** @var WC_Product&PHPUnit\Framework\MockObject\MockObject $product_mock */
		$product_mock = $this->createMock( WC_Product::class );

		$this->variation_collections->set_product( $product_mock );
		$result = $this->variation_collections->get_product();
		$this->assertSame(
			$product_mock,
			$result,
			'get_product should return an instance of WC_Product.'
		);
	}

	/**
	 * Test get_product when no product is set.
	 *
	 * Ensures that get_product() returns null if no product has been set.
	 *
	 * @covers \DRO\PVVP\Includes\DRO_PVVP_Variation_Collections::get_product
	 * @since 1.1.0
	 * @return void
	 */
	public function test_get_product_when_no_product_is_set() {
		$result = $this->variation_collections->get_product();
		$this->assertNull(
			$result,
			'get_product should return null when no product is set.'
		);
	}
}
