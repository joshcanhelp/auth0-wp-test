<?php

/*
 *
 * Actions
 *
 */

/**
 * @param WP_User $user - WordPress user ID
 */
function auth0_theme_hook_auth0_before_login ( $user ) {
	echo '<strong>WP user</strong>:<br>' . '<pre>' . print_r( $user, TRUE ) . '</pre>' . '<hr>';
	wp_die( 'Login process started!' );
}
//add_action( 'auth0_before_login', 'auth0_theme_hook_auth0_before_login', 10, 1 );

/**
 * @param integer $user_id - WordPress user ID for logged-in user
 * @param stdClass $userinfo - user information object from Auth0
 * @param boolean $is_new - true if the user was created in WordPress, false if not
 * @param string $id_token - ID token for the user from Auth0 (not used in code flow)
 * @param string $access_token - bearer access token from Auth0 (not used in implicit flow)
 */
function auth0_theme_hook_auth0_user_login ( $user_id, $userinfo, $is_new, $id_token, $access_token ) {
	echo '<strong>WP user ID</strong>:<br>' . $user_id . '<hr>';
	echo '<strong>Auth0 user info</strong>:<br><pre>' . print_r( $userinfo, TRUE ) . '</pre><hr>';
	echo '<strong>Added to WP DB?</strong>:<br>' . ( $is_new ? 'yep' : 'nope' ) . '' . '<hr>';
	echo '<strong>ID Token</strong>:<br>' . ( $id_token ? $id_token : 'not provided' ) . '<hr>';
	echo '<strong>Access Token</strong>:<br>' . ( $access_token ? $access_token : 'not provided' ) . '<hr>';
	wp_die( 'Login successful!' );
}
//add_action( 'auth0_user_login', 'auth0_theme_hook_auth0_user_login', 10, 5 );

/**
 * @param integer $user_id - WordPress user ID for created user
 * @param string $email - email address for created user
 * @param string $password - password used for created user
 * @param string $f_name - first name for created user
 * @param string $l_name - last name for created user
 */
function auth0_theme_hook_wpa0_user_created ( $user_id, $email, $password, $f_name, $l_name ) {
	echo '<strong>User ID</strong>:<br>' . $user_id . '<hr>';
	echo '<strong>Email</strong>:<br>' . $email . '<hr>';
	echo '<strong>Password</strong>:<br>' . $password . '<hr>';
	echo '<strong>First name</strong>:<br>' . $f_name . '<hr>';
	echo '<strong>Last name</strong>:<br>' . $l_name . '<hr>';
	wp_die( 'User created!' );
}
//add_action( 'wpa0_user_created', 'auth0_theme_hook_wpa0_user_created', 10, 5 );

/*
 *
 * Filters
 *
 */

/**
 * @param WP_User|null $user - found WordPress user, null if no user was found
 * @param stdClass $userinfo - user information from Auth0
 *
 * @return WP_User|null
 */
function auth0_theme_hook_auth0_get_wp_user( $user, $userinfo ) {
	$found_user = get_user_by( 'email', $userinfo->email );
	$user = $found_user instanceof WP_User ? $user : null;
	return $user;
}
//add_filter( 'auth0_get_wp_user', 'auth0_theme_hook_auth0_get_wp_user', 1, 2 );

/**
 * @param string $html - HTML to modify, echoed out within wp_die()
 * @param stdClass $userinfo - user info object from Auth0
 * @param string $id_token - DEPRECATED, do not use
 *
 * @return string
 */
function auth0_theme_hook_auth0_verify_email_page ( $html, $userinfo, $id_token ) {
	$html = 'Hi ' . $userinfo->email . '!<br>' . $html;
	$html = str_replace( 'email', 'banana', $html );
	return $html;
}
//add_filter( 'auth0_verify_email_page', 'auth0_theme_hook_auth0_verify_email_page', 10, 3 );

/**
 * @param string $connection - name of the connection, initially pulled from Auth0 plugin settings
 *
 * @return string mixed
 */
function auth0_theme_hook_auth0_get_auto_login_connection( $connection ) {
	$connection = ! empty( $_GET[ 'connection' ] ) ? sanitize_text_field( $_GET[ 'connection' ] ) : $connection;
	return $connection;
}
//add_filter( 'auth0_get_auto_login_connection', 'auth0_theme_hook_auth0_get_auto_login_connection' );

/**
 * @param mixed $value - value of the option, initially pulled from the database
 * @param string $key - key of the settings array
 *
 * @return mixed
 */
function auth0_theme_hook_wp_auth0_get_option( $value, $key ) {
	$value = 'bad_key' === $key ? 'That is a bad key and you know it' : $value;
	return $value;
}
//add_filter( 'wp_auth0_get_option', 'auth0_theme_hook_wp_auth0_get_option', 10, 2 );

/**
 * @param WP_User $user - WordPress user object found during migration and authenticated
 *
 * @return WP_User
 */
function auth0_theme_hook_auth0_migration_ws_authenticated( $user ) {
	$user->data->display_name = 'Sir ' . $user->data->display_name . ', Esquire';
	return $user;
}
//add_filter( 'auth0_migration_ws_authenticated', 'auth0_theme_hook_auth0_migration_ws_authenticated' );

/**
 * @param bool $should_create - should the user be created, initialized as TRUE
 * @param stdClass $userinfo - Auth0 user information
 *
 * @return bool
 */
function auth0_theme_hook_wpa0_should_create_user( $should_create, $userinfo ) {
	$should_create = FALSE === strpos( 'josh', $userinfo->email );
	return $should_create;
}
//add_filter( 'wpa0_should_create_user', 'auth0_theme_hook_wpa0_should_create_user' );

/**
 * @param string $css, initialized as empty
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
//add_filter( 'auth0_login_css', 'auth0_theme_hook_auth0_login_css' );

/**
 * Append a cache-breaking parameter to login_url redirects
 *
 * @param string $login_url
 * @param string $redirect
 *
 * @return string
 */
function auth0_theme_hook_login_url( $login_url, $redirect )  {
	if ( ! empty( $redirect ) ) {
		$login_url = remove_query_arg( 'redirect_to', $login_url );
		$redirect = add_query_arg( 'logged_in', 1, $redirect );
		$redirect = urlencode( $redirect );
		$login_url = add_query_arg( 'redirect_to', $redirect, $login_url );
	}
	return $login_url;
}
//add_filter( 'login_url', 'auth0_theme_hook_login_url', 10, 2 );