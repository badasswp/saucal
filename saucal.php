<?php
/**
 * Plugin Name: Saucal
 * Plugin URI:  https://github.com/badasswp/saucal
 * Description: Saucal Plugin.
 * Version:     1.0.0
 * Author:      badasswp
 * Author URI:  https://github.com/badasswp
 * License:     GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain: saucal
 * Domain Path: /languages
 *
 * @package Saucal
 */

namespace badasswp\Saucal;

if ( ! defined( 'WPINC' ) ) {
	exit;
}

define( 'SAUCAL_AUTOLOAD', __DIR__ . '/vendor/autoload.php' );

// Composer Check.
if ( ! file_exists( SAUCAL_AUTOLOAD ) ) {
	add_action(
		'admin_notices',
		function () {
			vprintf(
				/* translators: Plugin directory path. */
				esc_html__( 'Fatal Error: Composer not setup in %s', 'saucal' ),
				[ __DIR__ ]
			);
		}
	);

	return;
}

// Run Plugin.
require_once SAUCAL_AUTOLOAD;
( \Saucal\Plugin::get_instance() )->run();
