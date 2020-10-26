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
		 * Show all options
		 */
		public function esc_attr() {
			WP_CLI::line(esc_attr('\u0022;console.log("hi")'));
			WP_CLI::line(sanitize_text_field('\u0022;console.log("hi")'));
			WP_CLI::line(json_encode('\u0022;console.log("hi")'));
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
					'post_name'   => 'auth0-testing',
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
		 * @param bool $exit - Exit the process on error?
		 *
		 * @throws \WP_CLI\ExitException
		 */
		public function delete_client( $exit = true ) {
			if ( empty( $this->opts['client_id'] ) ) {
				WP_CLI::error( 'No Client ID for this site', $exit );
				return;
			}

			if ( empty( $this->opts['auth0_app_token'] ) ) {
				WP_CLI::error( 'No API token for this site', $exit );
				return;
			}

			$client_id = $this->opts['client_id'];
			$app_token = $this->opts['auth0_app_token'];

			$delete_resp = wp_remote_request(
				'https://' . $this->opts['domain'] . '/api/v2/clients/' . $client_id,
				[
					'method'  => 'DELETE',
					'headers' => [
						'Authorization' => 'Bearer ' . $app_token,
					],
				]
			);

			$delete_resp_code = (int) wp_remote_retrieve_response_code( $delete_resp );
			$delete_resp_body = wp_remote_retrieve_body( $delete_resp );

			if ( 204 !== $delete_resp_code ) {
				WP_CLI::error( $delete_resp_body, $exit );
				return;
			}

			WP_CLI::success( 'Auth0 Client deleted!' );
		}

		/**
		 * @param bool $exit - Exit the process on error?
		 *
		 * @throws \WP_CLI\ExitException
		 */
		public function delete_connection( $exit = true ) {

			if ( empty( $this->opts['auth0_app_token'] ) ) {
				WP_CLI::error( 'No API token for this site', $exit );
				return;
			}

			$remote_req_opts = [
				'method' => 'GET',
				'headers' => [
					'Authorization' => 'Bearer ' . $this->opts['auth0_app_token'],
				],
			];
			$connections_url = 'https://' . $this->opts['domain'] . '/api/v2/connections';

			$search_resp = wp_remote_request(
				add_query_arg(
					[
						'name' => 'DB-' . get_auth0_curatedBlogName(),
						'strategy' => 'auth0',
						'fields' => 'id'
					],
					$connections_url
				),
				$remote_req_opts
			);

			$search_resp_code = (int) wp_remote_retrieve_response_code( $search_resp );
			$search_resp_body = json_decode( wp_remote_retrieve_body( $search_resp ) );

			if ( 200 !== $search_resp_code || empty( $search_resp_body ) ) {
				WP_CLI::error( 'No Connection found or error', $exit );
				return;
			}

			$delete_req_opts = $remote_req_opts;
			$delete_req_opts['method'] = 'DELETE';
			$delete_resp = wp_remote_request(
				'https://' . $this->opts['domain'] . '/api/v2/connections/' . $search_resp_body[0]->id,
				$delete_req_opts
			);

			$delete_resp_code = (int) wp_remote_retrieve_response_code( $delete_resp );
			$delete_resp_body = wp_remote_retrieve_body( $delete_resp );

			if ( 204 !== $delete_resp_code ) {
				WP_CLI::error( $delete_resp_body, $exit );
				return;
			}

			WP_CLI::success( 'Auth0 Connection deleted!' );
		}

		/**
		 * @throws \WP_CLI\ExitException
		 */
		public function reset_install() {

			if ( empty( $this->opts['auth0_app_token'] ) ) {
				WP_CLI::error( 'No API token for this site' );
			}

			$this->delete_client( false );
			$this->delete_connection( false );
			WP_Auth0::uninstall();
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
