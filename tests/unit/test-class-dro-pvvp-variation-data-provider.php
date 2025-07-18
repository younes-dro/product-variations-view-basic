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

	/**
	 * Test that get_available_variations() returns a list of variation data arrays
	 * for a valid variable product, or throws an Invalid_Product_Exception
	 * if the product is not set or invalid.
	 *
	 * @covers ::get_available_variations
	 *
	 * @return void
	 * @throws \DRO\PVVP\Includes\Exceptions\DRO_PVVP_Invalid_Product_Exception
	 */
	public function test_get_available_variations(): void {
		// Create a variable product
		$product_id_test = wp_insert_post(
			array(
				'post_title'  => 'Test Variable Product',
				'post_status' => 'publish',
				'post_type'   => 'product',
			)
		);

		$product_variable_test = new WC_Product_Variable( $product_id_test );

		// Set up attribute for variations (e.g., 'pa_color')
		$attribute = new WC_Product_Attribute();
		$attribute->set_name( 'pa_color' );
		$attribute->set_options( array( 'red' ) );
		$attribute->set_visible( true );
		$attribute->set_variation( true );

		$product_variable_test->set_attributes( array( $attribute ) );
		$product_variable_test->save();

		// Create two variations
		$variation_id_test_1 = wp_insert_post(
			array(
				'post_parent' => $product_id_test,
				'post_status' => 'publish',
				'post_type'   => 'product_variation',
			)
		);
		$variation_id_test_2 = wp_insert_post(
			array(
				'post_parent' => $product_id_test,
				'post_status' => 'publish',
				'post_type'   => 'product_variation',
			)
		);

		$variation_test_1 = new WC_Product_Variation( $variation_id_test_1 );
		$variation_test_1->set_attributes( array( 'pa_color' => 'red' ) );
		$variation_test_1->set_regular_price( '9.99' );
		$variation_test_1->save();

		$variation_test_2 = new WC_Product_Variation( $variation_id_test_2 );
		$variation_test_2->set_attributes( array( 'pa_color' => 'red' ) );
		$variation_test_2->set_regular_price( '14.99' );
		$variation_test_2->save();

		// Assign the product to the provider and test
		$this->provider->set_product( $product_variable_test );
		$available_variations = $this->provider->get_available_variations();

		// Assert the structure of returned variation data
		$this->assertIsArray( $available_variations );
		$this->assertNotEmpty( $available_variations );
		$this->assertIsArray( $available_variations[0] );
		$this->assertArrayHasKey( 'attributes', $available_variations[0] );
		$this->assertArrayHasKey( 'display_price', $available_variations[0] );
	}
}
