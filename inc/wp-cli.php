<?php
if ( WP_CLI ) {
  final class Auth0_Cli {

    /**
     * Options loaded from the DB
     *
     * @var array
     */
    private $opts = [];
    private $opts_updated = false;

    public function __construct() {
      $this->opts = get_option( 'wp_auth0_settings' );
    }

    /**
     * Set an option or multiple options.
     *
     * @param array $args - used to set a single option.
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
      } else {
        WP_CLI::error( 'Nothing to change' );
      }
    }

    /**
     * Do the change if possible, output a line if not.
     *
     * @param string $key - option key to set.
     * @param bool|string|int $val - value to change the key to.
     *
     * @return bool
     */
    private function do_set_opt( $key, $val ) {
      if ( ! array_key_exists( $key, $this->opts ) ) {
        WP_CLI::line( 'Option `' . $key . '` does not exist ... skipping' );
        return false;
      }
      $this->opts[$key] = $val;
      WP_CLI::line( 'Set `' . $key . '` to ' . $val );
      $this->opts_updated = true;
      return true;
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