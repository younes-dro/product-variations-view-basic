<?php
/**
 * Invalid Product Exception
 *
 * Custom exception thrown when a product context is invalid (e.g., null or not variable).
 *
 * @package     DRO\PVVP\Includes\Exceptions
 * @version     1.1.0
 * @since       1.1.0
 * @author      Younes DRO <younesdro@gmail.com>
 * @license     GPL-2.0-or-later
 */

declare(strict_types=1);

namespace DRO\PVVP\Includes\Exceptions;

use Exception;

/**
 * Class DRO_PVVP_Invalid_Product_Exception
 *
 * Thrown when the product is either not set or not a variable product.
 */
class DRO_PVVP_Invalid_Product_Exception extends Exception {}
