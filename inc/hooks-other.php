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
function auth0_theme_hook_login_url( $login_url, $redirect ) {
	if ( ! empty( $redirect ) ) {
		$login_url = remove_query_arg( 'redirect_to', $login_url );
		$redirect  = add_query_arg( 'logged_in', 1, $redirect );
		$redirect  = rawurlencode( $redirect );
		$login_url = add_query_arg( 'redirect_to', $redirect, $login_url );
	}

	return $login_url;
}
// add_filter( 'login_url', 'auth0_theme_hook_login_url', 10, 2 );

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
function auth0_theme_hook_woocommerce_created_customer( $customer_id, $new_customer_data, $password_generated ) {
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
// add_action( 'woocommerce_created_customer', 'auth0_theme_hook_woocommerce_created_customer', 10, 3 );

/**
 * Play nicely with Restricted Site Access.
 *
 * @param bool $is_restricted - Original $is_restricted value
 * @param WP $wp - WP object.
 *
 * @return mixed
 */
function auth0_mu_hook_restricted_site_access_is_restricted( $is_restricted, $wp ) {
	if (
		! empty( $wp->query_vars['auth0'] )
		&& empty( $wp->query_vars['page'] )
		&& $_COOKIE['auth0_state'] === $wp->query_vars['state']
	) {
		return false;
	}
	return $is_restricted;
}
//add_filter( 'restricted_site_access_is_restricted', 'auth0_mu_hook_restricted_site_access_is_restricted', 100, 2 );
