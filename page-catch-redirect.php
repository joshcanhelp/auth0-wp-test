<?php
/**
 * Page template for testing auth redirection.
 * Loading this page while logged out should redirect to wp-login.php, then back to this page.
 * Page slug should be 'test-auth-redirect' for this template to be active.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-page
 *
 * @package WordPress
 * @subpackage WP_Auth0_Testing
 * @since 1.0
 * @version 1.0
 */

if ( ! is_user_logged_in() ) {
	auth_redirect();
}

get_template_part( 'template-parts/block', 'header' );
the_post();
?>

	<article>
	    <?php get_template_part( 'template-parts/block', 'test-tpl-header' ); ?>

		<div class="the-content">
			<pre>
				<?php var_dump( wp_get_current_user() ); ?>
		    </pre>
		</div>

	</article>

<?php get_template_part( 'template-parts/block', 'footer' ); ?>
