<?php
/**
 * Interface for registering and enqueuing layout-specific assets (JS/CSS).
 *
 * Classes implementing this interface must define the method to enqueue
 * any necessary frontend assets (stylesheets or scripts) required
 * for a specific gallery layout.
 *
 * This is useful to keep layout concerns encapsulated and maintain
 * separation of logic between layout rendering and asset loading.
 *
 * @package DRO\PVVP\Includes\Gallery\Interfaces
 * @since 1.1.0
 * @version 1.1.0
 * @author Younes DRO <younesdro@gmail.com>
 * @license GPL-2.0-or-later
 */

declare(strict_types=1);

namespace DRO\PVVP\Includes\Gallery\Interfaces;

/**
 * Interface DRO_PVVP_Layout_Assets_Interface
 *
 * Defines the method required to enqueue assets for a specific layout.
 */
interface DRO_PVVP_Layout_Assets_Interface {

	/**
	 * Enqueue frontend styles and scripts for this layout.
	 *
	 * This method is called during an appropriate WordPress hook (e.g. `wp_enqueue_scripts`)
	 * and is intended to include CSS/JS specific to the gallery layout.
	 *
	 * @return void
	 */
	public function enqueue_assets(): void;
}
