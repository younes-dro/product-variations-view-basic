<?php
/**
 * Handles plugin initialization and main functionality.
 *
 * This class is responsible for initializing the plugin, checking dependencies,
 * loading the plugin textdomain, handling admin notices, and more.
 *
 * @package ProductVariationsViewPro
 * @author Younes DRO
 * @version 1.0.0
 * @since 1.0.0
 */

namespace DRO\ProductVariationsViewPro\Includes;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}


/**
 *
 * Main Product Variations View Pro class.
 *
 * @since 1.0.0
 */
class Product_Variations_View_Pro {

	/**
	 * The Single instance of the class.
	 *
	 * @var Product_Variations_View_Pro|null
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
	public $plugin_name = PRODUCT_VARIATIONS_VIEW_PRO_NAME;

	/**
	 * Instance of the Product_Variations_View_Pro_Dependencies class.
	 *
	 * Verify the requirements
	 *
	 * @var obj Product_Variations_View_Pro_Dependencies object
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
	 *
	 * @param Product_Variations_View_Pro_Dependencies $dependencies
	 */
	public function __construct( Product_Variations_View_Pro_Dependencies $dependencies ) {

		self::$dependencies = $dependencies;

		add_action( 'admin_init', array( $this, 'check_environment' ) );

		add_action( 'admin_init', array( $this, 'add_plugin_notices' ) );

		add_action( 'admin_notices', array( $this, 'admin_notices' ), 15 );

		add_action( 'plugins_loaded', array( $this, 'init_plugin' ) );

		add_action( 'init', array( $this, 'load_textdomain' ) );

		add_action( 'after_setup_theme', array( $this, 'frontend_includes' ) );
	}

	/**
	 * Gets the main Product_Variations_View_Pro instance.
	 *
	 * Ensures only one instance of Product_Variations_View_Pro is loaded or can be loaded.
	 *
	 * @since 1.0.0
	 * @return Product_Variations_View_Pro instance
	 */
	public static function start( Product_Variations_View_Pro_Dependencies $dependencies ): Product_Variations_View_Pro {

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

		if ( ! self::$dependencies->check_php_version() && is_plugin_active( plugin_basename( PRODUCT_VARIATIONS_VIEW_PRO_FILE ) ) ) {

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

		deactivate_plugins( plugin_basename( PRODUCT_VARIATIONS_VIEW_PRO_FILE ) );
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
	 * Displays any admin notices added with \Product_Variations_View_Pro::add_admin_notice()
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

			Product_Variations_View_Pro_Admin::start_admin();
		}
	}

	/**
	 * Include template functions and hooks.
	 */
	public function frontend_includes() {
		if ( $this->is_frontend_enabled() ) {
			new Product_Variations_View_Pro_Display();
			require_once INCLUDES_FOLDER . 'wc-cvp-template-functions.php';
			require_once INCLUDES_FOLDER . 'wc-cvp-template-hooks.php';
		}
	}
	/*
	-----------------------------------------------------------------------------------*/
	/*
		Helper Functions                                                                 */
	/*-----------------------------------------------------------------------------------*/
	/**
	 * Checks whether the frontend functionality is enabled.
	 *
	 * @return bool True if frontend functionality is enabled, false otherwise.
	 */
	private function is_frontend_enabled() {
		$is_enabled = get_option( 'pvv_is_enabled', true );
		return filter_var( $is_enabled, FILTER_VALIDATE_BOOLEAN );
	}

	/**
	 * Get the plugin url.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function plugin_url() {
		return untrailingslashit( plugins_url( '/', PRODUCT_VARIATIONS_VIEW_PRO_FILE ) );
	}

	/**
	 * Get the plugin path.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function plugin_path() {
		return untrailingslashit( plugin_dir_path( PRODUCT_VARIATIONS_VIEW_PRO_FILE ) );
	}

	/**
	 * Get the plugin base path name.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function plugin_basename() {
		return plugin_basename( PRODUCT_VARIATIONS_VIEW_PRO_FILE );
	}

	public function load_textdomain() {
		load_plugin_textdomain( 'product-variations-view-pro', false, $this->plugin_path() . '/languages' );
	}
}
