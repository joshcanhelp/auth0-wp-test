<?php
/**
 * Sample filters included in the Auth0 plugin
 *
 * @package auth0-wp-test
 *
 * @link https://auth0.com/docs/cms/wordpress/extending#filters
 */

/**
 * Filter the WordPress user found during login.
 *
 * @see WP_Auth0_LoginManager::login_user()
 *
 * @param WP_User|null $user     - found WordPress user, null if no user was found.
 * @param stdClass     $userinfo - user information from Auth0.
 *
 * @return WP_User|null
 */
function auth0_theme_hook_auth0_get_wp_user( $user, $userinfo ) {
	$found_user = get_user_by( 'email', $userinfo->email );
	$user       = $found_user instanceof WP_User ? $user : null;
	return $user;
}
// add_filter( 'auth0_get_wp_user', 'auth0_theme_hook_auth0_get_wp_user', 1, 2 );

/**
 * Filter the HTML used on the email verification wp_die page.
 *
 * @see WP_Auth0_Email_Verification::render_die()
 *
 * @param string   $html     - HTML to modify, echoed out within wp_die().
 * @param stdClass $userinfo - user info object from Auth0.
 * @param string   $id_token - DEPRECATED, do not use.
 *
 * @return string
 */
function auth0_theme_hook_auth0_verify_email_page( $html, $userinfo, $id_token ) {
	$html = 'Hi ' . $userinfo->email . '!<br>' . $html;
	$html = str_replace( 'email', 'banana', $html );
	return $html;
}
// add_filter( 'auth0_verify_email_page', 'auth0_theme_hook_auth0_verify_email_page', 10, 3 );

/**
 * Filter the auto-login connection used by looking for a URL parameter.
 *
 * @param string $connection - name of the connection, initially pulled from Auth0 plugin settings.
 *
 * @return string mixed
 */
function auth0_theme_hook_auth0_get_auto_login_connection( $connection ) {
	return ! empty( $_GET['connection'] ) ? rawurlencode( $_GET['connection'] ) : $connection;
}
// add_filter( 'auth0_get_auto_login_connection', 'auth0_theme_hook_auth0_get_auto_login_connection' );

/**
 * Adjust an options value before use.
 *
 * @param mixed  $value - value of the option, initially pulled from the database.
 * @param string $key   - key of the settings array.
 *
 * @return mixed
 */
function auth0_theme_hook_wp_auth0_get_option( $value, $key ) {
	$value = 'bad_key' === $key ? 'That is a bad key and you know it' : $value;
	return $value;
}
// add_filter( 'wp_auth0_get_option', 'auth0_theme_hook_wp_auth0_get_option', 10, 2 );

/**
 * Filter the WP user object before sending back to Auth0 during migration.
 *
 * @param WP_User $user - WordPress user object found during migration and authenticated.
 *
 * @return WP_User
 */
function auth0_theme_hook_auth0_migration_ws_authenticated( $user ) {
	$user->data->display_name = 'Sir ' . $user->data->display_name . ', Esquire';
	return $user;
}
// add_filter( 'auth0_migration_ws_authenticated', 'auth0_theme_hook_auth0_migration_ws_authenticated' );

/**
 * Should a new user be created?
 *
 * @param bool     $should_create - should the user be created, initialized as TRUE
 * @param stdClass $userinfo      - Auth0 user information
 *
 * @return bool
 */
function auth0_theme_hook_wpa0_should_create_user( $should_create, $userinfo ) {
	$should_create = false === strpos( 'josh', $userinfo->email );
	return $should_create;
}
// add_filter( 'wpa0_should_create_user', 'auth0_theme_hook_wpa0_should_create_user' );

/**
 * Add CSS to the Auth0 login form.
 *
 * @param string $css - initialized as empty.
 *
 * @return string
 */
function auth0_theme_hook_auth0_login_css( $css ) {
	$css .= '
		body {background: radial-gradient(#01B48F, #16214D)} 
		#login h1 {display: none}
		.login form.auth0-lock-widget {box-shadow: none}
	';
	return $css;
}
// add_filter( 'auth0_login_css', 'auth0_theme_hook_auth0_login_css' );

/**
 * Override the Lock login form template.
 *
 * @param string  $tpl_path - original template path.
 * @param array   $lock_options - Lock options.
 * @param boolean $show_legacy_login - Should the template include a link to the standard WP login?
 *
 * @return string
 */
function auth0_theme_hook_auth0_login_form_tpl( $tpl_path, $lock_options, $show_legacy_login ) {
	return AUTH0_THEME_ROOT . '/templates/auth0-login-form.html';
}
// add_filter( 'auth0_login_form_tpl', 'auth0_theme_hook_auth0_login_form_tpl', 10, 3 );

/**
 * Modify existing or add new settings fields.
 *
 * @param array  $options - array of options for a specific settings tab.
 * @param string $id      - settings tab id.
 *
 * @return array
 *
 * @see WP_Auth0_Admin_Generic::init_option_section()
 */
function auth0_theme_hook_auth0_settings_fields( $options, $id ) {
	switch ( $id ) {
		case 'basic':
			$options[] = array(
				'name'     => __( 'A Custom Basic Setting' ),
				'opt'      => 'custom_basic_opt_name',
				'id'       => 'wpa0_custom_basic_opt_name',
				'function' => 'auth0_theme_render_custom_basic_opt_name',
			);
			break;
		case 'features':
			break;
		case 'appearance':
			break;
		case 'advanced':
			break;
	}
	return $options;
}
// add_filter( 'auth0_settings_fields', 'auth0_theme_hook_auth0_settings_fields', 10, 2 );

/**
 * Callback for add_settings_field
 *
 * @param array $args - 'label_for' = id attr, 'opt_name' = option name
 *
 * @see auth0_theme_hook_auth0_settings_fields()
 */
function auth0_theme_render_custom_basic_opt_name( $args ) {
	$options = WP_Auth0_Options::Instance();
	printf(
		'<input type="text" name="%s[%s]" id="%s" value="%s">',
		esc_attr( $options->get_options_name() ),
		esc_attr( $args['opt_name'] ),
		esc_attr( $args['label_for'] ),
		esc_attr( $options->get( $args['opt_name'] ) )
	);
}

/**
 * Add or modify requested access token scopes during login.
 *
 * @param array $scopes - current array of scopes to add/delete/modify
 *
 * @return array
 */
function auth0_theme_hook_auth0_auth_scope( $scopes ) {
	// Add offline_access to include a refresh token.
	// See auth0_theme_hook_auth0_user_login() for how this token can be used.
	$scopes[] = 'offline_access';
	return $scopes;
}
// add_filter( 'auth0_auth_scope', 'auth0_theme_hook_auth0_auth_scope' );

/**
 * Prefix state and nonce cookie names.
 *
 * @param string $cookie_name - Cookie name to modify.
 *
 * @return string
 */
function auth0_theme_hook_prefix_cookie_name( $cookie_name ) {
	return 'STYXKEY_' . $cookie_name;
}
// add_filter( 'auth0_state_cookie_name', 'auth0_theme_hook_prefix_cookie_name' );
// add_filter( 'auth0_nonce_cookie_name', 'auth0_theme_hook_prefix_cookie_name' );

/**
 * Prefix used for constant-based options.
 * NOTE: This must load before WP_Auth0::init() so it cannot be used in a theme.
 *
 * @param string $prefix - Constant prefix to modify.
 *
 * @return string
 */
function auth0_theme_hook_settings_constant_prefix( $prefix ) {
	// Replace the prefix with something else.
	// return 'AUTH_ENV_';

	// Prefix the prefix.
	return 'PREFIX_' . $prefix;
}
// add_filter( 'auth0_settings_constant_prefix', 'auth0_theme_hook_settings_constant_prefix' );

/**
 * Adjust the authorize URL parameters used for auto-login and universal login page.
 *
 * @param array $params - Existing URL parameters.
 * @param string $connection - Connection for auto-login, optional.
 * @param string $redirect_to - URL to redirect to after logging in.
 *
 * @return mixed
 */
function auth0_theme_hook_authorize_url_params( $params, $connection, $redirect_to ) {
	if ( 'twitter' === $connection ) {
		$params[ 'param1' ] = 'value1';
	}

	if ( FALSE !== strpos( 'twitter', $redirect_to ) ) {
		$params[ 'param2' ] = 'value2';
	}

	return $params;
}
//add_filter( 'auth0_authorize_url_params', 'auth0_theme_hook_authorize_url_params', 10, 3 );

/**
 * Adjust the authorize URL parameters used for auto-login and universal login page.
 *
 * @param string $auth_url - Built authorize URL.
 * @param array $auth_params - Existing URL parameters.
 *
 * @return mixed
 */
function auth0_theme_hook_authorize_url( $auth_url, $auth_params ) {

	if ( 'twitter' === $auth_params['connection'] ) {
		$auth_url .= '&param1=value1';
	}

	if ( ! empty( $auth_params['display'] ) ) {
		$auth_url .= '&param2=value2';
	}

	$auth_url .= '&param3=value3';
	return $auth_url;
}
//add_filter( 'auth0_authorize_url', 'auth0_theme_hook_authorize_url', 10, 3 );
