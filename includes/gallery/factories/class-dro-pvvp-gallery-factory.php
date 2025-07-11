<?php
/**
 * Gallery Factory Class
 *
 * @since 1.1.0
 * @version 1.1.0
 * @author Younes DRO
 * @license GPL-2.0-or-later
 * @package DRO\PVVP\Includes\Gallery\Factories
 */
declare(strict_types=1);

namespace DRO\PVVP\Includes\Gallery\Factories;

use DRO\PVVP\Includes\Gallery\Interfaces\DRO_PVVP_Gallery_Interface as Gallery_Interface;
use DRO\PVVP\Includes\Gallery\Layouts\DRO_PVVP_Default_Gallery as Default_Gallery;
use Exception;

defined( 'ABSPATH' ) || exit;

/**
 * Gallery Factory Class
 *
 * Responsible for instantiating the appropriate gallery layout class
 * based on the layout configuration provided. Acts as a Factory in the
 * design pattern to abstract layout instantiation logic.
 *
 * @package DRO\PVVP\Includes\Gallery
 */
class DRO_PVVP_Gallery_Factory {


	/**
	 * Singleton instance
	 *
	 * @var self|null
	 */
	private static ?self $instance = null;


	/**
	 * Constructor
	 */
	public function __construct() {
	}

	/**
	 * Get singleton instance
	 *
	 * @return self
	 */
	public static function get_instance(): self {
		return self::$instance ??= new self();
	}

	/**
	 * Create gallery for product using configuration
	 *
	 * @param string $config Gallery configuration
	 * @return Gallery_Interface|null Gallery Layout or null if no variations
	 * @throws Exception
	 */
	public function create_gallery_layout( string $layout ): Gallery_Interface {

		// Call Gallery layout
		switch ( $layout ) {
			case 'default':
				return new Default_Gallery();
			default:
				throw new \InvalidArgumentException( "Unknown gallery layout: {$layout}" );
		}
	}
}
