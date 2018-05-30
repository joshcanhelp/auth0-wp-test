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
			<?php if ( ! is_user_logged_in() ) : ?>
				<p>If you see this, something is wrong ...</p>
			<?php else : ?>
				<p>If you see this, you should be logged in.</p>
			<?php endif; ?>
			<p>If you see this, it worked!</p>
		</div>

	</article>

<?php get_template_part( 'template-parts/block', 'footer' ); ?>
