<?php

declare(strict_types=1);

use DRO\PVVP\Includes\Providers\DRO_PVVP_Variation_Data_Provider;

class DRO_PVVP_Variation_Data_Provider_Test extends WP_UnitTestCase {

	/**
	 * Instance of the class being tested
	 *
	 * @var DRO_PVVP_Variation_Data_Provider|null
	 * @since 1.1.0
	 */
	private DRO_PVVP_Variation_Data_Provider $variation_data_provider;

	/**
	 * Prepares the test environment for each test method.
	 *
	 * @return void
	 * @see DRO\PVVP\Includes\Providers\DRO_PVVP_Variation_Data_Provider::get_instance()
	 */
	public function setUp(): void {
		parent::setUp();
		$this->variation_data_provider = DRO_PVVP_Variation_Data_Provider::get_instance();
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
		$instance1 = DRO_PVVP_Variation_Data_Provider::get_instance();
		$instance2 = DRO_PVVP_Variation_Data_Provider::get_instance();

		$this->assertInstanceOf( DRO_PVVP_Variation_Data_Provider::class, $this->variation_data_provider, 'get_instance should return the same instance.' );
		$this->assertSame( $this->variation_data_provider, $instance1, 'get_instance should return the same instance.' );
		$this->assertSame( $instance1, $instance2, 'get_instance should return the same instance.' );
	}
}
