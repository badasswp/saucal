<?php
/**
 * Kernel Interface.
 *
 * Define contract methods to be adopted globally
 * by classes across the plugin.
 *
 * @package Saucal
 */

namespace Saucal\Interfaces;

interface Kernel {
	/**
	 * Register class.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function register(): void;
}
