<?php
/**
 * Page template for viewing the current user data.
 * Page slug should be 'test-user' for this template to be active.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-page
 *
 * @package WordPress
 * @subpackage WP_Auth0_Testing
 * @since 1.0
 * @version 1.0
 */

get_template_part( 'template-parts/block', 'header' );
the_post();
$opts     = WP_Auth0_Options::Instance();
$all_opts = $opts->get_options();
?>

	<article>
	<?php get_template_part( 'template-parts/block', 'test-tpl-header' ); ?>
		<div class="the-content">
	<?php
	$current_uid = get_current_user_id();
	if ( $current_uid ) :
		?>
	 <h2>WP user</h2>
	 <pre><?php var_dump( get_user_by( 'id', $current_uid )->data ); ?></pre>
		<?php if ( function_exists( 'get_auth0userinfo' ) && $auth0_user = get_auth0userinfo( $current_uid ) ) : ?>
					<h2>Stored Auth0 user</h2>
					<pre><?php var_dump( $auth0_user ); ?></pre>
				<?php else : ?>
					<div class="alert alert-warning"><strong>No Auth0 user</strong></div>
				<?php endif; ?>
	<?php else : ?>
				<div class="alert alert-warning"><strong>No current WP session</strong></div>
	<?php endif; ?>
		</div>
	</article>

<?php get_template_part( 'template-parts/block', 'footer' ); ?>
