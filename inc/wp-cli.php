<?php

if ( defined( 'WP_CLI' ) && WP_CLI ) {
	final class Auth0_Cli {

		const AUTH0_OPT_NAME = 'wp_auth0_settings';

		/**
		 * @var WP_Auth0_Options
		 */
		private $opts_obj;

		/**
		 * @var array
		 */
		private $opts = [];

		/**
		 * Auth0_Cli constructor.
		 */
		public function __construct() {
			$this->opts_obj = WP_Auth0_Options::Instance();
			$this->opts     = $this->opts_obj->get_options();
		}

		/**
		 * Show all options
		 */
		public function get_opts() {
			echo '<pre>' . print_r( $this->opts, true ) . '</pre>';
		}

		/**
		 * Set an option or multiple options.
		 *
		 * @param array $args       - used to set a single option.
		 * @param array $assoc_args - used to set multiple options.
		 *
		 * @throws \WP_CLI\ExitException
		 */
		public function set_opt( $args = [], $assoc_args = [] ) {
			if ( empty( $args ) && empty( $assoc_args ) ) {
				WP_CLI::error( 'No arguments to process' );
			}

			// Command like: `wp auth0 set_opt domain tenant.auth0.com`
			if ( isset( $args[0] ) && isset( $args[1] ) ) {
				$this->do_set_opt( $args[0], $args[1] );
			}

			// Command like: `wp auth0 set_opt --domain="tenant.auth0.com" --client_id="AUTH0_CLIENT_ID"`
			if ( ! empty( $assoc_args ) ) {
				foreach ( $assoc_args as $key => $val ) {
					$this->do_set_opt( $key, $val );
				}
			}
			WP_CLI::success( 'Options updated!' );
		}

		/**
		 * Do the change if possible, output a line if not.
		 *
		 * @param string          $key - option key to set.
		 * @param bool|string|int $val - value to change the key to.
		 */
		private function do_set_opt( $key, $val ) {
			if ( array_key_exists( $key, $this->opts ) ) {
				$this->opts[ $key ] = $val;
				update_option( self::AUTH0_OPT_NAME, $this->opts );
				WP_CLI::line( '✔︎ Set `' . $key . '` to ' . $val );
			} else {
				WP_CLI::line( '✘ Option `' . $key . '` not found' );
			}
		}

		/**
		 * Create Auth0 test pages
		 *
		 * @param array $args - CLI parameters
		 *
		 * @throws \WP_CLI\ExitException
		 */
		public function make_test_pages( $args = [] ) {

			if ( isset( $args[0] ) ) {
				switch_to_blog( absint( $args[0] ) );
			}

			$parent_id = wp_insert_post(
				[
					'post_type'   => 'page',
					'post_title'  => 'Auth0 Testing',
					'post_name'   => 'auth-zero',
					'post_status' => 'publish',
					'post_author' => 1,
				]
			);
			if ( $parent_id && ! is_wp_error( $parent_id ) ) {
				foreach ( auth0_wp_test_get_test_page_slugs() as $slug ) {
					$page_id = wp_insert_post(
						[
							'post_type'   => 'page',
							'post_title'  => 'Test ' . ucfirst( $slug ),
							'post_name'   => 'test-' . $slug,
							'post_status' => 'publish',
							'post_author' => 1,
							'post_parent' => $parent_id,
						]
					);
					if ( empty( $page_id ) || is_wp_error( $page_id ) ) {
						WP_CLI::error( 'Problem creating the child page ' . $slug );
					}
				}
			} else {
				WP_CLI::error( 'Problem creating the parent page' );
			}
		}

		/**
		 * @param array $args - CLI args
		 *
		 * @throws \WP_CLI\ExitException
		 */
		public function delete_user( $args ) {
			if ( empty( $args[0] ) ) {
				WP_CLI::error( 'No user ID!' );
			}

			$user_id = intval( $args[0] );

			if ( ! get_user_by( 'id', $user_id ) ) {
				WP_CLI::error( 'No user found for ID: ' . $user_id );
			}

			$auth0_id = get_user_meta( $user_id, 'wp_auth0_id', true );

			if ( ! wp_delete_user( $user_id, 1 ) ) {
				WP_CLI::error( 'Something went wrong when deleting ID: ' . $user_id );
			}

			if ( ! $auth0_id ) {
				WP_CLI::success( 'WP user deleted, no Auth0 connection found' );
				return;
			}

			$client_credentials = new WP_Auth0_Api_Client_Credentials( $this->opts_obj );
			$api_token          = $client_credentials->call();

			if ( ! $api_token ) {
				WP_CLI::error( 'WP user deleted, no app token to delete existing Auth0 user' );
			}

			$resp = wp_remote_request(
				'https://' . $this->opts['domain'] . '/api/v2/users/' . $auth0_id,
				[
					'method'  => 'DELETE',
					'headers' => [
						'Authorization' => 'Bearer ' . $api_token,
					],
				]
			);

			$delete_resp_code = (int) wp_remote_retrieve_response_code( $resp );
			$delete_resp_body = wp_remote_retrieve_body( $resp );

			if ( 204 !== $delete_resp_code ) {
				WP_CLI::error( $delete_resp_body );
				return;
			}

			WP_CLI::success( 'WP user deleted, Auth0 user deleted!' );
		}

		/**
		 * @throws \WP_CLI\ExitException
		 */
		public function delete_client() {
			if ( empty( $this->opts['client_id'] ) ) {
				WP_CLI::error( 'No Client ID for this site' );
				return;
			}

			if ( empty( $this->opts['auth0_app_token'] ) ) {
				WP_CLI::error( 'No API token for this site' );
				return;
			}

			$client_id = $this->opts['client_id'];
			$app_token = $this->opts['auth0_app_token'];

			$resp = wp_remote_request(
				'https://' . $this->opts['domain'] . '/api/v2/clients/' . $client_id,
				[
					'method'  => 'DELETE',
					'headers' => [
						'Authorization' => 'Bearer ' . $app_token,
					],
				]
			);

			$delete_resp_code = (int) wp_remote_retrieve_response_code( $resp );
			$delete_resp_body = wp_remote_retrieve_body( $resp );

			if ( 204 !== $delete_resp_code ) {
				WP_CLI::error( $delete_resp_body );
				return;
			}

			WP_CLI::success( 'Auth0 Client deleted!' );
		}
	}

	try {
		WP_CLI::add_command( 'auth0', 'Auth0_Cli' );
	} catch ( Exception $e ) {
		try {
			WP_CLI::error( $e->getMessage() );
		} catch ( \WP_CLI\ExitException $e ) {
			echo $e->getMessage();
		}
	}
}
