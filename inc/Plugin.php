<?php
/**
 * Plugin Class.
 *
 * Establish Plugin Singleton and entry
 * point for all services.
 *
 * @package Saucal
 */

namespace Saucal;

use Saucal\Core\Container;

class Plugin {
	/**
	 * Plugin Instance.
	 *
	 * @since 1.0.0
	 *
	 * @var Plugin
	 */
	protected static $instance;

	/**
	 * Set up Instance.
	 *
	 * @since 1.0.0
	 *
	 * @return Plugin
	 */
	public static function get_instance(): Plugin {
		if ( is_null( static::$instance ) ) {
			static::$instance = new self();
		}

		return static::$instance;
	}

	/**
	 * Run Plugin.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function run(): void {
		( new Container() )->register();
	}
}