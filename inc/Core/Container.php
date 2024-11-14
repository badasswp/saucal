<?php
/**
 * Container Class.
 *
 * This class acts as a Factory class for registering
 * all Plugin services.
 *
 * @package Saucal
 */

namespace Saucal\Core;

use Saucal\Services\MyAccount;
use Saucal\Interfaces\Kernel;

class Container implements Kernel {
	/**
	 * Services.
	 *
	 * @since 1.0.0
	 *
	 * @var array
	 */
	public static array $services;

	/**
	 * Set up Services.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		static::$services = [
			MyAccount::class,
		];
	}

	/**
	 * Register Services.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function register(): void {
		foreach ( static::$services as $service ) {
			( $service::get_instance() )->register();
		}
	}
}
