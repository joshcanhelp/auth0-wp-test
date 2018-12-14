<?php
/**
 * Page template for viewing WP-Auth0 settings
 * Page slug should be 'test-settings' for this template to be active.
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
ksort( $all_opts );
?>

<article>
	<?php get_template_part( 'template-parts/block', 'test-tpl-header' ); ?>

    <div class="the-content">
		<?php if ( ! class_exists( 'WP_Auth0_Options' ) ) : ?>
            <div class="alert alert-warning"><strong>Auth0 is not installed.</strong></div>
		<?php elseif ( ! current_user_can( 'manage_options' ) ) : ?>
            <div class="alert alert-warning"><strong>User is not authorized.</strong></div>
		<?php else : ?>
			<?php wp_a0_opts(); ?>
		<?php endif; ?>
    </div>
</article>

<?php get_template_part( 'template-parts/block', 'footer' ); ?>
