<?php

/**
 * Plugin Name: Variation Carousel for WooCommerce
 * Plugin URI: https://github.com/younes-dro
 * Description: Display product variations in carousel animation and allow the customer to add several variations to the basket In one time.
 * Version: 1.0.0
 * Author: Younes DRO
 * Author URI: https://github.com/younes-dro
 * Text Domain: dro-vcw
 * Domain Path: /languages
 *
 * WC requires at least: 3.7.0
 * WC tested up to: 5.3.0
 *
 * Copyright: © 2020 Younes DRO
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 */



if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}





/**
 * DRO_Variation_Carousel class.
 *
 * The main instance of the plugin.
 *
 * @since 1.0.0
 */
class DRO_Variation_Carousel {

	/**
	 * The Single instance of the class.
	 *
	 * @var obj DRO_Variation_Carousel object
	 */
	protected static $instance;

	/**
	 * Plugin Version.
	 *
	 * @var String
	 */
	public $version = '1.0.0';

	/**
	 * Plugin Name
	 *
	 * @var String
	 */
	public $plugin_name = 'Variation Carousel for WooCommerce';
	/**
	 * Instance of the DRO_Variation_Carousel_Dependencies class.
	 *
	 * Verify the requirements
	 *
	 * @var obj DRO_Variation_Carousel_Dependencies object
	 */
	protected static $dependencies;

	/** @var array the admin notices to add */
	protected $notices = array();

	/**
	 *
	 * @param DRO_Variation_Carousel_Dependencies $dependencies
	 */
	public function __construct( DRO_Variation_Carousel_Dependencies $dependencies ) {

		self::$dependencies = $dependencies;

		register_activation_hook( __FILE__, array( $this, 'activation_check' ) );

		add_action( 'admin_init', array( $this, 'check_environment' ) );

		add_action( 'admin_init', array( $this, 'add_plugin_notices' ) );

		add_action( 'admin_notices', array( $this, 'admin_notices' ), 15 );

		add_action( 'plugins_loaded', array( $this, 'init_plugin' ) );

	}

	/**
	 * Gets the main DRO_Variation_Carousel instance.
	 *
	 * Ensures only one instance of DRO_Variation_Carousel is loaded or can be loaded.
	 *
	 * @since 1.0.0
	 * @return DRO_Variation_Carousel instance
	 */
	public static function start( DRO_Variation_Carousel_Dependencies $dependencies ) {
		if ( null === self::$instance ) {
			self::$instance = new self( $dependencies );
		}

		return self::$instance;
	}

	/**
	 * Cloning is forbidden due to singleton pattern.
	 *
	 * @since 1.0.0
	 */
	public function __clone() {
		$cloning_message = sprintf(
			esc_html__( 'You cannot clone instances of %s.', 'dro-vcw' ),
			get_class( $this )
		);
		_doing_it_wrong( __FUNCTION__, $cloning_message, $this->version );
	}

	/**
	 * Unserializing instances is forbidden due to singleton pattern.
	 *
	 * @since 1.0.0
	 */
	public function __wakeup() {
		$unserializing_message = sprintf(
			esc_html__( 'You cannot clone instances of %s.', 'dro-vcw' ),
			get_class( $this )
		);
				_doing_it_wrong( __FUNCTION__, $unserializing_message, $this->version );
	}

	/**
	 * Checks the server environment and deactivates plugins as necessary.
	 *
	 * @since 1.0.0
	 */
	public function activation_check() {

		if ( ! self::$dependencies->check_php_version() ) {

			$this->deactivate_plugin();

			wp_die( $this->plugin_name . esc_html__( ' could not be activated. ', 'dro-vcw' ) . self::$dependencies->get_php_notice() );

		}
	}

	/**
	 * Checks the environment on loading WordPress, just in case the environment changes after activation.
	 *
	 * @since 1.0.0
	 */
	public function check_environment() {

		if ( ! self::$dependencies->check_php_version() && is_plugin_active( plugin_basename( __FILE__ ) ) ) {

			$this->deactivate_plugin();
			$this->add_admin_notice(
				'bad_environment',
				'error',
				$this->plugin_name . esc_html__( ' has been deactivated. ', 'dro-vcw' ) . self::$dependencies->get_php_notice()
			);
		}
	}

	/**
	 * Deactivate the plugin
	 *
	 * @since 1.0.0
	 */
	protected function deactivate_plugin() {

		deactivate_plugins( plugin_basename( __FILE__ ) );

		if ( isset( $_GET['activate'] ) ) {
			unset( $_GET['activate'] );
		}
	}

	/**
	 * Adds an admin notice to be displayed.
	 *
	 * @since 1.0.0
	 *
	 * @param string $slug message slug
	 * @param string $class CSS classes
	 * @param string $message notice message
	 */
	public function add_admin_notice( $slug, $class, $message ) {

		$this->notices[ $slug ] = array(
			'class'   => $class,
			'message' => $message,
		);
	}

	public function add_plugin_notices() {

		if ( ! self::$dependencies->check_wp_version() ) {

			$this->add_admin_notice( 'update_wordpress', 'error', self::$dependencies->get_wp_notice() );
		}

		if ( ! self::$dependencies->check_wc_version() ) {

			 $this->add_admin_notice( 'update_woocommerce', 'error', self::$dependencies->get_wc_notice() );
		}
	}

	/**
	 * Displays any admin notices added with \DRO_Variation_Carousel::add_admin_notice()
	 *
	 * @since 1.0.0
	 */
	public function admin_notices() {

		foreach ( (array) $this->notices as $notice_key => $notice ) {

			echo "<div class='" . esc_attr( $notice['class'] ) . "'><p>";
			echo wp_kses( $notice['message'], array( 'a' => array( 'href' => array() ) ) );
			echo '</p></div>';
		}
	}

		/**
		 * Initializes the plugin.
		 *
		 * @since 1.0.0
		 */
	public function init_plugin() {

		if ( ! self::$dependencies->is_compatible() ) {
			return;
		}
		// Load the front end template
		add_action( 'after_setup_theme', array( $this, 'frontend_includes' ) );

		if ( ! is_admin() ) {
			new DRO_Variation_Carousel_Display();
		}
	}

	/**
	 * Include template functions and hooks.
	 */
	public function frontend_includes() {
		require_once 'includes/wc-cvp-template-functions.php';
		require_once 'includes/wc-cvp-template-hooks.php';
	}
	/*
	-----------------------------------------------------------------------------------*/
	/*
	  Helper Functions                                                                 */
	/*-----------------------------------------------------------------------------------*/

		/**
		 * Get the plugin url.
		 *
		 * @since 1.0.0
		 *
		 * @return string
		 */
	public function plugin_url() {
		return untrailingslashit( plugins_url( '/', __FILE__ ) );
	}

		/**
		 * Get the plugin path.
		 *
		 * @since 1.0.0
		 *
		 * @return string
		 */
	public function plugin_path() {
		return untrailingslashit( plugin_dir_path( __FILE__ ) );
	}

		/**
		 * Get the plugin base path name.
		 *
		 * @since 1.0.0
		 *
		 * @return string
		 */
	public function plugin_basename() {
		return plugin_basename( __FILE__ );
	}

		/**
		 * Register the built-in autoloader
		 *
		 * @codeCoverageIgnore
		 */
	public static function register_autoloader() {
		spl_autoload_register( array( 'DRO_Variation_Carousel', 'autoloader' ) );
	}

		/**
		 * Register autoloader.
		 *
		 * @param string $class_name Class name to load
		 */
	public static function autoloader( $class_name ) {

		$class = strtolower( str_replace( '_', '-', $class_name ) );
		$file  = plugin_dir_path( __FILE__ ) . '/includes/class-' . $class . '.php';
		if ( file_exists( $file ) ) {
			require_once $file;
		}
	}

}

/**
 * Returns the main instance of DRO_Variation_Carousel.
 */
function DRO_Variation_Carousel() {
	DRO_Variation_Carousel::register_autoloader();
	return DRO_Variation_Carousel::start( new DRO_Variation_Carousel_Dependencies() );
}

DRO_Variation_Carousel();
