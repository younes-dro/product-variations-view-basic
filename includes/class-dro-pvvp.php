<?php
/**
 * Handles plugin initialization and main functionality.
 *
 * This class is responsible for initializing the plugin, checking dependencies,
 * loading the plugin textdomain, handling admin notices, and more.
 *
 * @package Product Variations View Pro
 * @author Younes DRO
 * @version 1.0.0
 * @since 1.0.0
 */

namespace DRO\PVVP\Includes;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}


/**
 *
 * Main Product Variations View Pro class.
 *
 * @since 1.0.0
 */
class DRO_PVVP {

	/**
	 * The Single instance of the class.
	 *
	 * @var DRO_PVVP|null
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
	public $plugin_name = DRO_PVVP_NAME;

	/**
	 * Instance of the DRO_PVVP_Dependencies class.
	 *
	 * Verify the requirements
	 *
	 * @var obj DRO_PVVP_Dependencies object
	 */
	protected static $dependencies;

	/**
	 * An array of admin notices to be displayed.
	 *
	 * This property stores the notices that need to be displayed in the WordPress admin.
	 * Each notice can be a string containing the HTML content or an array of parameters
	 * that define the type of notice (e.g., error, warning, success) and the message.
	 *
	 * @var array
	 */
	protected $notices = array();


	/**
	 * Constructor for the class.
	 *
	 * Initializes the class with required dependencies.
	 *
	 * @param DRO_PVVP_Dependencies $dependencies The dependencies required for the class to function.
	 */
	private function __construct( DRO_PVVP_Dependencies $dependencies ) {

		self::$dependencies = $dependencies;

		add_action( 'admin_init', array( $this, 'check_environment' ) );

		add_action( 'admin_init', array( $this, 'add_plugin_notices' ) );

		add_action( 'admin_notices', array( $this, 'admin_notices' ), 15 );

		add_action( 'plugins_loaded', array( $this, 'init_plugin' ) );

		add_action( 'after_setup_theme', array( $this, 'frontend_includes' ) );
	}

	/**
	 * Gets the main DRO_PVVP instance.
	 *
	 * Ensures only one instance of DRO_PVVP is loaded or can be loaded.
	 *
	 * @since 1.0.0
	 * @param DRO_PVVP_Dependencies $dependencies The dependencies required for the class to function.
	 * @return DRO_PVVP instance
	 */
	public static function start( DRO_PVVP_Dependencies $dependencies ): DRO_PVVP {

		self::$instance ??= new self( $dependencies );

		return self::$instance;
	}

	/**
	 * Cloning is forbidden due to singleton pattern.
	 *
	 * @since 1.0.0
	 */
	public function __clone() {
		$cloning_message = sprintf(
			/* translators: %s is the class name that cannot be cloned */
			esc_html__( 'You cannot clone instances of %s.', 'product-variations-view-pro' ),
			get_class( $this )
		);
		_doing_it_wrong( __FUNCTION__, esc_html( $cloning_message ), esc_html( $this->version ) );
	}

	/**
	 * Unserializing instances is forbidden due to singleton pattern.
	 *
	 * @since 1.0.0
	 */
	public function __wakeup() {
		$unserializing_message = sprintf(
			/* translators: %s is the class name that cannot be unserialized */
			esc_html__( 'You cannot clone instances of %s.', 'product-variations-view-pro' ),
			get_class( $this )
		);
		_doing_it_wrong( __FUNCTION__, esc_html( $unserializing_message ), esc_xml( $this->version ) );
	}


	/**
	 * Checks the environment on loading WordPress, just in case the environment changes after activation.
	 *
	 * @since 1.0.0
	 */
	public function check_environment() {

		if ( ! self::$dependencies->check_php_version() && is_plugin_active( plugin_basename( DRO_PVVP_FILE ) ) ) {

			$this->deactivate_plugin();
			$this->add_admin_notice(
				'bad_environment',
				'error',
				$this->plugin_name . esc_html__( ' has been deactivated. ', 'product-variations-view-pro' ) . self::$dependencies->get_php_notice()
			);
		}
	}

	/**
	 * Deactivate the plugin
	 *
	 * @since 1.0.0
	 */
	protected function deactivate_plugin() {

		deactivate_plugins( plugin_basename( DRO_PVVP_FILE ) );
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Nonce not needed as "activate" is used for display purposes only.
		if ( isset( $_GET['activate'] ) ) {
			unset( $_GET['activate'] );
		}
	}

	/**
	 * Adds an admin notice to be displayed.
	 *
	 * @since 1.0.0
	 *
	 * @param string $slug message slug.
	 * @param string $css_class CSS classes.
	 * @param string $message notice message.
	 */
	public function add_admin_notice( $slug, $css_class, $message ) {

		$this->notices[ $slug ] = array(
			'class'   => $css_class,
			'message' => $message,
		);
	}
	/**
	 * Adds plugin-related admin notices.
	 *
	 * Checks the WordPress and WooCommerce version compatibility.
	 * If the versions do not meet the plugin requirements, it adds admin notices
	 * to inform the user about necessary updates.
	 *
	 * @return void
	 */
	public function add_plugin_notices() {

		if ( ! self::$dependencies->check_wp_version() ) {

			$this->add_admin_notice( 'update_wordpress', 'error', self::$dependencies->get_wp_notice() );
		}

		if ( ! self::$dependencies->check_wc_version() ) {

			$this->add_admin_notice( 'update_woocommerce', 'error', self::$dependencies->get_wc_notice() );
		}
	}

	/**
	 * Displays any admin notices added with \DRO_PVVP::add_admin_notice()
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
		if ( is_admin() ) {

			DRO_PVVP_Admin::start_admin();
		}
	}

	/**
	 * Include template functions and hooks.
	 */
	public function frontend_includes() {
		if ( $this->is_frontend_enabled() ) {
			DRO_PVVP_Display::start_display();
			require_once DRO_PVVP_INCLUDES_FOLDER . 'dro-pvvp-template-functions.php';
			require_once DRO_PVVP_INCLUDES_FOLDER . 'dro-pvvp-template-hooks.php';
		}
	}

	/**
	 * Checks whether the frontend functionality is enabled.
	 *
	 * @return bool True if frontend functionality is enabled, false otherwise.
	 */
	private function is_frontend_enabled() {
		return (bool) filter_var( get_option( 'dro_pvvp_is_enabled', true ), FILTER_VALIDATE_BOOLEAN );
	}

	/**
	 * Get the plugin url.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function plugin_url() {
		return untrailingslashit( plugins_url( '/', DRO_PVVP_FILE ) );
	}

	/**
	 * Get the plugin path.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function plugin_path() {
		return untrailingslashit( plugin_dir_path( DRO_PVVP_FILE ) );
	}

	/**
	 * Get the plugin base path name.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function plugin_basename() {
		return plugin_basename( DRO_PVVP_FILE );
	}
}
