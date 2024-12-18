<?php
/**
 * Plugin Name: Product Variations View Pro
 * Plugin URI: https://github.com/younes-dro/product-variations-view-pro
 * Description: Product Variation View Pro enhances WooCommerce variable products by displaying variations in an intuitive, carousel-style interface. It allows customers to add multiple product variations to the cart in a single action, streamlining the shopping experience.
 * Version: 1.0.0
 * Author: Younes DRO
 * Author URI: https://github.com/younes-dro
 * Text Domain: product-variations-view
 * Domain Path: /languages
 *
 * WC requires at least: 3.7.0
 * WC tested up to: 5.3.0
 *
 * Copyright: © 2024 Younes DRO
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace DRO\Pvv;

require __DIR__ . '/vendor/autoload.php';

use Inpsyde\Modularity\{Package, Properties};
use DRO\Pvv\Modules\Env\CheckEnvModule;
use DRO\Pvv\Modules\Env\CheckEnvService;
use DRO\Pvv\Modules\Utility\HelperModule;
use DRO\Pvv\Modules\Utility\HelperService;



if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

define( 'PVV_FILE', __FILE__ );
define( 'PVV_NAME', 'Product Variations View Pro' );
define( 'INCLUDES_FOLDER', untrailingslashit( plugin_dir_path( __FILE__ ) ) . '/includes/' );

/**
 * Checks the server environment and deactivates plugins as necessary.
 *
 * @since 1.0.0
 */
function activation_check() {
	$envChecker = new CheckEnvService();

	if ( ! $envChecker->check_php_version() ) {
		deactivate_plugins( plugin_basename( PVV_FILE ) );

		if ( isset( $_GET['activate'] ) ) {
			unset( $_GET['activate'] );
		}

		wp_die( esc_html__( PVV_NAME . ' could not be activated. ', 'product-variations-view' ) . $envChecker->get_php_notice() );
	}
}
register_activation_hook( PVV_FILE, __NAMESPACE__ . '\\activation_check' );



function initPackage(): Package {
	static $package;
	if ( ! $package ) {

		$properties = Properties\PluginProperties::new( __FILE__ );
		$package    = Package::new( $properties )
			->addModule( new CheckEnvModule() )
			->addModule( new HelperModule() );
	}
	return $package;
}

add_action(
	'plugins_loaded',
	function () {
		initPackage()->build();
		initPackage()->boot();
	}
);

/**
 * Returns the main instance of Pvv.
 */
function Pvv() {
	static $instance = null;

	if ( null === $instance ) {
		$container  = initPackage()->container();
		$envChecker = $container->get( CheckEnvService::class );
		$helper     = $container->get( HelperService::class );
		$instance   = Pvv::start( $envChecker, $helper );
	}

	return $instance;
}

add_action(
	'plugins_loaded',
	function () {
		Pvv();
	}
);
