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

	/**
	 * Add Saucal Tab.
	 *
	 * @since 1.0.0
	 *
	 * @param mixed[] $menu_items Menu Items.
	 * @return mixed[]
	 */
	public function add_saucal_tab( $menu_items ): array {
		$menu_items['saucal-tab'] = __( 'User Feed', 'saucal' );
		return $menu_items;
	}

	/**
	 * Add Saucal Endpoint.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function add_saucal_endpoint(): void {
		add_rewrite_endpoint( 'saucal-tab', EP_ROOT | EP_PAGES );
	}

	/**
	 * Add Saucal Content.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function add_saucal_content(): void {
		if ( ! is_user_logged_in() ) {
			return;
		}

		// User Feed Form.
		vprintf(
			'<form method="POST" action="./">
				<div>
					<h3>%1$s</h3>
					<p>%6$s</p>
					<p><textarea name="%2$s" rows="5">%7$s</textarea></p>
					<p><button name="%3$s">%4$s</button></p>
					<p>%5$s</p>
				</div>
			</form>',
			$this->get_form_options()
		);

		// User Feed Data.
		vprintf(
			'<div>
				<h3>%s</h3>
				<div>%s</div>
			</div>',
			$this->get_feed_options()
		);
	}

	/**
	 * Get Form Options.
	 *
	 * @since 1.0.0
	 *
	 * @return mixed[]
	 */
	protected function get_form_options(): array {
		$nonce_name   = 'saucal_nonce';
		$nonce_action = 'saucal_action';

		return [
			'heading'      => esc_html__( 'Form', 'saucal' ),
			'txarea_name'  => esc_attr( 'saucal_list' ),
			'button_name'  => esc_attr( 'saucal_submit' ),
			'button_label' => esc_html__( 'Submit', 'saucal' ),
			'nonce_field'  => wp_nonce_field( $nonce_action, $nonce_name, true, false ),
			'description'  => esc_html__( 'Enter the list of elements in the text area box below, each element should be on one line.', 'saucal' ),
			'form_content' => get_user_meta( get_current_user_id(), 'saucal_user_feed', true ),
		];
	}

	/**
	 * Get Feed Options.
	 *
	 * @since 1.0.0
	 *
	 * @return mixed[]
	 */
	protected function get_feed_options(): array {
		foreach( ( $this->get_user_feed() ?? [] ) as $feed ) {
			$user_feed .= sprintf( '<li>%s</li>', $feed );
		}

		$user_feed = sprintf( '<ul>%s</ul>', $user_feed );

		return [
			'heading' => esc_html__( 'User Feed', 'saucal' ),
			'content' => wp_kses(
				$user_feed,
				[
					'ul' => [],
					'li' => [],
				],
			)
		];
	}

	/**
	 * Save Form Options.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function save_form_options(): void {
		$nonce_name   = 'saucal_nonce';
		$nonce_action = 'saucal_action';

		if (
			isset( $_POST['saucal_submit'] ) &&
			wp_verify_nonce(
				sanitize_text_field( wp_unslash( $_POST[ $nonce_name ] ?? '' ) ),
				$nonce_action
			)
		) {
			// Update Settings.
			update_user_meta(
				get_current_user_id(),
				'saucal_user_feed',
				sanitize_textarea_field( $_POST['saucal_list'] )
			);

			// Update Cache.
			$cache_key = 'saucal_cache_' . get_current_user_id();
			wp_cache_set(
				$cache_key,
				( new API(
					'https://httpbin.org/post',
					$this->get_payload()
				) )->fetch()
			);
		}
	}
}
