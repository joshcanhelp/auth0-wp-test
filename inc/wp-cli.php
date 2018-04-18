<?php
if ( WP_CLI ) {
  final class Auth0_Cli {

    public function set_opt( $args = [], $assoc_args = [] ) {
      if ( empty( $assoc_args ) ) {
        WP_CLI::error( 'Nothing to do' );
      }

      $opts = get_option( 'wp_auth0_settings' );

      foreach ( $assoc_args as $key => $val ) {
        if ( ! array_key_exists( $key, $opts ) ) {
          WP_CLI::line( 'Option ' . $key . ' does not exist ... skipping' );
          continue;
        }
        $opts[$key] = $val;
        WP_CLI::line( 'Setting `' . $key . '` = ' . $val );
      }
      update_option( 'wp_auth0_settings', $opts );
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