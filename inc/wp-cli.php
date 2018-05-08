<?php

if ( defined( 'WP_CLI' ) && WP_CLI ) {
  final class Auth0_Cli {


    /**
     * Options loaded from the DB
     *
     * @var array
     */
    private $opts = [];
    /**
     * Have the options been updated yet?
     *
     * @var bool
     */
    private $opts_updated = false;

    /**
     * Auth0_Cli constructor.
     */
    public function __construct() {

      $this->opts = get_option( 'wp_auth0_settings' );
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
      if ( ! empty( $args[0] ) && ! empty( $args[1] ) ) {
        $this->do_set_opt( $args[0], $args[1] );
      }
      // Command like: `wp auth0 set_opt --domain="tenant.auth0.com" --client_id="AUTH0_CLIENT_ID"`
      if ( ! empty( $assoc_args ) ) {
        foreach ( $assoc_args as $key => $val ) {
          if ( ! $this->do_set_opt( $key, $val ) ) {
            continue;
          }
        }
      }
      if ( $this->opts_updated ) {
        update_option( 'wp_auth0_settings', $this->opts );
        WP_CLI::success( 'Options updated!' );
        $this->opts_updated = false;
      } else {
        WP_CLI::error( 'Nothing to change' );
      }
    }

    /**
     * Do the change if possible, output a line if not.
     *
     * @param string          $key - option key to set.
     * @param bool|string|int $val - value to change the key to.
     *
     * @return bool
     */
    private function do_set_opt( $key, $val ) {

      if ( ! array_key_exists( $key, $this->opts ) ) {
        WP_CLI::line( 'Option `' . $key . '` does not exist ... skipping' );

        return false;
      }
      $this->opts[ $key ] = $val;
      WP_CLI::line( 'Set `' . $key . '` to ' . $val );
      $this->opts_updated = true;

      return true;
    }

    /**
     * Create Auth0 test pages
     *
     * @throws \WP_CLI\ExitException
     */
    public function make_test_pages() {

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
        foreach ( auth0_theme_get_test_page_slugs() as $slug ) {
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

      $app_token = $this->opts[ 'auth0_app_token' ];

      if ( ! $app_token ) {
        WP_CLI::error( 'WP user deleted, no app token to delete existing Auth0 user' );
      }

      $resp = wp_remote_request(
        'https://' . $this->opts[ 'domain' ] . '/api/v2/users/' . $auth0_id,
        [
          'method' => 'DELETE',
          'headers' => [
            'Authorization' => 'Bearer ' . $app_token
          ]
        ]
      );

      $delete_resp_code = (int) wp_remote_retrieve_response_code( $resp );
      $delete_resp_body = wp_remote_retrieve_body( $resp );

      if ( 204 !== $delete_resp_code ) {
        WP_CLI::error( $delete_resp_body );
      }

      WP_CLI::success( 'WP user deleted, Auth0 user deleted!' );
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
