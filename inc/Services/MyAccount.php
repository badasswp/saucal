<?php
/**
 * MyAccount Service.
 *
 * Set up MyAccount logic for displaying the
 * user interface.
 *
 * @package Saucal
 */

namespace Saucal\Services;

use Saucal\Core\API;
use Saucal\Abstracts\Service;
use Saucal\Interfaces\Kernel;

class MyAccount extends Service implements Kernel {
	/**
	 * Bind to WP.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function register(): void {
		add_action( 'init', [ $this, 'save_form_options' ] );
		add_action( 'init', [ $this, 'add_saucal_endpoint' ] );
		add_action( 'woocommerce_account_saucal-tab_endpoint', [ $this, 'add_saucal_content' ] );
		add_filter( 'woocommerce_account_menu_items', [ $this, 'add_saucal_tab' ] );
	}
}