<?php
/**
 * Handles plugin initialization and main functionality.
 *
 * @package ProductVariationsViewPro
 * @author Younes DRO
 * @version 1.0.0
 * @since 1.0.0
 */

namespace DRO\Pvv;

use DRO\Pvv\Modules\Env\CheckEnvService;
use DRO\Pvv\Modules\Utility\HelperService;
use DRO\Pvv\PvvAdmin;
use DRO\Pvv\PvvFront;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}


/**
 *
 * Main Product Variations View Pro class.
 *
 * @since 1.0.0
 */
class Pvv {

	/**
	 * The Single instance of the class.
	 *
	 * @var Pvv|null
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
	public $plugin_name = PVV_NAME;

	/**
	 * Instance of the CheckEnvService class.
	 *
	 * Verify the requirements.
	 *
	 * @var obj CheckEnvService object
	 */
	private $envChecker;

	/**
	 * Instance of the HelperService class.
	 *
	 * @var obj HelperService object
	 */
	private $helper;

	/** @var array the admin notices to add */
	protected $notices = array();

	/**
	 *
	 * @param CheckEnvService $envChecker
	 */
	public function __construct( CheckEnvService $envChecker, HelperService $helper ) {

		$this->envChecker = $envChecker;
		$this->helper     = $helper;
	}

	/**
	 * Gets the main Pvv instance.
	 *
	 * Ensures only one instance of Pvv is loaded or can be loaded.
	 *
	 * @since 1.0.0
	 * @return Pvv instance
	 */
	public static function start( CheckEnvService $envChecker, HelperService $helper ): Pvv {
		if ( null === self::$instance ) {
			self::$instance = new self( $envChecker, $helper );

			self::$instance->register_hooks();
		}

		return self::$instance;
	}

	private function register_hooks() {
		add_action( 'init', array( $this, 'init_plugin' ) );
		add_action( 'init', array( $this, 'load_textdomain' ) );
		add_action( 'after_setup_theme', array( $this, 'frontend_includes' ) );
		add_action( 'admin_init', array( $this, 'check_environment' ) );
		add_action( 'admin_init', array( $this, 'add_plugin_notices' ) );
		add_action( 'admin_notices', array( $this, 'admin_notices' ), 15 );
	}


	/**
	 * Cloning is forbidden due to singleton pattern.
	 *
	 * @since 1.0.0
	 */
	public function __clone() {
		$cloning_message = sprintf(
			esc_html__( 'You cannot clone instances of %s.', 'product-variations-view' ),
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
			esc_html__( 'You cannot clone instances of %s.', 'product-variations-view' ),
			get_class( $this )
		);
		_doing_it_wrong( __FUNCTION__, $unserializing_message, $this->version );
	}


	/**
	 * Checks the environment on loading WordPress, just in case the environment changes after activation.
	 *
	 * @since 1.0.0
	 */
	public function check_environment() {

		if ( ! $this->envChecker->check_php_version() && is_plugin_active( plugin_basename( PVV_FILE ) ) ) {

			$this->deactivate_plugin();
			$this->add_admin_notice(
				'bad_environment',
				'error',
				$this->plugin_name . esc_html__( ' has been deactivated. ', 'product-variations-view' ) . $this->envChecker->get_php_notice()
			);
		}
	}

	/**
	 * Deactivate the plugin
	 *
	 * @since 1.0.0
	 */
	protected function deactivate_plugin() {

		deactivate_plugins( plugin_basename( PVV_FILE ) );

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

		if ( ! $this->envChecker->check_wp_version() ) {

			$this->add_admin_notice( 'update_wordpress', 'error', $this->envChecker->get_wp_notice() );
		}

		if ( ! $this->envChecker->check_wc_version() ) {

			$this->add_admin_notice( 'update_woocommerce', 'error', $this->envChecker->get_wc_notice() );
		}
	}

	/**
	 * Displays any admin notices added with \Pvv::add_admin_notice()
	 *
	 * @since 1.0.0
	 */
	public function admin_notices() {

		foreach ( (array) $this->notices as $notice_key => $notice ) {

			printf(
				'<div class="%s"><p>%s</p></div>',
				esc_attr( $notice['class'] ),
				wp_kses( $notice['message'], array( 'a' => array( 'href' => array( array() ) ) ) )
			);

		}
	}

	/**
	 * Initializes the plugin.
	 *
	 * @since 1.0.0
	 */
	public function init_plugin() {

		if ( ! $this->envChecker->is_compatible() ) {
			return;
		}

		if ( is_admin() ) {

			PvvAdmin::start_admin();
		}
	}

	/**
	 * Include template functions and hooks.
	 */
	public function frontend_includes() {
		if ( $this->is_frontend_enabled() ) {
			new PvvFront();
			require_once INCLUDES_FOLDER . 'wc-cvp-template-functions.php';
			require_once INCLUDES_FOLDER . 'wc-cvp-template-hooks.php';
		}
	}
	/**
	 * Checks whether the frontend functionality is enabled.
	 *
	 * @return bool True if frontend functionality is enabled, false otherwise.
	 */
	private function is_frontend_enabled() {
		$is_enabled = get_option( 'pvv_is_enabled', true );
		return filter_var( $is_enabled, FILTER_VALIDATE_BOOLEAN );
	}


	public function load_textdomain() {
		load_plugin_textdomain( 'product-variations-view', false, $this->plugin_path() . '/languages' );
	}
	public function plugin_path() {
		return $this->helper->get_plugin_path();
	}
	public function plugin_url() {
		return $this->helper->get_plugin_url();
	}
}
