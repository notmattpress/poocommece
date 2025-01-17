<?php
/**
 * Plugin_Formatter class
 *
 * @package  PooCommerce
 */

namespace Automattic\PooCommerce\MonorepoTools\Changelogger;

use Automattic\Jetpack\Changelogger\FormatterPlugin;

/**
 * Jetpack Changelogger Formatter for PooCommerce plugins
 */

require_once 'class-formatter.php';

/**
 * Jetpack Changelogger Formatter for PooCommerce Plugins
 *
 * Class Formatter
 */
class Plugin_Formatter extends Formatter implements FormatterPlugin {
	/**
	 * Epilogue text.
	 *
	 * @var string
	 */
	public $epilogue = "---\n\n[See changelogs for previous versions](https://raw.githubusercontent.com/poocommerce/poocommerce/trunk/changelog.txt).";

	/**
	 * Entry pattern regex.
	 *
	 * @var string
	 */
	public $entry_pattern = '/^##?#\s+([^\n=]+)\s+((?:(?!^##).)+)/ms';
}
