<?php
/**
 * Sample actions included in the Auth0 plugin
 *
 * @package auth0-wp-test
 *
 * @link https://auth0.com/docs/cms/wordpress/extending#actions
 */

/**
 * Stop login process before logging in and output the current $user object.
 * NOTE: The example below will break the user login process.
 *
 * @see WP_Auth0_LoginManager::do_login()
 *
 * @param WP_User $user - WordPress user ID
 */
function auth0_theme_hook_auth0_before_login( $user ) {
	echo '<strong>WP user</strong>:<br><pre>' . print_r( $user, true ) . '</pre><hr>';
	wp_die( 'Login process started!' );
}
// add_action( 'auth0_before_login', 'auth0_theme_hook_auth0_before_login', 10, 1 );
/**
 * Stop the login process after WP login.
 * NOTE: The example below will break the user login process.
 *
 * @see WP_Auth0_LoginManager::do_login()
 *
 * @param integer  $user_id       - WordPress user ID for logged-in user
 * @param stdClass $userinfo      - user information object from Auth0
 * @param boolean  $is_new        - true if the user was created in WordPress, false if not
 * @param string   $id_token      - ID token for the user from Auth0
 * @param string   $access_token  - bearer access token from Auth0 (not used in implicit flow)
 * @param string   $refresh_token - refresh token from Auth0 (not used in implicit flow)
 */
function auth0_theme_hook_auth0_user_login( $user_id, $userinfo, $is_new, $id_token, $access_token, $refresh_token ) {
	echo '<strong>WP user ID</strong>:<br>' . $user_id . '<hr>';
	echo '<strong>Auth0 user info</strong>:<br><pre>' . print_r( $userinfo, true ) . '</pre><hr>';
	echo '<strong>Added to WP DB?</strong>:<br>' . ( $is_new ? 'yep' : 'nope' ) . '<hr>';
	echo '<strong>ID Token</strong>:<br>' . ( $id_token ? $id_token : 'not provided' ) . '<hr>';
	echo '<strong>Access Token</strong>:<br>' . ( $access_token ? $access_token : 'not provided' ) . '<hr>';
	echo '<strong>Refresh Token</strong>:<br>' . ( $refresh_token ? $refresh_token : 'not provided' ) . '<hr>';
	wp_die( 'Login successful! <a href="' . home_url() . '">Home</a>' );
}
// add_action( 'auth0_user_login', 'auth0_theme_hook_auth0_user_login', 10, 6 );
/**
 * Stop the login process after a new user has been created.
 * NOTE: The example below will break the user login process.
 *
 * @see WP_Auth0_Users::create_user()
 *
 * @param integer $user_id  - WordPress user ID for created user
 * @param string  $email    - email address for created user
 * @param string  $password - password used for created user
 * @param string  $f_name   - first name for created user
 * @param string  $l_name   - last name for created user
 */
function auth0_theme_hook_wpa0_user_created( $user_id, $email, $password, $f_name, $l_name ) {
	echo '<strong>User ID</strong>:<br>' . $user_id . '<hr>';
	echo '<strong>Email</strong>:<br>' . $email . '<hr>';
	echo '<strong>Password</strong>:<br>' . $password . '<hr>';
	echo '<strong>First name</strong>:<br>' . $f_name . '<hr>';
	echo '<strong>Last name</strong>:<br>' . $l_name . '<hr>';
	wp_die( 'User created!' );
}
// add_action( 'wpa0_user_created', 'auth0_theme_hook_wpa0_user_created', 10, 5 );
