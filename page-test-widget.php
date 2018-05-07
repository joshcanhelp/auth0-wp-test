<?php
/**
 * Page template for testing the login widget.
 * Page slug should be 'test-widget' for this template to be active.
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
$opts = WP_Auth0_Options::Instance();
?>

	<article>
	<?php get_template_part( 'template-parts/block', 'test-tpl-header' ); ?>

		<div class="the-content">
		<?php get_template_part( 'template-parts/block', 'lock-mods' ); ?>
		<?php
		$widget_opts = [];
		foreach ( auth0_theme_get_lock_mod_opts() as $attr ) {
			if ( isset( $_GET[ $attr ] ) ) {
				$widget_opts[ $attr ] = rawurldecode( $_GET[ $attr ] );
			}
		}
		the_widget(
			'WP_Auth0_Embed_Widget',
			$widget_opts
		);
		?>
		</div>

	</article>

<?php get_template_part( 'template-parts/block', 'footer' ); ?>
