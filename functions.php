<?php
/**
 * Main bootstrap file for this theme. Notes for usage:
 *
 * - This file is used for theme setup, file includes, and constant definition for the theme
 * - All functions defined here should be tied to the init, after_theme_setup, or activation hook
 * - Additional function definitions should go in a required file
 * - All relative path mentions in comments are relative to the theme root (where this file is)
 *
 * @package    WordPress
 * @subpackage WPAuth0Test
 */

if ( WP_DEBUG ) {
	// phpcs:ignore
	ini_set( 'display_errors', 1 );
	// phpcs:ignore
	ini_set( 'display_startup_errors', 1 );
	// phpcs:ignore
	error_reporting( E_ALL );
}

/**
 * Do not allow this file to be loaded directly
 */
if ( ! function_exists( 'add_action' ) ) {
	die( 'Nothing to do...' );
}

/**
 * Constants
 */
define( 'AUTH0_THEME_ROOT', dirname( __FILE__ ) );

/**
 * Sample actions and filters ... activate and have fun!
 */
require 'inc/hooks-other.php';
require 'inc/hooks-core-actions.php';
require 'inc/hooks-core-filters.php';
require 'inc/debug.php';
require 'inc/wp-cli.php';

/**
 * Init hook actions for the theme.
 * Actions/filters that go here instead of the after_theme_setup hook might be:
 *
 * - Actions that override ones in a particular plugin
 * - Actions that need to run after plugin init actions
 */
function auth0_theme_hook_init() {
  // Allow excepts and post thumbnails on pages.
  add_post_type_support( 'page', array( 'excerpt', 'thumbnail' ) );
}
add_action( 'init', 'auth0_theme_hook_init', 100 );

/**
 * Add theme-specific functionality.
 *
 * "This is the first action hook available to themes, triggered immediately after the active theme's
 * functions.php file is loaded."
 *
 * @link http://codex.wordpress.org/Plugin_API/Action_Reference
 *
 * "This hook is called during each page load, after the theme is initialized.
 * It is generally used to perform basic setup and registration actions for a theme."
 *
 * @link http://codex.wordpress.org/Plugin_API/Action_Reference/after_setup_theme
 *
 * @link https://codex.wordpress.org/Function_Reference/add_theme_support#more-information
 */
function auth0_theme_hook_after_setup_theme() {
  // Let WordPress manage the meta title tag.
	add_theme_support( 'title-tag' );

	// Support thumbnails.
	add_theme_support( 'post-thumbnails' );

	// HTML5 everywhere.
	add_theme_support(
		'html5', array(
			'search-form',
			'comment-form',
			'comment-list',
			'gallery',
			'caption',
		)
	);
}
add_action( 'after_setup_theme', 'auth0_theme_hook_after_setup_theme' );

/**
 * Queuing up CSS for the front-end.
 *
 * @see https://codex.wordpress.org/Function_Reference/wp_enqueue_style
 */
function auth0_theme_wp_enqueue_scripts() {
	wp_enqueue_style( 'auth0-core', '//cdn.auth0.com/styleguide/core/2.0.5/core.min.css' );
	wp_enqueue_style( 'auth0-comp', '//cdn.auth0.com/styleguide/components/2.0.0/components.min.css' );
	wp_enqueue_style( 'auth0-test', get_stylesheet_directory_uri() . '/assets/css/main.css' );
}
add_action( 'wp_enqueue_scripts', 'auth0_theme_wp_enqueue_scripts' );

/**
 * Queuing up CSS for wp-login.
 *
 * @see https://codex.wordpress.org/Function_Reference/wp_enqueue_style
 */
function auth0_theme_login_enqueue_scripts() {
	wp_enqueue_style( 'auth0-login', get_stylesheet_directory_uri() . '/assets/css/wp-login.css' );
}
add_action( 'login_enqueue_scripts', 'auth0_theme_login_enqueue_scripts' );

/**
 * Register widget areas and update sidebar with default widgets.
 */
function auth0_theme_hook_widgets_init() {
	register_sidebar(
		array(
			'name'          => 'Sidebar',
			'id'            => 'sidebar-1',
			'before_widget' => '<aside id="%1$s" class="widget %2$s">',
			'after_widget'  => '</aside>',
			'before_title'  => '<h6 class="widget-title">',
			'after_title'   => '</h6>',
			'fallback'      => false,
		)
	);
	add_filter( 'widget_text', 'shortcode_unautop' );
	add_filter( 'widget_text', 'do_shortcode' );
}

add_action( 'widgets_init', 'auth0_theme_hook_widgets_init' );

/**
 * Get test page slugs
 *
 * @return array
 */
function auth0_theme_get_test_page_slugs() {
	return [ 'settings', 'client', 'user', 'avatars', 'shortcode', 'widget', 'auth-redirect' ];
}

/**
 * Get Lock modification options for shortcodes and widgets
 *
 * @return array
 */
function auth0_theme_get_lock_mod_opts() {
	return [
		'custom_css',
		'custom_js',
		'dict',
		'extra_conf',
		'form_title',
		'gravatar',
		'icon_url',
		'modal_trigger_name',
		'redirect_to',
		'show_as_modal',
		'social_big_buttons',
	];
}
