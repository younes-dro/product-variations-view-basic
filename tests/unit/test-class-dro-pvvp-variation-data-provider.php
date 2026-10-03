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

	protected int $product_id;
	protected WC_Product_Variable $product;

	/**
	 * Prepares the test environment for each test method.
	 *
	 * @return void
	 */
	public function setUp(): void {
		parent::setUp();
		$this->provider = Provider::get_instance();

		// Register 'pa_color' taxonomy
		if ( ! taxonomy_exists( 'pa_color' ) ) {
			register_taxonomy(
				'pa_color',
				'product_variation',
				array(
					'hierarchical' => false,
					'label'        => 'Color',
					'show_ui'      => false,
					'query_var'    => true,
					'rewrite'      => false,
				)
			);
		}

		// Insert term 'red' into 'pa_color'
		$term = term_exists( 'red', 'pa_color' );
		if ( ! $term ) {
			$term = wp_insert_term( 'red', 'pa_color' );
		}
		$term_id = is_array( $term ) ? $term['term_id'] : $term;

		// Create variable product
		$this->product_id = wp_insert_post(
			array(
				'post_title'  => 'Test Variable Product',
				'post_status' => 'publish',
				'post_type'   => 'product',
			)
		);
		$this->product = new WC_Product_Variable( $this->product_id );

		// Add attribute to product
		$attribute = new WC_Product_Attribute();
		$attribute->set_id( 0 );
		$attribute->set_name( 'pa_color' );
		$attribute->set_options( array( 'red' ) );
		$attribute->set_visible( true );
		$attribute->set_variation( true );
		$this->product->set_attributes( array( $attribute ) );
		wp_set_object_terms( $this->product_id, 'red', 'pa_color' );
		$this->product->save();

		// Create variation
		$variation_id = wp_insert_post(
			array(
				'post_title'  => 'Variation #1',
				'post_status' => 'publish',
				'post_parent' => $this->product_id,
				'post_type'   => 'product_variation',
			)
		);

		$variation = new WC_Product_Variation( $variation_id );
		$variation->set_regular_price( '9.99' );
		$variation->set_attributes( array( 'pa_color' => 'red' ) );
		$variation->save();

		// Refresh product
		$this->product = wc_get_product( $this->product_id );
		$this->provider->set_product( $this->product );
	}

	/**
	 * Cleans up the test environment after each test method.
	 *
	 * @return void
	 */
	public function tearDown(): void {
		wp_delete_post( $this->product_id, true );
		$this->provider = null;
		parent::tearDown();
	}

	public function test_get_instance(): void {
		$instance1 = Provider::get_instance();
		$instance2 = Provider::get_instance();

		$this->assertInstanceOf( Provider::class, $this->provider );
		$this->assertSame( $this->provider, $instance1 );
		$this->assertSame( $instance1, $instance2 );
	}

	public function test_set_product(): void {
		$product                       = new WC_Product();
		$should_return_provider_object = $this->provider->set_product( $product );

		$provider_reflection = new ReflectionClass( $this->provider );
		$product_prop        = $provider_reflection->getProperty( 'product' );
		$product_prop->setAccessible( true );
		$product_prop_value = $product_prop->getValue( $this->provider );

		$this->assertInstanceOf( WC_Product::class, $product_prop_value );
		$this->assertInstanceOf( Provider::class, $should_return_provider_object );
	}

	public function test_get_available_variations(): void {
		$available_variations = $this->provider->get_available_variations();

		$this->assertIsArray( $available_variations );
		$this->assertNotEmpty( $available_variations, 'Expected at least one available variation.' );
		$this->assertIsArray( $available_variations[0] );
		$this->assertArrayHasKey( 'attributes', $available_variations[0] );
		$this->assertArrayHasKey( 'display_price', $available_variations[0] );
	}
}
