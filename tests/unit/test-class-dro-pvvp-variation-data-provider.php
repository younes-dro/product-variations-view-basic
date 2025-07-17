<?php
/**
 * Unit tests for the DRO_PVVP_Variation_Data_Provider class.
 *
 * @package DRO_PVVP
 * @subpackage Tests
 * @group unit
 */

declare(strict_types=1);

use DRO\PVVP\Includes\Providers\DRO_PVVP_Variation_Data_Provider as Provider;

/**
 * Unit tests for the DRO_PVVP_Variation_Data_Provider class.
 *
 * @coversDefaultClass DRO\PVVP\Includes\Providers\DRO_PVVP_Variation_Data_Provider
 */
class DRO_PVVP_Variation_Data_Provider_Test extends WP_UnitTestCase {

	/**
	 * Instance of the class being tested
	 *
	 * @var DRO_PVVP_Variation_Data_Provider|null
	 * @since 1.1.0
	 */
	private ?Provider $provider = null;

	/**
	 * Prepares the test environment for each test method.
	 *
	 * @return void
	 * @see DRO\PVVP\Includes\Providers\DRO_PVVP_Variation_Data_Provider::get_instance()
	 */
	public function setUp(): void {
		parent::setUp();
		$this->provider = Provider::get_instance();
	}

	/**
	 * Cleans up the test environment after each test method.
	 * Resets the variation_data_provider to null to ensure a clean state for subsequent tests.
	 *
	 * @return void
	 */
	public function tearDown(): void {
		parent::tearDown();
		$this->provider = null;
	}

	/**
	 * Tests the singleton instance retrieval of DRO_PVVP_Variation_Data_Provider.
	 *
	 * Ensures that:
	 * - The retrieved instance is of the correct class.
	 * - The instance retrieved via `get_instance()` is the same as the one set in setUp().
	 * - Multiple calls to `get_instance()` return the same object reference (singleton behavior).
	 *
	 * @return void
	 */
	public function test_get_instance(): void {
		$instance1 = Provider::get_instance();
		$instance2 = Provider::get_instance();

		$this->assertInstanceOf( Provider::class, $this->provider, 'get_instance should return the same instance.' );
		$this->assertSame( $this->provider, $instance1, 'get_instance should return the same instance.' );
		$this->assertSame( $instance1, $instance2, 'get_instance should return the same instance.' );
	}

	/**
	 * Test the set_product method.
	 *
	 * It should store the WC_Product instance and return the provider itself.
	 *
	 * @covers ::set_product
	 * @return void
	 */
	public function test_set_product(): void {
		$product = new WC_Product();

		$should_return_provider_object = $this->provider->set_product( $product );

		$provider_reflection = new ReflectionClass( $this->provider );
		$product_prop        = $provider_reflection->getProperty( 'product' );
		$product_prop->setAccessible( true );
		$product_prop_value = $product_prop->getValue( $this->provider );

		$this->assertInstanceOf( WC_Product::class, $product_prop_value );
		$this->assertInstanceOf( Provider::class, $should_return_provider_object );
	}
}
