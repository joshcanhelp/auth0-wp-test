<?php
/**
 * Sample hooks for non-Auth0 actions and filters.
 *
 * @package auth0-wp-test
 *
 * @link https://auth0.com/docs/cms/wordpress/extending#filters
 */

/**
 * Append a cache-breaking parameter to login_url redirects.
 * This can solve issues with cached authentication redirects and aggressive page caching.
 *
 * @param string $login_url - original login URL.
 * @param string $redirect - where to redirect after successful login.
 *
 * @return string
 */
function auth0_wp_test_hook_login_url( $login_url, $redirect ) {
	if ( ! empty( $redirect ) ) {
		$login_url = remove_query_arg( 'redirect_to', $login_url );
		$redirect  = add_query_arg( 'logged_in', 1, $redirect );
		$redirect  = rawurlencode( $redirect );
		$login_url = add_query_arg( 'redirect_to', $redirect, $login_url );
	}

	return $login_url;
}
// add_filter( 'login_url', 'auth0_wp_test_hook_login_url', 10, 2 );

/**
 * Adds an Auth0 user when a new user is created in the WP-Admin.
 * This will NOT add a new user when one is registered via the WP form (Auth0 should handle that).
 * Make sure to change $payload['connection'] to the database connection you're using.
 *
 * @param int|WP_Error $wp_user_id ID of the newly created user.
 *
 * @return void|WP_Error
 */
function auth0_wp_test_hook_create_auth0_user_from_wp_admin( $wp_user_id ) {

	// WordPress user was not created so do not proceed.
	if ( is_wp_error( $wp_user_id ) ) {
		return;
	}

	$a0_options = WP_Auth0_Options::Instance();
	$payload    = array(
		'client_id'  => $a0_options->get( 'client_id' ),
		// This is run during a POST request to create the user so pull the data from global.
		'email'      => $_POST['email'],
		'password'   => $_POST['pass1'],
		// Make sure this Database Connection is correct for your Auth0 configuration.
		'connection' => 'Username-Password-Authentication',
	);

	$new_auth0_user = WP_Auth0_Api_Client::signup_user( $a0_options->get( 'domain' ), $payload );

	// Returns false and logs an error in the plugin if this fails.
	// The WP user was still created but the Auth0 was not.
	if ( ! $new_auth0_user ) {
		return;
	}

	// Auth0 user created; now update the usermeta to connect the two accounts.
	$new_auth0_user->sub = 'auth0|' . $new_auth0_user->_id;
	unset( $new_auth0_user->_id );
	$user_repo = new WP_Auth0_UsersRepo( $a0_options );
	$user_repo->update_auth0_object( $wp_user_id, $new_auth0_user );
}
// add_action( 'edit_user_created_user', 'auth0_wp_test_hook_create_auth0_user_from_wp_admin', 10 );

/**
 * Play nicely with Restricted Site Access.
 *
 * @param bool $is_restricted - Original $is_restricted value
 * @param WP   $wp - WP object.
 *
 * @return mixed
 */
function auth0_wp_test_restricted_site_access_is_restricted( $is_restricted, $wp ) {
	if (
		! empty( $wp->query_vars['auth0'] )
		&& empty( $wp->query_vars['page'] )
		&& $_COOKIE['auth0_state'] === $wp->query_vars['state']
	) {
		return false;
	}
	return $is_restricted;
}
// add_filter( 'restricted_site_access_is_restricted', 'auth0_wp_test_restricted_site_access_is_restricted', 100, 2 );

/**
 * Redirect a user without a WordPress session to log in.
 */
function auth0_wp_test_redirect_for_auth() {
	if ( is_user_logged_in() ) {
		// User is logged in, nothing to do.
		return;
	}

	if ( 'page-template-that-needs-auth.php' === get_page_template_slug() ) {
		// User is trying to access a page template that requires authentication.
		auth_redirect();
	}

	if ( 'post' === get_post_type() ) {
		// User is trying to access a post type that requires authentication.
		auth_redirect();
	}

	return;
}
// add_action( 'template_redirect', 'auth0_wp_test_redirect_for_auth', 1 );
