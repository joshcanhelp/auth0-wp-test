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
 * Adds an Auth0 user when a new customer is created in WooCommerce.
 * Make sure to change $payload['connection'] to the database connection you're using.
 *
 * @param integer $customer_id - WordPress user ID for the customer.
 * @param array   $new_customer_data - data used to create a new WordPress user.
 * @param string  $password_generated - auto-generated password.
 *
 * @link https://docs.woocommerce.com/wc-apidocs/source-function-wc_create_new_customer.html#114
 */
function auth0_wp_test_hook_woocommerce_created_customer( $customer_id, $new_customer_data ) {
	$a0_options     = WP_Auth0_Options::Instance();
	$payload        = array(
		'client_id'  => $a0_options->get( 'client_id' ),
		'email'      => $new_customer_data['user_email'],
		'password'   => $new_customer_data['user_pass'],
		'connection' => 'Username-Password-Authentication',
	);
	$new_auth0_user = WP_Auth0_Api_Client::signup_user( $a0_options->get( 'domain' ), $payload );
	if ( $new_auth0_user ) {
		$new_auth0_user->sub = 'auth0|' . $new_auth0_user->_id;
		unset( $new_auth0_user->_id );
		$user_repo = new WP_Auth0_UsersRepo( $a0_options );
		$user_repo->update_auth0_object( $customer_id, $new_auth0_user );
	}
}
// add_action( 'woocommerce_created_customer', 'auth0_wp_test_hook_woocommerce_created_customer', 10, 2 );

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
