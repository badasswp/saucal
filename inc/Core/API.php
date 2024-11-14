<?php
/**
 * API Class.
 *
 * This class handles all remote API calls.
 *
 * @package Saucal
 */

namespace Saucal\Core;

class API {
	/**
	 * API options.
	 *
	 * @since 1.0.0
	 *
	 * @var array
	 */
	protected array $options = [];

	/**
	 * Initial setup.
	 *
	 * @since 1.0.0
	 *
	 * @param string  $url     Remote Endpoint.
	 * @param mixed[] $payload Payload Array.
	 */
	public function __construct( $url, $payload ) {
		$this->options['timeout']     = 300;
		$this->options['redirection'] = 5;
		$this->options['blocking']    = true;
		$this->options['httpversion'] = '1.0';
		$this->options['sslverify']   = true;

		$this->url     = $url;
		$this->payload = $payload;
	}

	/**
	 * Get Headers for API call.
	 *
	 * @since 1.0.0
	 *
	 * @return mixed[]
	 */
	protected function get_headers(): array {
		$this->options['headers'] = [
			'Cache-Control' => 'no-cache',
			'Content-Type'  => 'application/json',
		];

		/**
		 * Filter API Header options.
		 *
		 * @since 1.0.0
		 *
		 * @param array $options API Header options.
		 * @return array
		 */
		$this->options['headers'] = apply_filters( 'saucal_api_header', $this->options['headers'] ?? [] );

		return $this->options['headers'];
	}

	/**
	 * Get Body for API call.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	protected function get_body(): string {
		/**
		 * Filter API Body payload.
		 *
		 * @since 1.0.0
		 *
		 * @param mixed[] $payload API JSON payload.
		 * @return string
		 */
		$this->options['body'] = wp_json_encode( apply_filters( 'saucal_api_body', $this->payload ) );

		return $this->options['body'];
	}

	/**
	 * Get API Error message.
	 *
	 * @since 1.0.0
	 *
	 * @param string $message Error message.
	 * @return \WP_Error
	 */
	protected function get_api_error( $message ): \WP_Error {
		return new \WP_Error(
			'saucal-api-error',
			sprintf(
				'Error: %2$s. Options: %1$s',
				wp_json_encode( $this->options ),
				$message,
			)
		);
	}

	/**
	 * Get JSON.
	 *
	 * @since 1.0.0
	 *
	 * @param string $response API Response.
	 * @return mixed[]
	 */
	protected function get_json( $response ): array {
		return json_decode( wp_remote_retrieve_body( $response ), true );
	}

	/**
	 * Fetch User Feed.
	 *
	 * @since 1.0.0
	 *
	 * @return mixed[]|\WP_Error
	 */
	public function fetch() {
		$api_headers = $this->get_headers();
		$api_body    = $this->get_body();

		if ( ! $this->url ) {
			return $this->get_api_error( 'No URL set' );
		}

		if ( empty( $this->options['body'] ?? '' ) ) {
			return $this->get_api_error( 'No Payload set' );
		}

		$response = wp_safe_remote_post( $this->url, $this->options );

		if ( is_wp_error( $response ) ) {
			return $this->get_api_error( $response->get_error_message() );
		}

		return $this->get_json( $response );
	}
}
