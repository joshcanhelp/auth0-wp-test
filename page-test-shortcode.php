<?php
/**
 * Page template for testing [auth0] shortcode.
 * Page slug should be 'test-shortcode' for this template to be active.
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
?>

	<article>
	<?php get_template_part( 'template-parts/block', 'test-tpl-header' ); ?>

		<div class="the-content">

		<?php get_template_part( 'template-parts/block', 'lock-mods' ); ?>
		<?php
		$shortcode = '[auth0';
		foreach ( auth0_theme_get_lock_mod_opts() as $attr ) {
			if ( isset( $_GET[ $attr ] ) ) {
				$shortcode .= sprintf( ' %s="%s"', $attr, urldecode( $_GET[ $attr ] ) );
			}
		}
		$shortcode .= ']';
		echo do_shortcode( $shortcode );
		?>
		</div>
	</article>

<?php get_template_part( 'template-parts/block', 'footer' ); ?>
